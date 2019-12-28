<?php

namespace App\Http\Controllers\Admin;
use App\UploadQueue;

/**
 * Class UploadsController
 * @package App\Http\Controllers\Admin
 */
class UploadsController extends Controller
{
    public function errors()
    {
		$pageTitle = "Upload Errors";

		$db = UploadQueue::where('status', 'ERROR');
		$db->orderBy('created_at','desc');
		
		$errors = $db->paginate(20);
		
		return view('Admin/uploads/errors', compact('pageTitle','limit','errors'));
	}

    public function tasks()
    {
        $pageTitle = "Upload Tasks";

        $db = UploadQueue::whereIn('status', ['QUEUED','IN PROGRESS', 'ERROR', 'COMPLETE']);
        $db->orderBy('created_at','desc');

        $tasks = $db->paginate(20);

        return view('Admin/uploads/tasks', compact('pageTitle','limit','tasks'));
    }
}
