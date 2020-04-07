<?php

namespace App\Repositories;

use App\Models\File as Model;
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
class FileGateway extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * Get file information
     * 
     * @param int $file_id
     * 
     * @return Model
     */
    public function get($file_id)
    {
        // TODO: user role based condition
        // $user = Auth::user();
        // if (!isset($user)) {
        //     return null;
        // }

        $result = $this->startCondition()->find($file_id);

        return $result;
    }

    /**
     * Get files for output
     * 
     * @param int $id object id that file belongs to
     * @param string $type of object
     * 
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
     */
    public function getAll($id, $type)
    {
        // TODO: user role based condition
        // $user = Auth::user();
        // if (!isset($user)) {
        //     return null;
        // }
        $columns = ['file_id', 'object_id', 'object_type', 'original_name', 'stored_name'];

        $result = $this
            ->startCondition()
            ->select($columns)
            ->where('object_id', '=', $id)
            ->where('object_type', '=', $type)
            ->get();

        return $result;
    }

    public function store($files, $object_id, $object_type)
    {
        $result_ids = [];

        foreach ($files as $key => $file) {
            $result = $file->store('attachments');

            if ($result) {
                $data = [
                    'object_id' => $object_id,
                    'object_type' => $object_type,
                    'original_name' => $file->getClientOriginalName(),
                    'stored_name' => $result
                ];

                $result_ids[] = Model::create($data)->file_id;
            }
        }

        return $result_ids;
    }
}
