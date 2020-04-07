<?php

namespace App\Http\Controllers;

use App\Repositories\FileGateway;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * @var FileGateway
     */
    private $fileGateway;

    public function __construct(FileGateway $fileGateway)
    {
        $this->fileGateway = $fileGateway;
    }
    /**
     * Download the specified file.
     *
     * @param  \App\Claim  $claim
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $file = $this->fileGateway->get($id);

        if (empty($file)) {
            abort(404);
        }

        // TODO check user and his group
        // if (!auth()->check()) {
        //     return abort(404);
        // }
        return response()->download(storage_path('app/' . $file->stored_name));
    }
}
