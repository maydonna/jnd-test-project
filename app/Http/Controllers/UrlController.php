<?php

namespace App\Http\Controllers;

use App\Http\Resources\UrlResource;
use App\Http\Resources\VisitorResource;
use App\Models\Url;
use AshAllenDesign\ShortURL\Classes\Builder;
use AshAllenDesign\ShortURL\Exceptions\ShortURLException;
use AshAllenDesign\ShortURL\Models\ShortURL;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class UrlController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        $perPage = $request->query('per_page', 5); //for publicly use 5
        $sortKey = $request->query('sort_key', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        $keyword = $request->query('keyword') ?? null;
        $status = $request->query('status') ?? null;

        if($userId === 0) {
            return response()->noContent();
        }

        $urls = Url::with('user')
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->when($status, function ($query) use ($status) {
                return $status === 'active' ? $query->whereNull('deactivated_at') : $query->whereNotNull('deactivated_at');
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->whereRaw('LOWER(destination_url) LIKE ?', ['%'.strtolower($keyword).'%']);
            })
            ->withCount('visitors')
            ->orderBy($sortKey, $sortOrder)
            ->paginate($perPage);

        return UrlResource::collection($urls);
    }

    /**
     * @throws ShortURLException
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'url' => ['required', 'url'],
        ]);

        $shortURLObject = app(Builder::class)
            ->destinationUrl($data['url'])
            ->beforeCreate(function (ShortURL $model) use ($request): void {
                $model->user_id = $request->user()->id;
            })
            ->make();

        return response()->json([
            'short_url' => $shortURLObject->default_short_url,
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Url $url)
    {
        $url->load('user');

        return new UrlResource($url);
    }

    public function update(Url $url): UrlResource
    {
        Gate::authorize('update', $url);

        $url->deactivated_at = !!$url->deactivated_at ? null : now();
        $url->save();

        return new UrlResource($url);
    }

    public function destroy(Url $url)
    {
        Gate::authorize('delete', $url);

        $url->delete();

        return response()->noContent();
    }

    public function visitors(Request $request, Url $url)
    {
        $sortKey = $request->query('sort_key', 'visited_at');
        $sortOrder = $request->query('sort_order', 'desc');
        $keyword = $request->query('keyword') ?? null;

        $visitors = $url->visitors()
            ->when($keyword, function ($query) use ($keyword) {
                return $query->whereRaw('LOWER(ip_address) LIKE ?', ['%'.strtolower($keyword).'%'])
                    ->orWhereRaw('LOWER(browser) LIKE ?', ['%'.strtolower($keyword).'%'])
                    ->orWhereRaw('LOWER(operating_system) LIKE ?', ['%'.strtolower($keyword).'%'])
                    ->orWhereRaw('LOWER(device_type) LIKE ?', ['%'.strtolower($keyword).'%']);
            })
            ->orderBy($sortKey, $sortOrder)
            ->paginate(10);

        return VisitorResource::collection($visitors);
    }
}
