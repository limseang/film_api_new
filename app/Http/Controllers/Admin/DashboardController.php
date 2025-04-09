<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirebaseRealTimeService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;

class DashboardController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseRealTimeService $firebaseService)
    {
        $this->middleware('lang');
        $this->firebaseService = $firebaseService;
    }
    
    public function index()
    {
        $data['bc'] = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => '#', 'page' => __('global.dashboard')]];
        
        // Get income/expense report data for dashboard
        $data['incomeExpenseSummary'] = \App\Models\ReportIncomeExpense::select('type', 'currency', \DB::raw('SUM(amount) as total'))
            ->groupBy('type', 'currency')
            ->get();
            
        // Get monthly report data for the current year
        $currentYear = date('Y');
        $monthlyData = \App\Models\ReportIncomeExpense::selectRaw('MONTH(date_at) as month, type, currency, SUM(amount) as total')
            ->whereYear('date_at', $currentYear)
            ->groupBy('month', 'type', 'currency')
            ->orderBy('month')
            ->get();
            
        $data['monthlyReport'] = $monthlyData;
        
        // Get online users from Firebase
        $onlineUsers = $this->firebaseService->getOnlineUsers();
        $data['onlineUsersCount'] = count($onlineUsers);
        
        // Get user details for online users
        $onlineUserIds = array_keys($onlineUsers);
        $data['onlineUserDetails'] = [];
        
        if (!empty($onlineUserIds)) {
            // Get user details from database for the online users
            $userDetails = User::whereIn('id', $onlineUserIds)
                ->select('id', 'name', 'email', 'avatar', 'role_id')
                ->get()
                ->keyBy('id');
                
            // Merge with the online status data
            foreach ($onlineUsers as $userId => $statusData) {
                if (isset($userDetails[$userId])) {
                    $data['onlineUserDetails'][] = [
                        'user' => $userDetails[$userId],
                        'status' => $statusData,
                        'last_active_time' => isset($statusData['last_active']) 
                            ? date('Y-m-d H:i:s', $statusData['last_active']) 
                            : null
                    ];
                }
            }
        }
        
        // Get total registered users count
        $data['totalUsersCount'] = User::count();
        
        // Get active users in the last 30 days
        $thirtyDaysAgo = \Carbon\Carbon::now()->subDays(30);
        $data['activeUsersCount'] = UserLogin::where('created_at', '>=', $thirtyDaysAgo)
            ->distinct('user_id')
            ->count('user_id');
        
        return view('dashboard', $data);
    }
    
    /**
     * Manually update current admin user status to online
     */
    public function updateOnlineStatus()
    {
        $user = auth()->user();
        
        if ($user) {
            $this->firebaseService->updateUserStatus($user->id, true, [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role_id,
                'is_admin' => true
            ]);
        }
        
        return response()->json(['success' => true]);
    }
}
