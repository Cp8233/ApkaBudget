<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Models\Subscription;

class CommonController extends Controller
{
    protected function categories()
    {
        try {
            $categories = Category::where('status', 1)->select('id', 'category', 'image')->get();

            if ($categories->isEmpty()) {
                return $this->errorResponse('No categories found', 404);
            }

            $categories->transform(function ($category) {
                $category->image = url($category->image);
                return $category;
            });

            return $this->successResponse($categories, 'Categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function notifications()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $notifications = Notification::where('user_id', $this->user->id)->get();

            $notifications = $notifications->map(function ($notification) {
                $notification->time_ago = Carbon::parse($notification->created_at)->diffForHumans();
                return $notification;
            });

            return $this->successResponse($notifications, 'Notifications retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function transaction_history()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $transactions = Transaction::where('user_id', $this->user->id)
                ->orderBy('id', 'DESC')
                ->get(['id', 'type', 'transaction', 'amount', 'transaction_id', 'status', 'created_at']);

            // Format created_at for human-readable display
            $formattedTransactions = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'transaction' => $transaction->transaction,
                    'amount' => $transaction->amount,
                    'transaction_id' => $transaction->transaction_id,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->format('d M Y, h:i A'), // Proper formatting
                ];
            });

            return $this->successResponse($formattedTransactions, 'Transaaction retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function profile()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $userData = $this->user->role == 2
                ? array_merge(
                    $this->user->only(['id', 'name', 'mobile_no', 'email', 'category_id', 'experience', 'identity_id', 'identity_number', 'identity_image', 'country_id', 'state_id', 'city_id', 'pincode', 'address']),
                    [
                        'identity_image' => $this->user->identity_image ? url($this->user->identity_image) : null,
                        'security_added' => Subscription::hasActiveSecurity($this->user->id, 2),
                        'delivered_service' => 0
                    ]
                )
                : $this->user->only(['id', 'name', 'mobile_no', 'email']);


            return $this->successResponse($userData, 'Profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function edit_profile(Request $request)
    {
        $rules = $this->user->role == 2 ? [
            // 'name'            => 'required',
            // 'country_id'      => 'required',
            // 'state_id'        => 'required',
            // 'city_id'         => 'required',
            // 'pincode'         => 'required',
            // 'address'         => 'required',
            'category_id'     => 'required|exists:categories,id',
            'experience'      => 'required|integer|min:1',
            'identity_id'     => 'required|exists:identity_types,id',
            'identity_number' => 'required|string|max:50|unique:users,identity_number,' . auth()->id(),
            'identity_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ] : [
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ];

        $validation = $this->validateRequest($request, $rules);
        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            if ($request->hasFile('identity_image') && $this->user->role == 2) {
                $filename = 'identity_' . time() . '.' . $request->file('identity_image')->getClientOriginalExtension();
                $request->file('identity_image')->move(public_path('uploads/identities/'), $filename);
                $this->user->identity_image = 'uploads/identities/' . $filename;
            }

            $fields = $this->user->role == 2
                ? ['name', 'country_id', 'state_id', 'city_id', 'pincode', 'address', 'category_id', 'experience', 'identity_id', 'identity_number']
                : ['name', 'email'];

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $this->user->$field = $request->$field;
                }
            }

            $this->user->save();

            return $this->successResponse('Profile updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
}
