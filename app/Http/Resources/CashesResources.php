<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CashesResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'when' => $this->when->addHour(7)->format('d F Y H:i'),
            'amount' => formatPrice(abs($this->amount)),
            'created_at' => $this->created_at->format('d F Y H:m'),
            'updated_at' => $this->updated_at->format('d F Y H:m'),
            'isCredit' => ($this->amount < 0) ? true : false,
        ];
    }
}
