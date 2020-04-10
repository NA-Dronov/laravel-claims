<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClaimCreateRequest;
use App\Http\Requests\ResponseCreateRequest;
use App\Models\Claim;
use App\Models\ClaimStatus;
use App\Models\File;
use App\Models\Response;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ClaimResponseNotification;
use App\Notifications\ClaimStatusNotification;
use App\Repositories\ClaimRepository;
use App\Repositories\FileBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ClaimController extends Controller
{
    /**
     * @var ClaimRepository
     */
    private $claimRepository;

    /**
     * @var FileBroker
     */
    private $fileBroker;

    public function __construct(ClaimRepository $claimRepository, FileBroker $fileBroker)
    {
        $this->middleware(['can:create_claim', 'claim.time'])->only(['create', 'store']);
        $this->middleware('can:assign_claim')->only(['assign']);
        $this->middleware('claim.check_permission')->only(['response', 'show', 'close']);
        $this->middleware('claim.check_status')->only(['response', 'assign']);

        $this->claimRepository = $claimRepository;
        $this->fileBroker = $fileBroker;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $request->input();

        extract($this->claimRepository->getAllWithPaginate($data));

        $claimsStatuses = $this->claimRepository->getStatusesForCombobox();

        /**
         * @var \App\Models\User $manager
         */
        $manager = auth()->user();

        $managerMode = $manager->hasRole('manager');

        return view('claims.index', compact('paginator', 'claimsStatuses', 'search', 'sorting', 'managerMode'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item = Claim::make();

        return view('claims.create', compact('item'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClaimCreateRequest $request)
    {
        $data = $request->input();

        $data['user_id'] = auth()->user()->user_id;
        $data['status'] = ClaimStatus::OPEN;

        // Create object and add to database
        $item = Claim::create($data);

        if ($item) {

            $managers = Role::whereName('super')->first()->users;

            if ($managers->isNotEmpty()) {
                Notification::send($managers, new ClaimStatusNotification($item));
            }

            $files = $request->allFiles();

            if (!empty($files['attachments'])) {
                $this->fileBroker->store($files['attachments'], $item->claim_id, File::CLAIM);
            }

            return redirect()->route('claims.show', [$item->claim_id])
                ->with(['success']);
        } else {
            return back()->withErrors(['msg' => 'Ошибка сохранения'])
                ->withInput();
        }
    }

    /**
     * Store a newly created response in storage.
     *
     * @param  \App\Models\Claim  $request
     * @param  \Illuminate\Http\ResponseCreateRequest  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function response(Claim $claim, ResponseCreateRequest $request)
    {
        $data = $request->input();

        $respondent = auth()->user();

        $data['user_id'] = $respondent->user_id;
        $data['claim_id'] = $claim->claim_id;

        // Create object and add to database
        /**
         * @var \App\Models\Response $item
         */
        $item = Response::create($data);

        if ($item) {

            $files = $request->allFiles();

            if (!empty($files['attachments'])) {
                $this->fileBroker->store($files['attachments'], $item->response_id, File::RESPONSE);
            }

            $recipient = null;

            // If respondent is assigned manager then send to claim owner
            if ($respondent->user_id == $claim->manager_id) {
                $recipient = $claim->user;
                // Otherwise send to assigned manager if claim has one
            } elseif (null !== ($manager = $claim->manager)) {
                $recipient = $manager;
            }

            if (isset($recipient)) {
                Notification::send($recipient, new ClaimResponseNotification($item));
            }

            return redirect()->route('claims.show', [$item->claim_id])
                ->with(['success' => 'Успех', 'active' => 'responses']);
        } else {
            return back()->withErrors(['msg' => 'Ошибка сохранения'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Claim  $claim
     * @return \Illuminate\Http\Response
     */
    public function show(Claim $claim)
    {
        $item = $claim;

        $new_reponse = Response::make();
        return view('claims.show', compact('item', 'new_reponse'));
    }

    public function assign(Claim $claim, User $user)
    {
        $claim->update(['manager_id' => $user->user_id, 'status' => ClaimStatus::PROCESSED]);

        Notification::send($claim->user, new ClaimStatusNotification($claim));

        return redirect()->route('claims.show', [$claim->claim_id])
            ->with(['success']);
    }

    public function close(Claim $claim)
    {
        $claim->update(['status' => ClaimStatus::CLOSED]);

        if (null !== ($recipient = $claim->manager)) {
            Notification::send($recipient, new ClaimStatusNotification($claim));
        }

        return redirect()->route('claims.show', [$claim->claim_id])
            ->with(['success']);
    }
}
