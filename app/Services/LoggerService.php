<?php

namespace App\Services;

class LoggerService
{
    protected static $validFunctions = [
        'countries',
        'states',
        'cities',
        'provider_register',
        'provider_login',
        'user_login',
        'verify_otp',
        'logout',
        'deleteAccount',
        'identity_types',
        'payment_status',
        'dashboard',
        'bookings',
        'plans',
        'save_location',
        'sub_categories',
        'services',
        'rate_card',
        'addToCart',
        'add_address',
        'addresses',
        'getDailySlots',
        'checkout',
        'paymentstatus',
        'my_bookings',
        'categories',
        'notifications',
        'transaction_history',
        'profile',
        'edit_profile',
        'send_notification',
        'handleWebhook',
        'testPayment',
        'booking_detail',
        'booking_action'
    ];

    public static function isAllowed($method)
    {
        return in_array($method, self::$validFunctions);
    }
}
