<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function terms_conditions(){
        return view('Website.terms_condition');
    }
    public function contact_us(){
     return view('Website.contact_us');  
    }
    public function refund_policy(){
     return view('Website.refund_policy');   
    }
    public function privacy_policy(){
     return view('Website.privacy_policy');   
    }
}
