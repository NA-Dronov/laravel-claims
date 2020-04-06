<?php

namespace App\Http\Controllers;

use App\Models\Claim;
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
        dd(__METHOD__);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd(__METHOD__, $request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Claim  $claim
     * @return \Illuminate\Http\Response
     */
    public function show(Claim $claim)
    {
        dd(__METHOD__, $claim);
    }
}
