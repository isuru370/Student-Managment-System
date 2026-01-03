<?php

namespace App\Http\Controllers;

use App\Models\WelfarePayment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\WelfarePaymentService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Validation\ValidationException;

class WelfarePaymentController extends Controller
{
    protected WelfarePaymentService $service;

    public function __construct(WelfarePaymentService $service)
    {
        $this->service = $service;
    }

    /**
     * Display welfare payments index page
     */
    public function index(): View
    {
        $welfarePayments = $this->service->fetch();

        
        return view('welfare_payments.index', [
            'welfarePayments' => $welfarePayments,
        ]);
    }

    /**
     * Show the form for creating a new welfare payment
     */
    public function create(): View
    {
        return view('welfare_payments.create');
    }

    /**
     * Store a newly created welfare payment
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $this->service->store($request);

            return redirect()
                ->route('welfare_payments.index')
                ->with('success', 'Welfare payment created successfully.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create welfare payment.');
        }
    }

    /**
     * Display the specified welfare payment
     */
    public function show(int $id): View
    {
        $payment = WelfarePayment::withTrashed()
            ->with(['teacher', 'user'])
            ->findOrFail($id);

        return view('welfare_payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified welfare payment
     */
    public function edit(int $id): View
    {
        $payment = WelfarePayment::withTrashed()
            ->with(['teacher', 'user'])
            ->findOrFail($id);

        return view('welfare_payments.edit', compact('payment'));
    }

    /**
     * Update the specified welfare payment
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $this->service->update($request, $id);

            return redirect()
                ->route('welfare_payments.index')
                ->with('success', 'Welfare payment updated successfully.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update welfare payment.');
        }
    }

    /**
     * Soft delete the specified welfare payment
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->service->delete($id);

            return redirect()
                ->route('welfare_payments.index')
                ->with('success', 'Welfare payment deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('welfare_payments.index')
                ->with('error', 'Failed to delete welfare payment.');
        }
    }

    /**
     * Restore a soft-deleted welfare payment
     */
    public function restore(int $id): RedirectResponse
    {
        try {
            $this->service->restore($id);

            return redirect()
                ->route('welfare_payments.index')
                ->with('success', 'Welfare payment restored successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('welfare_payments.index')
                ->with('error', 'Failed to restore welfare payment.');
        }
    }
}
