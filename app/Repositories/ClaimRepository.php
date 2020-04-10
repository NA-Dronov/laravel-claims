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
     * Get model for showing
     * 
     * @param int $id
     * 
     * @return Model
     * 
     */
    public function getById($id)
    {
        return $this->startCondition()->with(['user:user_id,name', 'manager:user_id,name', 'responses' => function ($query) {
            $query->orderBy('created_at', 'DESC');
        }, 'responses.author', 'files', 'responses.files', 'responses.author', 'responses.author.roles', 'claim_status'])->find($id);
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
            'm.name as manager_name',
        ];

        $builder = $this
            ->startCondition()
            ->leftjoin('users as m', function ($join) {
                $join->on('claims.manager_id', '=', 'm.user_id');
            });
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        if (!$user->hasRole('manager')) {
            $builder->where('claims.user_id', $user->user_id);
        }

        #region SEARCH
        if (!empty($search['status'])) {
            $builder->where('claims.status', '=', $search['status']);
        }

        if ($user->hasRole('manager')) {
            if (isset($search['viewed'])) {

                if ($search['viewed']) {
                    $builder->join('claim_user_relations as was_viewed_relations', function ($join) {
                        $join->on('claims.claim_id', '=', 'was_viewed_relations.claim_id')
                            ->where('was_viewed_relations.relation_type', '=', 'V');
                    });
                } else {
                    $builder->leftjoin('claim_user_relations as was_viewed_relations', function ($join) {
                        $join->on('claims.claim_id', '=', 'was_viewed_relations.claim_id')
                            ->where('was_viewed_relations.relation_type', '=', 'V');
                    });

                    $builder->whereNull('was_viewed_relations.relation_type');
                }
            }

            if (isset($search['has_answer'])) {

                if ($search['has_answer']) {
                    $builder->join('claim_user_relations as has_new_responses_relations', function ($join) {
                        $join->on('claims.claim_id', '=', 'has_new_responses_relations.claim_id')
                            ->where('has_new_responses_relations.relation_type', '=', 'R');
                    });
                } else {
                    $builder->leftjoin('claim_user_relations as has_new_responses_relations', function ($join) {
                        $join->on('claims.claim_id', '=', 'has_new_responses_relations.claim_id')
                            ->where('has_new_responses_relations.relation_type', '=', 'R');
                    });

                    $builder->whereNull('has_new_responses_relations.relation_type');
                }
            }
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
        } elseif ($sorting['sort_by'] == 'manager') {
            $builder->orderBy('m.name', $sorting['sort_order']);
        } else {
            $builder->orderBy($sorting['sort_by'], $sorting['sort_order']);
        }
        #endregion

        $builder->selectRaw(implode(',', $columns))->with(['user:user_id,name', 'manager:user_id,name', 'claim_status:code,status'])->groupBy('claims.claim_id');

        $paginator = $builder->paginate(25);

        if (!empty($search)) {
            $paginator->appends($search);
        }

        if (!empty($sorting)) {
            $paginator->appends($sorting);
        }

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
