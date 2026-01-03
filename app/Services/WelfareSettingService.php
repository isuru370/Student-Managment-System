<?php

namespace App\Services;

use App\Models\WelfareSetting;
use Illuminate\Support\Facades\Auth;

class WelfareSettingService
{
    /**
     * Fetch active welfare setting for the authenticated user
     */
    public function fetch(): ?WelfareSetting
    {
        return WelfareSetting::where('user_id', Auth::id())
            ->where('status', 1)
            ->first();
    }

    /**
     * Create new welfare setting
     */
    public function create(float $amount): WelfareSetting
    {
        // Deactivate any existing active setting for this user
        WelfareSetting::where('user_id', Auth::id())
            ->where('status', 1)
            ->update(['status' => 0]);
        
        // Create new setting
        return WelfareSetting::create([
            'user_id' => Auth::id(),
            'amount' => $amount,
            'status' => 1,
        ]);
    }

    /**
     * Delete welfare setting (set status = 0)
     */
    public function delete(int $id): bool
    {
        $setting = WelfareSetting::findOrFail($id);
        
        // Optional: Add authorization check
        // if ($setting->user_id !== Auth::id()) {
        //     abort(403, 'Unauthorized action.');
        // }
        
        $setting->status = 0;
        return $setting->save();
    }
    
    /**
     * Alternative: Delete current user's welfare setting
     */
    public function deleteCurrentUserSetting(): bool
    {
        $setting = WelfareSetting::where('user_id', Auth::id())
            ->where('status', 1)
            ->first();
            
        if (!$setting) {
            return false;
        }
        
        $setting->status = 0;
        return $setting->save();
    }
}