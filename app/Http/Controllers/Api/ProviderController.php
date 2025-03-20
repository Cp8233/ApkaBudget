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
use App\Models\User;
use App\Models\Zone;
use App\Models\ZoneProvider;
use Illuminate\Support\Facades\DB;

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
            $countries = Country::where('status',1)->get();

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

            if ($this->user->id == 39) {

                $plan = Plan::where('type', $request->type)->where('id', 6)->get();
            } else {

                $plan = Plan::where('type', $request->type)->get();
            }

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
            // 'plan_id' => 'required|exists:plans,id',
            // 'transaction_id' => 'required',
            'status' => 'required|in:success,failed,pending',
            // 'amount' => 'required'
        ]);

        // if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            // $plan = Plan::find($request->plan_id);

            // if (!$plan) {
            //     return $this->errorResponse('Invalid plan selected', 400);
            // }
            // Check Existing Subscription of Same Type
            // $existingSubscription = Subscription::where([
            //     ['user_id', $this->user->id],
            //     ['type', $plan->type]
            // ])->first();

            // $subscriptionData = [
            //     'type' => $plan->type,
            //     'user_id' => $this->user->id,
            //     'plan_id' => $request->plan_id,
            //     'status' => ($request->status == 'success') ? 'active' : 'pending',
            //     'start_date' => ($request->status == 'success') ? now() : null,
            //     'end_date' => ($request->status == 'success') ? now()->addDays($plan->duration) : null
            // ];
            // Update or Create Subscription
            // if ($existingSubscription) {
            //     $existingSubscription->update($subscriptionData);
            // } else {
            //     $existingSubscription = Subscription::create($subscriptionData);
            // }


            // $transactionData = [
            //     'type' => $plan->type,
            //     'user_id' => $this->user->id,
            //     'transaction' => 2, // 2 = debit
            //     'amount' => $request->amount,
            //     'transaction_id' => $request->transaction_id,
            //     'subscription_id' => $request->plan_id,
            //     'status' => $request->status
            // ];

            // Transaction::create($transactionData);

            return $this->successResponse('Transaction added successfully', $request->status == 'success' ? 'Success' : 'Payment failed, please try again');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function bookings(Request $request)
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }
            $status = $request->input('status', 'placed');
            
            $validStatuses = ['placed', 'accepted', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return $this->errorResponse('Invalid status provided', 404);
        }
             $assignedZoneIds = ZoneProvider::where('user_id', $this->user->id)->pluck('zone_id');
            if ($assignedZoneIds->isEmpty()) {
                return $this->errorResponse('No zones assigned to this provider', 404);
            }
            $zones = Zone::whereIn('id', $assignedZoneIds)->get(['id', 'name', 'boundary']);
            if ($zones->isEmpty()) {
                return $this->errorResponse('No zones found', 404);
            }
            $bookings = Order::with([
                'user:id,name,mobile_no',
                'subCategory:id,name,image',
                'address:id,address,latitude,longitude,flat_no,landmark',
                // 'orderItems:id,order_id,service_id,quantity,unit_price,total_price'
            ])
                ->select('id', 'booking_id', 'user_id', 'subcategory_id', 'address_id', 'total_price', 'status', 'created_at')
                ->where('status', $status)
                ->orderBy('id', 'DESC')
                ->get()->filter(function ($booking) use ($zones) {
                    if (!$booking->address) {
                        return false;
                    }
                    $lat = (float)$booking->address->latitude;
                    $lng = (float)$booking->address->longitude;

                    // Check if lat-lng lies within any zone's boundary
                    foreach ($zones as $zone) {
                        $boundaries = json_decode($zone->boundary, true);

                        if (!is_array($boundaries)) {
                            throw new \Exception("Invalid boundary data for zone: " . $zone->id);
                        }

                        if ($this->isPointInPolygon($lat, $lng, $boundaries)) {
                            return true;
                        }
                    }
                    return false; // Skip if outside all assigned zones
                });

            $bookings->transform(function ($booking) {
                if ($booking->subCategory && $booking->subCategory->image) {
                    $booking->subCategory->image = url($booking->subCategory->image);
                }
                return $booking;
            });

            if ($bookings->isEmpty()) {
                return $this->errorResponse('No Bookings found', 404);
            }

            return $this->successResponse($bookings, 'Bookings retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
        protected function isPointInPolygon($latitude, $longitude, $polygon)
    {
        if (!is_array($polygon) || count($polygon) < 3) {
            throw new \Exception("Invalid polygon data: Polygon must have at least 3 points.");
        }

        foreach ($polygon as $index => $point) {
            if (!isset($point['lng']) || !isset($point['lat'])) {
                throw new \Exception("Invalid polygon data at index $index: Missing lat or lng.");
            }
        }

        $inside = false;
        $x = (float)$longitude;
        $y = (float)$latitude;
        $numPoints = count($polygon);
        $j = $numPoints - 1;

        for ($i = 0; $i < $numPoints; $j = $i++) {
            $xi = (float)$polygon[$i]['lng'];
            $yi = (float)$polygon[$i]['lat'];
            $xj = (float)$polygon[$j]['lng'];
            $yj = (float)$polygon[$j]['lat'];

            $intersect = (($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
    protected function booking_detail(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'id' => 'required|exists:orders,id'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $id = $request->id;

            $booking = Order::with([
                'user:id,name,mobile_no',
                'subCategory:id,name',
                'address:id,address,latitude,longitude,flat_no,landmark',
                'orderItems:id,order_id,service_id,quantity,unit_price,total_price',
                'orderItems.service:id,service_name,image'
            ])
                ->select('id', 'booking_id', 'user_id', 'subcategory_id', 'slot_date', 'slot_start_time', 'slot_end_time', 'address_id', 'total_price', 'payment_method', 'status', 'status', 'created_at')
                ->where('id', $id)
                ->first();

            if (!$booking) {
                return $this->errorResponse('Booking not found', 404);
            }

            $booking->orderItems->transform(function ($item) {
                if ($item->service && $item->service->image) {
                    $item->service->image = url($item->service->image);
                }
                return $item;
            });

            return $this->successResponse($booking, 'Booking details retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    // protected function booking_action(Request $request)
    // {
    //     $validation = $this->validateRequest($request, [
    //         'id' => 'required|exists:orders,id',
    //         'status' => 'required|in:accepted',
    //     ]);

    //     if ($validation) return $validation;

    //     try {
    //         if (!$this->user) {
    //             return $this->errorResponse('User not found', 404);
    //         }

    //         $order = Order::where('id', $request->id)->where('status', 'placed')->first();

    //         if (!$order) {
    //             return $this->errorResponse('Order not found or already accepted', 404);
    //         }

    //         // Lock the order row to prevent race conditions
    //         DB::beginTransaction();

    //         $order = Order::where('id', $request->id)->where('status', 'placed')->lockForUpdate()->first();

    //         if (!$order) {
    //             DB::rollBack();
    //             return $this->errorResponse('Order already accepted by another provider', 409);
    //         }

    //         $order->update([
    //             'provider_id' => $this->user->id,
    //             'status' => 'accepted'
    //         ]);

    //         // Save notification for user
    //         Notification::create([
    //             'user_id' => $order->user_id,
    //             'title' => 'Booking Accepted',
    //             'message' => "Your booking (ID: {$order->booking_id}) has been accepted by the provider."
    //         ]);

    //         // Send push notification to user
    //         $userDeviceToken = User::where('id', $order->user_id)->value('device_token');
    //         if ($userDeviceToken) {
    //             $this->notificationService->sendPushNotification([$userDeviceToken], 'Booking Accepted', "Your booking has been accepted.");
    //         }

    //         DB::commit();

    //         return $this->successResponse('Booking accepted successfully');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //     }
    // }
    protected function booking_action(Request $request)
{
    $validation = $this->validateRequest($request, [
        'id' => 'required|exists:orders,id',
        'status' => 'required|in:accepted,completed',
    ]);

    if ($validation) return $validation;

    try {
        if (!$this->user) {
            return $this->errorResponse('User not found', 404);
        }

        $order = Order::where('id', $request->id)->first();

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        // Status check for accepted or completed
        if ($request->status === 'accepted') {
            if ($order->status !== 'placed') {
                return $this->errorResponse('Order already accepted or invalid status', 409);
            }

            // Lock to prevent race conditions
            DB::beginTransaction();
            $order = Order::where('id', $request->id)->where('status', 'placed')->lockForUpdate()->first();

            if (!$order) {
                DB::rollBack();
                return $this->errorResponse('Order already accepted by another provider', 409);
            }

            $order->update([
                'provider_id' => $this->user->id,
                'status' => 'accepted'
            ]);

            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Booking Accepted',
                'message' => "Your booking (ID: {$order->booking_id}) has been accepted by the provider."
            ]);

            $userDeviceToken = User::where('id', $order->user_id)->value('device_token');
            if ($userDeviceToken) {
                $this->notificationService->sendPushNotification([$userDeviceToken], 'Booking Accepted', "Your booking has been accepted.");
            }

            DB::commit();
            return $this->successResponse('Booking accepted successfully');

        } elseif ($request->status === 'completed') {
             if ($order->status === 'completed') {
        return $this->errorResponse('Order is already completed.', 409);
    }
            if ($order->status !== 'accepted') {
                return $this->errorResponse('Order cannot be completed. It is not in accepted state.', 409);
            }

            $order->update([
                'status' => 'completed'
            ]);

            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Booking Completed',
                'message' => "Your booking (ID: {$order->booking_id}) has been marked as completed by the provider."
            ]);

            return $this->successResponse('Booking completed successfully');
        }
    } catch (\Exception $e) {
        DB::rollBack();
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
