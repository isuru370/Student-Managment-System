<?php

namespace App\Http\Controllers;

use App\Services\PaymentReasonService;
use Illuminate\Http\Request;

class PaymentReasonController extends Controller
{
    protected $paymentReasonService;

    public function __construct(PaymentReasonService $paymentReasonService)
    {
        $this->paymentReasonService = $paymentReasonService;
    }

    // Fetch all payment reasons
    public function fetchAllPaymentReason()
    {
        return $this->paymentReasonService->fetchAllPaymentReason();
    }

    // Store new payment reason
    public function store(Request $request)
    {
        return $this->paymentReasonService->store($request);
    }

    // Update payment reason
    public function update(Request $request, $id)
    {
        return $this->paymentReasonService->update($request, $id);
    }

    // Update payment reason
    public function destroy($id)
    {
        return $this->paymentReasonService->delete($id);
    }


    // Get dropdown list (id + reason_code)
    public function getDropdown()
    {
        return $this->paymentReasonService->getDropdownPaymentReason();
    }


    // web page route

    public function indexPage()
    {
        return view('payment_reason.index');
    }
}
