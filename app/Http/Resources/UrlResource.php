<?php

namespace App\Http\Resources;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Url
 */
class UrlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'destination_url' => $this->destination_url,
            'url_key' => $this->url_key,
            'default_short_url' => $this->default_short_url,
            'visitors_count' => $this->when($request->routeIs('urls.index'), function () {
                return $this->visitors_count ?? 0;
            }),
            'status' => !!$this->deactivated_at ? 'deactivated' : 'active',
            'user' => $this->when($request->routeIs('urls.show'), function () {
                return new UserResource($this->user);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
