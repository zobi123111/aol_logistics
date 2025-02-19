<?php
namespace App\Http\Controllers;

use App\Models\UserActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Models\Log;
use Yajra\DataTables\DataTables;

class UserActivityLogController extends Controller
{
    public function showAll()
    {
        // Retrieve all activity logs
        $logs = UserActivityLog::with('user')->orderBy('id', 'desc')->get(); // Load related users for each log

        // Return the logs to a view
        return view('activity_logs.index', compact('logs'));
    }

    public function deleteLogs(Request $request)
    {
        $dateRange = $request->input('date_range'); // daily, weekly, monthly, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($dateRange == 'daily') {
            $date = Carbon::now()->subDay();
        } elseif ($dateRange == 'weekly') {
            $date = Carbon::now()->subWeek();
        } elseif ($dateRange == 'monthly') {
            $date = Carbon::now()->subMonth();
        } elseif ($dateRange == 'custom') {
            if (!$startDate && !$endDate) {
                return response()->json(['error' => 'Start date and end date are required.'], 422);
            }
            if (!$startDate) {
                return response()->json(['error' => 'Start date is required.'], 422);
            }
            if (!$endDate) {
                return response()->json(['error' => 'End date is required.'], 422);
            }
            if ($startDate > $endDate) {
                return response()->json(['error' => 'Start date cannot be later than the end date.'], 422);
            }
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            UserActivityLog::whereBetween('created_at', [$startDate, $endDate])->delete();
            Session::flash('message', 'Logs deleted successfully!');
            return response()->json(['message' => 'Logs deleted successfully!']);
        } else {
            return response()->json(['error' => 'Invalid selection'], 400);
        }
        Session::flash('message', 'Logs deleted successfully!');
        UserActivityLog::where('created_at', '<=', $date)->delete();

        return response()->json(['message' => 'Logs deleted successfully!']);
    }

    public function getLogs(Request $request)
{
    $logs = UserActivityLog::select(['id', 'log_type', 'description', 'created_at']);

    return DataTables::of($logs)->make(true);
}
}