<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'full_name' => $this->full_name,
            'description' => $this->description,
            'url' => $this->url,
            'topics' => $this->topics,
            'language' => new LanguageResource($this->language),
        ];

    }
}
