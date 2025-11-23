<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Customer;
use App\Models\Tukang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TukangController extends Controller
{
    public function dashboard()
    {
        $tukang = Auth::guard('tukang')->user();

        if (!$tukang) {
            return redirect()->route('tukang.login');
        }

        $recentMessages = ChatMessage::where('receiver_type', 'App\Models\Tukang')
            ->where('receiver_id', $tukang->id)
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Count new messages
        $newMessagesCount = ChatMessage::where('receiver_type', 'App\Models\Tukang')
            ->where('receiver_id', $tukang->id)
            ->whereNull('read_at')
            ->count();

        return view('tukang.dashboard', compact('recentMessages', 'newMessagesCount'));
    }

    public function profile()
    {
        $tukang = Auth::guard('tukang')->user();
        return view('tukang.profile', compact('tukang'));
    }

    public function updateProfile(Request $request)
    {
        $tukang = Auth::guard('tukang')->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'specializations' => ['required', 'array', 'min:1'],
            'years_experience' => ['nullable', 'integer', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $tukang->update($request->only([
            'name', 'phone', 'address', 'city', 'postal_code',
            'specializations', 'years_experience', 'hourly_rate', 'description'
        ]));

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function jobs()
    {
        $tukang = Auth::guard('tukang')->user();

        // Get job requests (messages from customers)
        $jobRequests = ChatMessage::where('receiver_type', 'App\Models\Tukang')
            ->where('receiver_id', $tukang->id)
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('conversation_id')
            ->map(function ($conversationMessages) {
                return $conversationMessages->first();
            })
            ->values();

        return view('tukang.jobs', compact('jobRequests'));
    }

    public function toggleAvailability(Request $request)
    {
        $tukang = Auth::guard('tukang')->user();

        $tukang->update([
            'is_available' => !$tukang->is_available
        ]);

        return response()->json([
            'success' => true,
            'is_available' => $tukang->is_available
        ]);
    }
}
