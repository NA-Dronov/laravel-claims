<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClaimCreateRequest;
use App\Models\Claim;
use App\Models\ClaimStatus;
use App\Models\File;
use App\Models\User;
use App\Repositories\ClaimRepository;
use App\Repositories\FileGateway;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    /**
     * @var ClaimRepository
     */
    private $claimRepository;

    /**
     * @var FileGateway
     */
    private $fileGateway;

    public function __construct(ClaimRepository $claimRepository, FileGateway $fileGateway)
    {
        $this->middleware('can:create_claim')->only(['create', 'store']);
        $this->middleware('can:assign_claim')->only(['assign']);

        $this->claimRepository = $claimRepository;
        $this->fileGateway = $fileGateway;
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

        $managerMode = auth()->user()->hasRole('manager');

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

            $files = $request->allFiles();

            if (!empty($files['attachments'])) {
                $this->fileGateway->store($files['attachments'], $item->claim_id, File::CLAIM);
            }

            return redirect()->route('claims.show', [$item->claim_id])
                ->with(['success']);
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
    public function show($id)
    {
        $item = $this->claimRepository->getById($id);

        if (empty($item)) {
            abort(404);
        }

        if (auth()->user()->user_id != $item->user_id && !auth()->user()->hasRole("manager")) {
            return redirect()->route('claims.index')
                ->withErrors(['msg' => 'У вас недостаточно прав']);
        }

        $item_files = $this->fileGateway->getAll($item->claim_id, File::CLAIM)->toArray();
        return view('claims.show', compact('item', 'item_files'));
    }

    public function assign(Claim $claim, User $user)
    {
        $claim->update(['manager_id' => $user->user_id]);

        return redirect()->route('claims.show', [$claim->claim_id])
            ->with(['success']);
    }

    public function close(Claim $claim)
    {
        $isCurrentUser = auth()->user()->user_id == $claim->user_id;
        $isManager = auth()->user()->hasRole("manager");
        if (!$isCurrentUser && !$isManager) {
            return back()->withErrors(['msg' => 'У вас недостаточно прав']);
        }

        $claim->update(['status' => ClaimStatus::CLOSED]);

        return redirect()->route('claims.show', [$claim->claim_id])
            ->with(['success']);
    }
}
