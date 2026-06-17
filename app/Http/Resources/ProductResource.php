<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->_id,
            'code' => $this->code,
            'name' => $this->name,
            'brand' => $this->brand,
            'price' => (float) $this->price,
            // 👈 Formato del PDF: DD/MM/YYYY HH:MM
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y H:i') : null,
        ];
    }
}