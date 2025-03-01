<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Traits\ApiResponseTrait;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponseTrait;
    
    protected $user;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->user = Auth::guard('sanctum')->user();
        $this->notificationService = $notificationService;
    }
}
