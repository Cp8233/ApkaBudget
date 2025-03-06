<?php

namespace App\Services;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    }

    public function capturePayment($transactionId, $amount)
    {
        try {
            $payment = $this->api->payment->fetch($transactionId);
            return $payment->capture(['amount' => $amount]);
        } catch (\Exception $e) {
            Log::error('payment capture failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getPaymentDetails($transactionId)
    {
        try {
            return $this->api->payment->fetch($transactionId);
        } catch (\Exception $e) {
            Log::error('fetch payment failed: ' . $e->getMessage());
            return null;
        }
    }
}
