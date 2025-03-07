<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    protected function provider_register(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'name'       => 'required|string|max:255',
            'mobile_no'  => 'required|string|max:15|unique:users,mobile_no',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'country_id' => 'required|exists:countries,id',
            'state_id'   => 'required|exists:states,id',
            'city_id'    => 'required|exists:cities,id',
            'pincode'    => 'required|string|max:10',
            'address'    => 'required|string|max:500',
            'latitude'    => 'required',
            'longitude'    => 'required',
        ]);

        if ($validation) return $validation;

        try {

            $user = User::create([
                'role'          => 2, // Setting role as Provider (role 2)
                'name'          => $request->name,
                'mobile_no'     => $request->mobile_no,
                'email'         => $request->email,
                'password'      => Hash::make($request->password), // Hash the password
                'temp_password' => $request->password,
                'country_id'    => $request->country_id,
                'state_id'      => $request->state_id,
                'city_id'       => $request->city_id,
                'pincode'       => $request->pincode,
                'address'       => $request->address,
                'latitude'       => $request->latitude,
                'longitude'       => $request->longitude,
            ]);

            return $this->successResponse(
                $user->only(['id', 'name', 'mobile_no', 'email', 'country_id', 'state_id', 'city_id', 'pincode', 'address','latitude','longitude']),
                'Provider registered successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function provider_login(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'email'      => 'required|email',
            'password'   => 'required',
            'device_id'   => 'required',
            'device_type'   => 'required|in:android,ios,web',
            'device_model'   => 'required',
            'device_token'   => 'required',
            'ip_address'   => 'required',
        ]);

        if ($validation) return $validation;

        try {

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            // if ($user->device_id && $user->device_id !== $device_id) {
            //     return $this->errorResponse('User is already logged in from another device', 403);
            // }

            // $user->tokens()->delete(); // Delete old tokens to avoid duplication
            $token = $user->createToken('ApkaBudget')->plainTextToken;
            $user->update([
                'token'         => $token,
                'device_id'     => $request->device_id,
                'device_token'  => $request->device_token,
                'device_type'   => $request->device_type,
                'device_model'  => $request->device_model,
                'ip_address'  => $request->ip_address,
                'login_at'      => now()
            ]);

            return $this->successResponse([$user->only(['id', 'name', 'mobile_no', 'email', 'country_id', 'state_id', 'city_id', 'pincode', 'address', 'token', 'device_id', 'device_token', 'device_type', 'device_model', 'login_at'])], 'Provider login successful');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function user_login(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'mobile'      => 'required'
        ]);

        if ($validation) return $validation;

        try {
            $mobile = $request->mobile;
            $device_id = $request->device_id;

            $user = User::where('mobile_no', $mobile)->first();

            if (!$user) {
                $user = User::create([
                    'role' => 1,
                    'mobile_no' => $mobile
                ]);
            }

            $otp = rand(1000, 9999);
            $user->otp = $otp;
            $user->save();

            return $this->successResponse([$user->only(['id', 'mobile_no', 'otp'])], 'Otp send successful');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    protected function verify_otp(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'mobile' => 'required',
            'otp'    => 'required|digits:4',
            'device_id'   => 'required',
            'device_type'   => 'required|in:android,ios,web',
            'device_model'   => 'required',
            'device_token'   => 'required',
            'ip_address'   => 'required',
        ]);

        if ($validation) return $validation;

        try {
            $mobile = $request->mobile;
            $otp = $request->otp;
            $device_id = $request->device_id;
            $device_token = $request->device_token;

            $user = User::where('mobile_no', $mobile)->first();

            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }

            // OTP Check
            if ($user->otp != $otp) {
                return $this->errorResponse('Invalid OTP', 400);
            }

            // OTP verified successfully, generate auth token
            $token = $user->createToken('ApkaBudget')->plainTextToken;
            $user->token = $token;

            $user->device_id = $device_id;
            $user->device_token = $device_token;
            $user->device_type = $request->device_type;
            $user->device_model = $request->device_model;
            $user->ip_address = $request->ip_address;
            $user->login_at = now();

            // Clear OTP after successful verification
            $user->otp = null;
            $user->save();

            return $this->successResponse([$user->only(['id', 'mobile_no', 'token'])], 'OTP verified successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function logout(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->errorResponse('User not authenticated', 401);
            }
            // Clear device details and tokens
            $user->update([
                'device_id'    => null,
                'device_token' => null,
                'login_at'     => null,
                'logout_at'    => now(),
            ]);

            $user->tokens()->delete();

            return $this->successResponse('Logout successful');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function deleteAccount(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }

            $user->delete(); // Soft delete

            return $this->successResponse('Account has been successfully deleted');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
}
