<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ttl = 60 * 60; // 1 hour
        $data = Cache::remember('dashboard_data', $ttl, function () {
            return [
                'users_count' => User::whereNot('role', 'admin')->count(),
                'urls_count' => Url::query()->count()
            ];
        });

        return response()->json($data);
    }
}
