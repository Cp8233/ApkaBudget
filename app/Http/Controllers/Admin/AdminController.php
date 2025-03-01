<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
protected function index(){
    $admin = Auth::guard('admin')->user();
    return view('Admin.dashboard',compact('admin'));
}
}
