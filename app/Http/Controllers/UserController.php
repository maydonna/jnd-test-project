<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $sortKey = $request->query('sort_key', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');
        $keyword = $request->query('keyword') ?? null;

        //get only user, exclude admin
        $users = User::whereNull('role')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($keyword).'%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($keyword).'%']);
            })
            ->orderBy($sortKey, $sortOrder)
            ->paginate($perPage);

        return UserResource::collection($users);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
