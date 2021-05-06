<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $out = parent::toArray($request);
        $out['author'] = $this->author->name;
        $out['image'] = Storage::url($out['image']) ?? env('DEFAULT_POST_IMAGE');
        return $out;
    }
}
