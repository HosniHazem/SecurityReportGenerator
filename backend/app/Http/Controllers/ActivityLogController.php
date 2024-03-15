<?php

namespace App\Http\Controllers;

use App\ActivityLog;
use App\Models\ActivityLog as ModelsActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Retrieve all activity logs
        $activityLogs = ModelsActivityLog::all();

        return response()->json(['activity_logs' => $activityLogs,'status'=>200]);
    }

}
