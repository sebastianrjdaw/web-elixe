<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SyncRun;
use Inertia\Inertia;
use Inertia\Response;

class SyncRunController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/SyncRuns', [
            'runs' => SyncRun::latest('started_at')->paginate(30)->through(fn (SyncRun $run) => [
                'id' => $run->id,
                'source' => $run->source,
                'status' => $run->status,
                'startedAt' => $run->started_at?->toDateTimeString(),
                'finishedAt' => $run->finished_at?->toDateTimeString(),
                'recordsFound' => $run->records_found,
                'recordsCreated' => $run->records_created,
                'recordsUpdated' => $run->records_updated,
                'recordsSkipped' => $run->records_skipped,
                'errorMessage' => $run->error_message,
                'triggeredByUserId' => $run->triggered_by_user_id,
            ]),
        ]);
    }
}
