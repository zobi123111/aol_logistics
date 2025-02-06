<?php
namespace App\Http\Controllers;

use App\Models\UserActivityLog;
use App\Models\User;

class UserActivityLogController extends Controller
{
    public function showAll()
    {
        // Retrieve all activity logs
        $logs = UserActivityLog::with('user')->orderBy('id', 'desc')->get(); // Load related users for each log

        // Return the logs to a view
        return view('activity_logs.index', compact('logs'));
    }
}