<?php

namespace Domain\BookStore\Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource)) return [];
        return [
            'id' => $this->id,
            'name' => $this->name,
            'isbn' => $this->isbn,
            'value' => $this->value,
        ];
    }
}
