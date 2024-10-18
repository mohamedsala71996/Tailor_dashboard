<?php

namespace App\Http\Controllers\dashboard;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Notifications\testNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\App;


class HomeController extends Controller
{
    public function index(){


        $countOfProducts = Product::get()->count();
        $countOfTailors = User::where('store_id', auth()->user()->store_id)->where('role','tailor')->count();   // Assuming you have a Tailor model

        return view('dashboard.home',compact('countOfProducts','countOfTailors'));
    }
}
