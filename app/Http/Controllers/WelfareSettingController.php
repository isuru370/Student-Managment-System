<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\WelfareSettingService;

class WelfareSettingController extends Controller
{
    protected WelfareSettingService $service;

    public function __construct(WelfareSettingService $service)
    {
        $this->service = $service;
    }

    /**
     * Display welfare settings
     */
    public function indexPage(): View
    {
        // Fetch the current welfare setting for the user
        $welfareSetting = $this->service->fetch();
        
        return view('welfare_settings.index', compact('welfareSetting'));
    }

    /**
     * Store a new welfare setting
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:1000000',
        ]);

        try {
            $this->service->create($validated['amount']);
            
            return redirect()->route('welfare_settings.index')
                ->with('success', 'Welfare amount set successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to set welfare amount. Please try again.');
        }
    }

    /**
     * Delete (deactivate) a welfare setting
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $result = $this->service->delete((int) $id);
            
            if ($result) {
                return redirect()->route('welfare_settings.index')
                    ->with('success', 'Welfare setting deactivated successfully.');
            }
            
            return redirect()->route('welfare_settings.index')
                ->with('error', 'Failed to deactivate welfare setting.');
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('welfare_settings.index')
                ->with('error', 'Welfare setting not found.');
                
        } catch (\Exception $e) {
            return redirect()->route('welfare_settings.index')
                ->with('error', 'An error occurred. Please try again.');
        }
    }
}