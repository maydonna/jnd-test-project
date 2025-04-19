<?php

namespace App\Http\Resources;

use AshAllenDesign\ShortURL\Models\ShortURLVisit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ShortURLVisit
 */
class VisitorResource extends JsonResource
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
            'short_url_id' => $this->short_url_id,
            'browser' => $this->browser,
            'device_type' => $this->device_type,
            'ip_address' => $this->ip_address,
            'operating_system' => $this->operating_system,
            'visited_at' => $this->visited_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
