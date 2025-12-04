<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemUser;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Get authenticated user
        $user = Auth::user();
        
        // Get user statistics
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', 1)->count();
        $totalSystemUsers = SystemUser::count();
        $activeSystemUsers = SystemUser::where('is_active', 1)->count();
        
        // Get recent system users
        $recentSystemUsers = SystemUser::with(['user', 'user.userType'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', [
            'user' => $user,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'totalSystemUsers' => $totalSystemUsers,
            'activeSystemUsers' => $activeSystemUsers,
            'recentSystemUsers' => $recentSystemUsers
        ]);
    }
}