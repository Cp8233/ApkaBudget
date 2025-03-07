<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\Service;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;
use App\Models\User;
use App\Models\Transaction;

class UserController extends Controller
{
    protected function send_notification(Request $request)
    {
        $deviceTokens = $request->device_token;
        $title = $request->title ?? "Test Notification";
        $message = $request->message ?? "This is a test message";

        $response = $this->notificationService->sendPushNotification($deviceTokens, $title, $message);
        // Single device:
        // $this->sendPushNotification('YOUR_DEVICE_TOKEN', 'Hello', 'This is a test notification!');
        // Multiple devices:
        // $this->sendPushNotification(['TOKEN_1', 'TOKEN_2', 'TOKEN_3'], 'Hello', 'Group notification sent!');
        dd($response);
    }
    protected function sub_categories(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'category_id'     => 'required|exists:categories,id'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $category_id = $request->category_id;
            $SubCategories = SubCategory::where('category_id', $category_id)->select('id', 'name', 'image')->get();

            if ($SubCategories->isEmpty()) {
                return $this->errorResponse('No subcategory found', 404);
            }

            $SubCategories->transform(function ($subcategory) {
                $subcategory->image = url($subcategory->image);
                return $subcategory;
            });

            return $this->successResponse($SubCategories, 'Subctegories retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function services(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'category_id'     => 'required|exists:categories,id',
            'subcategory_id'     => 'required|exists:sub_categories,id'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id = $this->user->id;
            $category_id = $request->category_id;
            $subcategory_id = $request->subcategory_id;

            $SubSubCategories = SubSubCategory::where(['sub_subcategory_id' => $category_id, 'subcategory_id' => $subcategory_id])->select('id', 'sub_subcategory_name', 'image')->get();

            if ($SubSubCategories->isEmpty()) {
                return $this->errorResponse('No sub-subcategories found', 404);
            }
            $SubSubCategories->transform(function ($subSubCategory) {
                $subSubCategory->image = url($subSubCategory->image);
                return $subSubCategory;
            });
            $services = Service::where([
                'category_id'    => $category_id,
                'subcategory_id' => $subcategory_id
            ])->with('subSubCategory:id,sub_subcategory_name')
                ->select('id', 'sub_subcategory_id', 'service_name', 'image', 'price', 'time')
                ->get();

            if ($services->isEmpty()) {
                return $this->errorResponse('No service found', 404);
            }

            $cartItems = Cart::where('user_id', $user_id)
                ->whereIn('service_id', $services->pluck('id')) // Filter services in this subcategory
                ->with('service:id,service_name,image') // Load service details
                ->get()
                ->keyBy('service_id');

            $subcategory_total_price = $cartItems->sum('price');
            $total_items = $cartItems->sum('quantity');
            $total_unit_price = $cartItems->sum('unit_price');

            $groupedServices = $services->groupBy('sub_subcategory_id')->map(function ($services, $sub_subcategory_id) use ($cartItems) {
                return [
                    'sub_subcategory_id'   => $sub_subcategory_id,
                    'sub_subcategory_name' => $services->first()->subSubCategory->sub_subcategory_name ?? 'N/A',
                    'services'             => $services->map(function ($service) use ($cartItems) {
                        $cartItem = $cartItems[$service->id] ?? null;
                        return [
                            'id'           => $service->id,
                            'service_name' => $service->service_name,
                            'image'        => url($service->image),
                            'price'        => $service->price,
                            'time'         => $service->time,
                            'in_cart'      => $cartItem !== null,
                            'quantity'     => $cartItem->quantity ?? 0,
                            'total_price'  => $cartItem->price ?? 0, // Individual total
                            'unit_total_price' => ($cartItem ? $cartItem->unit_price * $cartItem->quantity : 0) // unit_price * quantity
                        ];
                    })
                ];
            })->values();

            // Add cart details related to this category/subcategory
            $cartDetails = $cartItems->map(function ($cartItem) {
                return [
                    'subcategory_id'   => $cartItem->subcategory_id,
                    'service_id'   => $cartItem->service->id,
                    'service_name' => $cartItem->service->service_name,
                    'image'        => url($cartItem->service->image),
                    'unit_price'   => $cartItem->unit_price,
                    'quantity'     => $cartItem->quantity,
                    'total_price'  => $cartItem->price
                ];
            })->values();

            $data = [
                'sub_subcategories' => $SubSubCategories,
                'services'                => $groupedServices,
                'cart_items'              => $cartDetails,
                'subcategory_total_price' => $subcategory_total_price,
                'total_items'             => $total_items,
                'total_unit_price'        => $total_unit_price,
            ];

            return $this->successResponse($data, 'Services retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function rate_card(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'service_id'     => 'required|exists:services,id',
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id = $this->user->id;
            $service_id = $request->service_id;

            $parts = Part::where('service_id', $service_id)
                ->select('id', 'service_id', 'part')
                ->with(['priceLists' => function ($query) {
                    $query->select('id', 'part_id', 'detail', 'charge', 'labour_charge');
                }])
                ->get();
            
            if ($parts->isEmpty()) {
                return $this->errorResponse('No parts found for the given service', 404);
            }

            $data = $parts->map(function ($part) {
                return [
                    'part_id' => $part->id,
                    'part_name' => $part->part,
                    'prices' => $part->priceLists->map(function ($price) {
                        return [
                            'price_id' => $price->id,
                            'detail' => $price->detail,
                            'charge' => $price->charge,
                            'labour_charge' => $price->labour_charge
                        ];
                    }),
                ];
            });

            return $this->successResponse($data, 'Rate card retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function addToCart(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'service_id'     => 'required|exists:services,id',
            'quantity'   => 'required|integer|min:0'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id    = $this->user->id;
            $service_id = $request->service_id;
            $quantity   = $request->quantity;

            $service = Service::find($service_id);
            if (!$service) {
                return $this->errorResponse('Service not found', 404);
            }
            $subcategory_id = $service->subcategory_id;

            $cartItem = Cart::where(['user_id' => $user_id, 'service_id' => $service_id])->first();

            if ($cartItem) {
                if ($quantity == 0) {
                    // Remove item if quantity is 0
                    $cartItem->delete();
                    return $this->successResponse([], 'Service removed from cart');
                }

                // Update quantity
                $cartItem->quantity = $quantity;
                $cartItem->unit_price = $cartItem->unit_price ?? $service->price; // Ensure stored price
                $cartItem->price = $cartItem->unit_price * $quantity;
                $cartItem->save();
            } else {
                if ($quantity > 0) {
                    // Create new cart item only if quantity > 0
                    Cart::create([
                        'subcategory_id'  => $subcategory_id,
                        'user_id'    => $user_id,
                        'service_id' => $service_id,
                        'quantity'   => $quantity,
                        'unit_price' => $service->price,
                        'price'      => $service->price * $quantity
                    ]);
                } else {
                    return $this->errorResponse('Invalid quantity', 400);
                }
            }

            return $this->successResponse([], 'Service added to cart successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    // protected function view_cart()
    // {
    //     try {
    //         if (!$this->user) {
    //             return $this->errorResponse('User not found', 404);
    //         }

    //         $user_id    = $this->user->id;
    //         $cartItems = Cart::where('user_id', $user_id)->with('service:id,service_name,image')->get();

    //         if ($cartItems->isEmpty()) {
    //             return $this->errorResponse('Cart is empty', 404);
    //         }

    //         $total_price = 0;
    //         $total_items = 0;

    //         $cartDetails = $cartItems->map(function ($cartItem) use (&$total_price, &$total_items) {
    //             $service = $cartItem->service;
    //             if (!$service) return null; // Skip if service is missing

    //             $total_price += $cartItem->price; // Use stored price
    //             $total_items += $cartItem->quantity;

    //             return [
    //                 'service_id'   => $service->id,
    //                 'service_name' => $service->service_name,
    //                 'image'        => url($service->image),
    //                 'unit_price'   => $cartItem->unit_price, // Show stored price
    //                 'quantity'     => $cartItem->quantity,
    //                 'total_price'  => $cartItem->price // Use stored total price
    //             ];
    //         })->filter()->values();

    //         $calculation = [
    //             'total_items' => $total_items,
    //             'total_price' => $total_price,
    //             'cart_items'  => $cartDetails
    //         ];

    //         return $this->successResponse($calculation, 'Cart retrieved successfully');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //     }
    // }
    protected function save_location(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'address'     => 'required',
            'latitude'     => 'required',
            'longitude'     => 'required',
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $this->user->address = $request->address;
            $this->user->latitude = $request->latitude;
            $this->user->longitude = $request->longitude;
            $this->user->save();

            return $this->successResponse('Location saved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function add_address(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'type'     => 'required', //1-home , 2-other
            'address'     => 'required',
            'latitude'   => 'required',
            'longitude'   => 'required',
            'flat_no'   => 'required'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id    = $this->user->id;
            $type = $request->type;
            $address = $request->address;
            $latitude   = $request->latitude;
            $longitude   = $request->longitude;
            $flat_no   = $request->flat_no;
            $landmark   = $request->landmark;

            $newAddress = Address::create([
                'type'      => $type,
                'user_id'      => $user_id,
                'address' => $address,
                'latitude'     => $latitude,
                'longitude'    => $longitude,
                'flat_no'      => $flat_no,
                'landmark'     => $landmark
            ]);

            return $this->successResponse([], 'Address added successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function addresses()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id    = $this->user->id;
            $addresses = Address::where('user_id', $user_id)->select('id', 'type', 'user_id', 'address', 'latitude', 'longitude', 'flat_no', 'landmark')->get();

            return $this->successResponse($addresses, 'Address retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function getDailySlots(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'date' => 'required|date' // YYYY-MM-DD format
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $date = Carbon::parse($request->date)->setTimezone('Asia/Kolkata'); // Local timezone
            $startTime = $date->copy()->setTime(9, 0); // 9:00 AM
            $endTime = $date->copy()->setTime(18, 0);  // 6:00 PM
            $interval = 15; // 15-minute slots
            $slots = [];

            $now = Carbon::now('Asia/Kolkata'); // Current time in IST

            while ($startTime < $endTime) {
                $nextSlot = $startTime->copy()->addMinutes($interval);

                // Skip past slots if date is today
                if ($date->isToday() && $startTime->lessThan($now)) {
                    $startTime = $nextSlot;
                    continue;
                }

                $slots[] = [
                    'slot' => $startTime->format('g:i A') . ' - ' . $nextSlot->format('g:i A'),
                    'start_time' => $startTime->format('H:i'),
                    'end_time' => $nextSlot->format('H:i'),
                    'is_available' => true
                ];

                $startTime = $nextSlot;
            }

            return $this->successResponse([
                'date' => $date->toDateString(),
                'slots' => $slots
            ], 'Slots fetched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function checkout(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'subcategory_id'     => 'required|exists:sub_categories,id',
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:online,cod',
            'slot_start_time' => 'required|date_format:H:i', // Format: 09:00
            'slot_end_time' => 'required|date_format:H:i|after:slot_start_time' // Ensure end time is after start time
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id = $this->user->id;
            $subcategory_id = $request->subcategory_id;
            $address_id = $request->address_id;
            $payment_method = $request->payment_method;
            $slot_start_time = $request->slot_start_time;
            $slot_end_time = $request->slot_end_time;

            // Fetch cart items for this subcategory
            $cartItems = Cart::where('user_id', $user_id)
                ->where('subcategory_id', $subcategory_id)
                ->get();

            // if ($cartItems->isEmpty()) {
            //     return $this->errorResponse('Cart is empty for this subcategory', 400);
            // }

            $total_price = $cartItems->sum('price');
            $booking_id = 'BOOK-' . strtoupper(Str::random(8));
            $order = Order::create([
                'user_id' => $user_id,
                'subcategory_id' => $subcategory_id,
                'address_id' => $address_id,
                'total_price' => $total_price,
                'payment_method' => $payment_method,
                'booking_id' => $booking_id,
                'status' => 'pending',
                'slot_start_time' => $slot_start_time,
                'slot_end_time' => $slot_end_time
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $cartItem->service_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total_price' => $cartItem->price
                ]);
            }
            //cart item delete
            Cart::where('user_id', $user_id)->where('subcategory_id', $subcategory_id)->delete();

            if ($payment_method == 'cod') {
                $serviceProviders = User::where('role', 2)->whereNotNull('device_token')->pluck('device_token')->toArray();
                // Save notification for each provider
                foreach ($serviceProviders as $token) {
                    Notification::create([
                        'user_id' => User::where('device_token', $token)->value('id'),
                        'title'   => 'New Booking Received!',
                        'messge' => "You have received a new booking (ID: {$booking_id}). Total Amount: ₹{$total_price}."
                    ]);
                }

                // Send push notification to multiple providers
                if (!empty($serviceProviders)) {
                    $title = 'New Booking Received!';
                    $message = "You have received a new booking (ID: {$booking_id}). Total Amount: ₹{$total_price}.";
                    $res = $this->notificationService->sendPushNotification($serviceProviders, $title, $message);
                }
            }

            $Responsedata = [
                'order_id'    => $order->id,
                'booking_id'  => $booking_id,
                'total_price' => $total_price,
                'status'      => $order->status
            ];

            return $this->successResponse($Responsedata, 'Checkout successful, proceed to payment');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function paymentstatus(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'order_id' => 'required|exists:orders,id',
            'payment_status' => 'required|in:success,failed',
            'transaction_id'  => 'nullable|string'
        ]);

        if ($validation) return $validation;

        try {

            $order = Order::find($request->order_id);

            $transaction_id = $request->transaction_id ?? 'TXN-' . strtoupper(Str::random(10));

            $transactionData = [
                'type' => 3,
                'user_id' => $this->user->id,
                'order_id' => $order->id,
                'transaction' => 2, // 2 = debit
                'amount' => $order->total_price,
                'transaction_id' => $transaction_id,
                'status' => $request->payment_status
            ];

            Transaction::create($transactionData);

            if ($request->payment_status == 'success') {
                $order->status = 'completed';
                $order->transaction_id = $transaction_id;

                $serviceProviders = User::where('role', 2)->whereNotNull('device_token')->pluck('device_token')->toArray();

                // Save notification for each provider
                foreach ($serviceProviders as $token) {
                    Notification::create([
                        'user_id' => User::where('device_token', $token)->value('id'),
                        'title'   => 'New Booking Received!',
                        'messge' => "You have received a new booking (ID: {$order->booking_id}). Total Amount: ₹{$order->total_price}."
                    ]);
                }

                // Send push notification to multiple providers
                if (!empty($serviceProviders)) {
                    $title = 'New Booking Received!';
                    $message = "Booking ID: {$order->booking_id}, Amount: ₹{$order->total_price}.";
                    $this->notificationService->sendPushNotification($serviceProviders, $title, $message);
                }
            } else {
                $order->status = 'failed';
                $order->transaction_id = $transaction_id;
            }
            $order->save();

            $Responsedata = [
                'order_id'        => $order->id,
                'booking_id'      => $order->booking_id,
                'transaction_id'  => $transaction_id,
                'status'          => $order->status
            ];
            return $this->successResponse($Responsedata, $request->payment_status == 'success' ? 'Payment successful, order confirmed' : 'Payment failed, please try again');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function my_bookings()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id    = $this->user->id;
            $bookings = Order::with('subCategory:id,name')
                ->where('user_id', $user_id)
                ->orderBy('id', 'DESC')
                ->get();

            if ($bookings->isEmpty()) {
                return $this->errorResponse('No booking found', 404);
            }

            return $this->successResponse($bookings, 'Booking retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
}
