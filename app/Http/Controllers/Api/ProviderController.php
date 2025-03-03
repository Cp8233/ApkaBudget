<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Category;
use App\Models\IdentityType;
use App\Models\Transaction;
use App\Models\Review;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use Twilio\Rest\Client;

class ProviderController extends Controller
{
    protected function dashboard()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $overviews = [
                'total_earning' => 0,
                'total_booking' => 0,
                'today_booking' => 0,
                'pending_booking' => 0,
            ];

            $reviews = Review::where('reviewee_id', $this->user->id)->with(['reviewer:id,name,profile'])->take(6)->select('id', 'reviewer_id', 'rating', 'review', 'created_at')->get();

            return $this->successResponse(['overviews' => $overviews, 'reviews' => $reviews], 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function countries()
    {
        try {
            $countries = Country::all();

            if ($countries->isEmpty()) {
                return $this->errorResponse('No countries found', 404);
            }

            return $this->successResponse($countries, 'Countries retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    protected function states(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'country_id' => 'required'
        ]);

        if ($validation) return $validation;

        try {
            $country_id = $request->country_id;
            $states = State::where('country_id', $country_id)->select('id', 'name')->get();

            if ($states->isEmpty()) {
                return $this->errorResponse('No state found', 404);
            }

            return $this->successResponse($states, 'States retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    protected function cities(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'state_id' => 'required'
        ]);

        if ($validation) return $validation;

        try {
            $state_id = $request->state_id;
            $cities = City::where('state_id', $state_id)->select('id', 'name')->get();

            if ($cities->isEmpty()) {
                return $this->errorResponse('No state found', 404);
            }

            return $this->successResponse($cities, 'States retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function identity_types()
    {
        try {
            $identity_types = IdentityType::where('status', 1)->select('id', 'identity')->get();

            if ($identity_types->isEmpty()) {
                return $this->errorResponse('No countries found', 404);
            }

            return $this->successResponse($identity_types, 'Identity Types retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function plans(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'type' => 'required|in:1,2' //1-subscriptions plan,2-security plan
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $plan = Plan::where('type', $request->type)->get();

            if (!$plan) {
                return $this->errorResponse('Invalid plan selected', 400);
            }

            return $this->successResponse($plan, 'Plan retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    //payment time hit api
    protected function payment_status(Request $request)
    {

        $validation = $this->validateRequest($request, [
            'plan_id' => 'required|exists:plans,id',
            // 'transaction_id' => 'required',
            'status' => 'required|in:success,failed,pending',
            'amount' => 'required'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $plan = Plan::find($request->plan_id);

            if (!$plan) {
                return $this->errorResponse('Invalid plan selected', 400);
            }
            // Check Existing Subscription of Same Type
            $existingSubscription = Subscription::where([
                ['user_id', $this->user->id],
                ['type', $plan->type]
            ])->first();

            $subscriptionData = [
                'type' => $plan->type,
                'user_id' => $this->user->id,
                'plan_id' => $request->plan_id,
                'status' => ($request->status == 'success') ? 'active' : 'pending',
                'start_date' => ($request->status == 'success') ? now() : null,
                'end_date' => ($request->status == 'success') ? now()->addDays($plan->duration) : null
            ];
            // Update or Create Subscription
            if ($existingSubscription) {
                $existingSubscription->update($subscriptionData);
            } else {
                $existingSubscription = Subscription::create($subscriptionData);
            }


            $transactionData = [
                'type' => $plan->type,
                'user_id' => $this->user->id,
                'transaction' => 2, // 2 = debit
                'amount' => $request->amount,
                'transaction_id' => $request->transaction_id,
                'subscription_id' => $request->plan_id,
                'status' => $request->status
            ];

            Transaction::create($transactionData);

            return $this->successResponse('Transaction added successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function bookings()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }
            $bookings = Order::with([
                'user:id,name',
                'subCategory:id,name',
                'address:id,address,latitude,longitude,flat_no,landmark'
            ])
                ->select('id', 'booking_id', 'user_id', 'subcategory_id', 'address_id', 'total_price', 'payment_method', 'status', 'created_at')
                ->get();

            if ($bookings->isEmpty()) {
                return $this->errorResponse('No Bookings found', 404);
            }

            return $this->successResponse($bookings, 'Bookings retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $amount = $plan->price * 100; // Convert to paisa

        $apiKey = env('RAZORPAY_KEY_ID');
        $apiSecret = env('RAZORPAY_KEY_SECRET');

        // Generate unique transaction ID
        $transactionId = 'TXN-' . strtoupper(Str::random(10));

        // Create Razorpay order
        $response = Http::withBasicAuth($apiKey, $apiSecret)->post('https://api.razorpay.com/v1/orders', [
            'amount' => $amount,
            'currency' => 'INR',
            'receipt' => $transactionId,
            'payment_capture' => 1 // Auto capture payment
        ]);

        $responseData = $response->json();

        if (!empty($responseData['id'])) {
            // Save transaction to database
            Transaction::create([
                'user_id' => $this->user->id,
                'type' => $plan->type,
                'transaction_id' => $transactionId,
                'amount' => $plan->price,
                'status' => 'pending',
                'subscription_id' => $plan->id
            ]);

            return response()->json([
                'order_id' => $responseData['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'key' => $apiKey,
                'name' => 'Your Company Name',
                'description' => $plan->name,
                'prefill' => [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'contact' => $this->user->mobile_no
                ],
                'theme' => ['color' => '#3399cc']
            ]);
        }

        return response()->json(['error' => 'Failed to create Razorpay order'], 500);
    }

    //   protected function initiatePayment(Request $request)
    // {
    //     $validation = $this->validateRequest($request, [
    //         'plan_id' => 'required|exists:plans,id'
    //     ]);

    //     if ($validation) return $validation;

    //     try {
    //         if (!$this->user) {
    //             return $this->errorResponse('User not found', 404);
    //         }

    //         $plan = Plan::findOrFail($request->plan_id);
    //         $amount = $plan->price * 100; // Convert to paise

    //         // PhonePe credentials
    //         $merchantId = env('PHONEPE_MERCHANT_ID');
    //         $saltKey = env('PHONEPE_SALT_KEY');
    //         $saltIndex = env('PHONEPE_SALT_INDEX');
    //         $baseUrl = env('PHONEPE_BASE_URL');

    //         // Generate unique transaction ID
    //         $transactionId = 'TXN-' . strtoupper(Str::random(10));

    //         // Prepare payload for payment initiation
    //         $payload = [
    //             "merchantId" => $merchantId,
    //             "merchantTransactionId" => $transactionId,
    //             "merchantUserId" => (string) $this->user->id,
    //             "amount" => $amount,
    //             "redirectUrl" => url('/api/payment-callback'),
    //             "redirectMode" => "REDIRECT",
    //             "callbackUrl" => url('/api/payment-callback'),
    //             "mobileNumber" => $this->user->mobile_no,
    //             "deviceContext" => [
    //                 "deviceOS" => "ANDROID"
    //             ],
    //             "paymentInstrument" => [
    //                 "type" => "UPI_INTENT"
    //             ]
    //         ];

    //         $payloadBase64 = base64_encode(json_encode($payload));
    //         $checksum = hash('sha256', $payloadBase64 . "/pg/v1/pay" . $saltKey) . "###" . $saltIndex;

    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'X-VERIFY' => $checksum
    //         ])->post($baseUrl . "/pg/v1/pay", ['request' => $payloadBase64]);

    //         $responseData = $response->json();
    //         dd($responseData);
    //         Log::info('PhonePe Response:', $responseData);

    //         if (!empty($responseData['success']) && $responseData['success']) {
    //             $redirectUrl = $responseData['data']['instrumentResponse']['redirectInfo']['url'] ?? null;
    //             if ($redirectUrl) {
    //                 Transaction::create([
    //                     'user_id' => $this->user->id,
    //                     'type' => $plan->type,
    //                     'transaction_id' => $transactionId,
    //                     'amount' => $plan->price,
    //                     'status' => 'pending',
    //                     'subscription_id' => $plan->id,
    //                     'transaction' => 2,
    //                 ]);

    //                 return $this->successResponse([
    //                     'payment_url' => $redirectUrl,
    //                     'transaction_id' => $transactionId
    //                 ]);
    //             }
    //         }

    //         return $this->errorResponse('Payment initiation failed', 500);
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //     }
    // }

    protected function paymentCallback(Request $request)
    {
        Log::info('PhonePe Callback:', $request->all());

        $transaction = Transaction::where('transaction_id', $request->input('transactionId'))->first();

        if (!$transaction) {
            return $this->errorResponse('Transaction not found', 404);
        }

        $paymentStatus = strtolower($request->input('transactionStatus')); // success, failed, pending

        $transaction->update(['status' => $paymentStatus]);

        if ($paymentStatus === 'success') {
            $plan = Plan::find($transaction->subscription_id);

            Subscription::updateOrCreate(
                ['user_id' => $transaction->user_id, 'type' => $plan->type],
                [
                    'type' => $plan->type,
                    'user_id' => $transaction->user_id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'start_date' => now(),
                    'end_date' => now()->addDays($plan->duration)
                ]
            );
        }

        return $this->successResponse('Payment status updated successfully');
    }

    protected function verifyPayment(Request $request)
    {
        // Validate the incoming request
        $validation = $this->validateRequest($request, [
            'transaction_id' => 'required|exists:transactions,transaction_id'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            // Fetch the pending transaction
            $transaction = Transaction::where('transaction_id', $request->transaction_id)
                ->where('user_id', $this->user->id)
                ->where('status', 'pending')
                ->first();

            if (!$transaction) {
                return $this->errorResponse('Transaction not found or already processed', 400);
            }

            // PhonePe environment variables
            $merchantId = env('PHONEPE_MERCHANT_ID');
            $saltKey = env('PHONEPE_SALT_KEY');
            $saltIndex = env('PHONEPE_SALT_INDEX');
            $baseUrl = rtrim(env('PHONEPE_BASE_URL'), '/');

            // Prepare API URL and checksum
            $statusUrl = "/pg/v1/status/$merchantId/{$transaction->transaction_id}";
            $checksum = hash('sha256', $statusUrl . $saltKey) . "###" . $saltIndex;

            // Make the API request to check payment status
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $checksum,
                'X-MERCHANT-ID' => $merchantId,
            ])->get($baseUrl . $statusUrl);

            $responseData = $response->json();
            Log::info('PhonePe Status API Response:', $responseData);

            // Handle PhonePe response
            if (!$response->successful() || empty($responseData['success'])) {
                return $this->errorResponse('Payment verification failed', 400, ['response' => $responseData]);
            }

            $paymentStatus = strtolower($responseData['data']['state'] ?? 'failed');
            $transaction->update(['status' => $paymentStatus]);

            // Handle payment success
            if ($paymentStatus === 'completed') {
                $plan = Plan::find($transaction->subscription_id);
                Subscription::updateOrCreate(
                    ['user_id' => $this->user->id, 'type' => $plan->type],
                    [
                        'type' => $plan->type,
                        'user_id' => $this->user->id,
                        'plan_id' => $plan->id,
                        'status' => 'active',
                        'start_date' => now(),
                        'end_date' => now()->addDays($plan->duration)
                    ]
                );

                return $this->successResponse('Payment verified');
            }

            // Handle pending or failed payments
            return $this->errorResponse('Payment verification failed', 400, ['status' => $paymentStatus]);
        } catch (\Exception $e) {
            Log::error('Payment verification error:', ['error' => $e->getMessage()]);
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function makeCall(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'to' => 'required|numeric|digits:10', // Customer ka number
        ]);

        
        $toNumber = '+91' . $request->to; // E.164 format for India (+91)

        // Twilio API endpoint
        $url = "https://api.twilio.com/2010-04-01/Accounts/$accountSid/Calls.json";

        // Prepare data for the API request
        $postData = http_build_query([
            'From' => $fromNumber,
            'To' => $toNumber,
            'Url' => 'https://api.apkabudget.com/public/voice.xml', // TwiML instructions
        ]);

        // Initialize cURL session
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$accountSid:$authToken");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Handle Twilio's response
        if ($httpCode == 201) {
            return response()->json(['message' => 'Call initiated successfully!', 'response' => json_decode($response)], 200);
        } else {
            return response()->json(['message' => 'Failed to initiate call', 'error' => json_decode($response)], $httpCode);
        }
    }
}
