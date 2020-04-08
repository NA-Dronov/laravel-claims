<?php

namespace App\Repositories;

use App\Models\Claim as Model;
use App\Models\ClaimStatus;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Collection;

/**
 * 
 * Class BlogPostRepository
 * 
 * @package App\Repositories
 */
class ClaimRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * 
     * Get model for editing in admin panel
     * 
     * @param int $id
     * 
     * @return Model
     * 
     */
    public function getEdit($id)
    {
        dd(__METHOD__, $id);
    }

    /**
     * Get claims for output with paginator
     * 
     * @param int|null $perPage
     * 
     * @return (\Illuminate\Contracts\Pagination\LengthAwarePaginator&array&array)[]
     */
    public function getAllWithPaginate($params)
    {
        $search = Model::parseFilters($params);
        $sorting = Model::parseSorting($params);
        // TODO: user role based condition
        // $user = Auth::user();
        // if (!isset($user)) {
        //     return null;
        // }

        $columns = [
            'claims.claim_id',
            'claims.subject',
            'claims.user_id',
            'claims.manager_id',
            'claims.status',
            'claims.created_at',
            'COUNT(was_viewed_relations.claim_id) AS was_viewed',
            'COUNT(has_new_responses_relations.claim_id) AS has_new_responses',
        ];

        /**
         * TODO:
         * was_viewed_relations join must be included only for managers
         * all joins must have user condition
         * query must have user_id condition
         * add filters
         */
        $builder = $this
            ->startCondition()
            ->leftjoin('claim_user_relations as was_viewed_relations', function ($join) {
                $join->on('claims.claim_id', '=', 'was_viewed_relations.claim_id')
                    ->where('was_viewed_relations.relation_type', '=', 'V');
            })
            ->leftjoin('claim_user_relations as has_new_responses_relations', function ($join) {
                $join->on('claims.claim_id', '=', 'has_new_responses_relations.claim_id')
                    ->where('was_viewed_relations.relation_type', '=', 'R');
            });

        #region SEARCH
        if (!empty($search['status'])) {
            $builder->where('claims.status', '=', $search['status']);
        }

        if (isset($search['viewed'])) {
            $wasViewedCondition = 'COUNT(was_viewed_relations.claim_id) ' . ($search['viewed'] == true ? '>' : '=') . '0';
            $builder->havingRaw($wasViewedCondition);
        }
        #endregion

        #region SORT
        if ($sorting['sort_by'] == 'status') {
            $builder->leftjoin('claim_statuses as cs', function ($join) {
                $join->on('claims.status', '=', 'cs.code');
            });
            $builder->orderBy('cs.status', $sorting['sort_order']);
        } elseif ($sorting['sort_by'] == 'user') {
            $builder->leftjoin('users as u', function ($join) {
                $join->on('claims.user_id', '=', 'u.user_id');
            });
            $builder->orderBy('u.name', $sorting['sort_order']);
        } else {
            $builder->orderBy($sorting['sort_by'], $sorting['sort_order']);
        }
        #endregion

        $builder->selectRaw(implode(',', $columns))->with(['user:user_id,name', 'claim_status:code,status'])->groupBy('claims.claim_id');

        $paginator = $builder->paginate(25);

        if (!empty($search)) {
            $paginator->appends($search);
        }

        if (!empty($sorting)) {
            $paginator->appends($sorting);
        }

        // $result = $this
        //     ->startCondition()
        //     ->select($columns)
        //     ->leftjoin('claim_user_relations', 'audioassets.raga_id', '=', 'claim_user_relations.id')
        //     ->orderBy('created_at', 'DESC')
        //     ->with([
        //         'relations:claim_id,relation_type',
        //         'user:user_id,name'
        //     ])
        //     ->paginate(25);

        return compact('paginator', 'search', 'sorting');
    }

    /**
     * 
     * Get statuses list for output in dropdown list
     * 
     * @return array
     */
    public function getStatusesForCombobox()
    {
        $statuses = ClaimStatus::all(['code', 'status'])
            ->keyBy('code')
            ->map(function ($item, $key) {
                return $item['status'];
            })->toArray();
        return $statuses;
    }
}
