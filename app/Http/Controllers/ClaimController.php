<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClaimCreateRequest;
use App\Models\Claim;
use App\Models\ClaimStatus;
use App\Repositories\ClaimRepository;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    /**
     * @var ClaimRepository
     */
    private $claimRepository;

    public function __construct(ClaimRepository $claimRepository)
    {
        $this->claimRepository = $claimRepository;
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
        return view('claims.show', ['item' => $claim]);
    }
}
