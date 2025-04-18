<?php

namespace App\Http\Controllers;

use App\Http\Resources\UrlResource;
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
            $urlQuery = Url::query();
            return [
                'users_count' => User::whereNot('role', 'admin')->count(),
                'urls_count' => $urlQuery->count(),
                'latest_urls' => UrlResource::collection($urlQuery->latest()->take(5)->get()),
            ];
        });

        return response()->json($data);
    }
}
