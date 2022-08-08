<?php

namespace App\Http\Controllers;


use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Total Number of Messages
        $total_message = Message::count();

        // Total Number of Users
        $total_user = User::query()->where('is_admin', 0)->count();

        // Latest Message
        $latest_message = Message::query()->latest('created_at')->first();

        // Latest Registered User
        $latest_user = User::query()->where('is_admin', 0)->latest('created_at')->first();

       

        return [
            'totalMessages' => $total_message,
            'latestMessage' => $latest_message ? : null,
            'totalUsers' => $total_user,
            'latestUser' => $latest_user
        ];
    }
}
