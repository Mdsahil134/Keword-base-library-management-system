<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'seconds' => ['required', 'integer', 'min:1', 'max:3600'],
        ]);

        $user = $request->user();
        $user->increment('time_spent', $validated['seconds']);

        return response()->json(['ok' => true]);
    }
}
