<?php 
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use App\Models\Page;
use Illuminate\Support\Str;
use App\Models\EmailJob;
use App\Models\EmailType;

function encode_id($id)
{
    $hashids = new Hashids(env('HASHIDS_SALT'), 8);
    return $hashids->encode($id);
}

function decode_id($hashedId)
{
    $hashids = new Hashids(env('HASHIDS_SALT'), 8);
    $decoded = $hashids->decode($hashedId);
    return !empty($decoded) ? $decoded[0] : null;
}

 function hasPermission($module)
{
    return auth()->user()->roledata->rolePermissions->contains('module',$module);
}


function getAllowedPages()
{
    $user = Auth::user();

    if (!$user || !$user->role) {
        return collect(); 
    }

    if ($user->is_owner || $user->is_dev) {
        return Page::with('modules')->orderBy('position', 'asc')->get();
    }

    $dashboardPage = Page::with('modules')->whereHas('modules', function ($query) {
        $query->where('route_name', 'dashboard');
    })->first(); 
    
    $allowedPages = Page::with('modules')
        ->orderBy('position', 'asc')
        ->whereHas('modules', function ($query) use ($user) {
            $query->whereHas('rolePermissions', function ($subQuery) use ($user) {
                $subQuery->where('role_id', $user->role);
            });
        })
        ->where(function ($query) {
            $query->whereRaw("route_name NOT LIKE '%roles%'");
        })
        ->get();
    
    // Merge the Dashboard page with the allowed pages (if it's not already included)
    if ($dashboardPage && !$allowedPages->contains('id', $dashboardPage->id)) {
        $allowedPages->prepend($dashboardPage);
    }

    return $allowedPages;
}

function checkAllowedModule($pageRoute, $routeName = null)
{
    $user = Auth::user();
    if (!$user || !$user->role) {
        return collect(); 
    }

    if ($user->is_owner || $user->is_dev) {
        return Page::where('route_name', $pageRoute)
            ->with('modules')
            ->orderBy('position', 'asc')
            ->get();
    }
    return Page::where('route_name', $pageRoute)->with(['modules' => function ($query) use ($routeName) {
        if ($routeName) {
            $query->where('route_name', $routeName);
        }
    }])
    ->orderBy('position', 'asc')
    ->whereHas('modules', function ($query) use ($user, $routeName) {
        $query->whereHas('rolePermissions', function ($subQuery) use ($user) {
            $subQuery->where('role_id', $user->role);
        });

        if ($routeName) {
            $query->where('route_name', $routeName);
        }
    })->get();
}

function isAdminUser()
{
    $user = auth()->user();
    
    if (!$user) {
        return false;
    }
    return ($user->is_owner == 1 || $user->is_dev == 1);
}

function isAolAdminUser()
{
    $user = auth()->user();
    
    if (!$user) {
        return false;
    }
    return ($user->role === 'aol_admin' || $user->is_owner == 1 || $user->is_dev == 1);
}

function isAolUser()
{
    $user = auth()->user();
    
    if (!$user) {
        return false;
    }

    return ($user->roledata->user_type_id == 1 || $user->is_owner == 1 || $user->is_dev == 1);
}

function isDev()
{
    $user = auth()->user();
    
    if (!$user) {
        return false;
    }

    return ($user->role === 'admin' || $user->is_dev == 1);
}

function isOwnerOrDev()
{
    $user = auth()->user();
    
    if (!$user) {
        return false;
    }

    return ( $user->is_owner == 1 || $user->is_dev == 1);
}

function isSupplierUser()
{
    $user = auth()->user();
    
    if (!$user) {
        return false;
    }
    if (!$user || !$user->roledata || $user->roledata->user_type_id !== 3) {
        return false;
    }

    return optional($user->supplier)->id ?: false;
}

function isClientUser()
{
    $user = auth()->user();
    
    if (!$user) {
        return false;
    }
    if (!$user || !$user->roledata || $user->roledata->user_type_id !== 2) {
        return false;
    }
    return true;
    // return optional($user->supplier)->id ?: false;
}

function queueEmailJob(array $recipients, string $subject, string $template, array $payload = [], ?string $emailType = null): bool
{
    try {
        $emailJob = new EmailJob();
        $emailJob->to_email = $recipients; 
        $emailJob->subject = $subject;
        $emailJob->template = $template; 
        $emailJob->payload = $payload;
        $emailJob->email_type = $emailType;
        $emailJob->status = 'pending';
        $emailJob->save();

        return true;
    } catch (\Exception $e) {
        \Log::error('Failed to queue email job: ' . $e->getMessage());
        return false;
    }
}

if (!function_exists('isEmailTypeActive')) {
    function isEmailTypeActive(string $name): bool
    {
        return EmailType::where('name', $name)->where('is_active', 1)->exists();
    }
}
?>