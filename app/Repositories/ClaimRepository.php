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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
     */
    public function getAllWithPaginate($params)
    {
        // TODO: user role based condition
        // $user = Auth::user();
        // if (!isset($user)) {
        //     return null;
        // }

        $columns = [
            'claims.claim_id',
            'subject',
            'claims.user_id',
            'manager_id',
            'status',
            'created_at',
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
            })
            ->selectRaw(implode(',', $columns))
            ->with(['user:user_id,name', 'claim_status:code,status']);

        if (!empty($params['status'])) {
            $builder->where('status', '=', $params['status']);
        }

        if (isset($params['viewed'])) {
            $wasViewedCondition = 'COUNT(was_viewed_relations.claim_id) ' . ($params['viewed'] == true ? '>' : '=') . '0';
            $builder->havingRaw($wasViewedCondition);
        }

        $builder->orderBy('created_at', 'DESC')
            ->groupBy('claims.claim_id');


        $result = $builder->paginate(25);


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

        return $result;
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
