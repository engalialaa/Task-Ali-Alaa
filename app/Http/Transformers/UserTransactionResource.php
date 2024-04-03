<?php

namespace App\Http\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;


class UserTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'wallet_balance'=>$this->wallet_balance,
            'operation'=>$this->operation,
            'end_wallet_balance'=>$this->end_wallet_balance,
            'admin_commission'=>$this->admin_commission,
            'created_at'=>$this->created_at->format("Y-m-d H:i:s"),
        ];
    }
}
