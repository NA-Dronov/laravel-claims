<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClaimCreateRequest;
use App\Models\Claim;
use App\Models\ClaimStatus;
use App\Models\File;
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
        $search = $request->only(['status', 'viewed', 'has_answer']);
        $search = array_filter($search, function ($filter) {
            return isset($filter) && $filter != "";
        });

        $paginator = $this->claimRepository->getAllWithPaginate($search);
        if (!empty($search)) {
            $paginator->appends($search);
        }

        $claimsStatuses = $this->claimRepository->getStatusesForCombobox();

        return view('claims.index', compact('paginator', 'claimsStatuses', 'search'));
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
        // TODO: remove after auth integration
        $data['user_id'] = 1;
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
    public function show(Claim $claim)
    {
        $files = $this->fileGateway->getAll($claim->claim_id, File::CLAIM)->toArray();
        return view('claims.show', ['item' => $claim, 'item_files' => $files]);
    }
}
