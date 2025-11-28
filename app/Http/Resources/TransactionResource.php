<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'account_id' => $this->account_id,
            'category_id' => $this->category_id,
            'transaction_date' => $this->transaction_date?->toDateString(),
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'description' => $this->description,
            'account' => [
                'id' => $this->account?->id,
                'name' => $this->account?->name,
            ],
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],
        ];
    }
}
