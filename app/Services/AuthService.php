<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class AuthService
{

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Check if user exists
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // Web request නම් back with error
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid credentials'
                    ], 401);
                }
                return back()->withErrors(['email' => 'Invalid credentials']);
            }

            // Verify password using Hash::check()
            if (!Hash::check($request->password, $user->password)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid credentials'
                    ], 401);
                }
                return back()->withErrors(['email' => 'Invalid credentials']);
            }

            // Check if user is active
            if (!$user->is_active) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Account is deactivated'
                    ], 401);
                }
                return back()->withErrors(['email' => 'Account is deactivated']);
            }

            // Create token for API (Mobile & Web)
            $token = $user->createToken('auth-token')->plainTextToken;

            // Get user with system user data
            $userWithDetails = User::with(['systemUser', 'userType'])
                ->where('id', $user->id)
                ->first();

            // For web - session regeneration
            if ($request->hasSession()) {
                Auth::login($user);
                $request->session()->regenerate();

                // Web request නම් dashboard එකට redirect කරන්න
                // Redirect to dashboard
                return redirect()->intended('/dashboard');
            }

            // API request නම් JSON response එක දෙන්න
            return response()->json([
                'status' => 'success',
                'message' => 'Logged in successfully',
                'token' => $token,
                'user' => $userWithDetails
            ]);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Login failed',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Login failed']);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Logout from session (for web)
            Auth::logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Web request නම් WELCOME PAGE එකට redirect කරන්න
                return redirect('/'); // මෙය '/login' වෙනුවට '/' කරන්න
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully'
            ]);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Logout failed',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Logout failed']);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user()->load(['systemUser', 'userType']);

            return response()->json([
                'status' => 'success',
                'user' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get user data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = $request->user()->load(['systemUser', 'userType']);

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'system_user' => $user->systemUser,
                'user_type' => $user->userType
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
