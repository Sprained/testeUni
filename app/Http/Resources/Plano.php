<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Plano extends JsonResource
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
            'id' => $this['id'],
            'name' => $this['name'],
            'days' => $this['days'],
            'payment_methods' => $this['payment_methods'],
            'trial_days' => $this['trial_days'],
            'amount' => $this['amount']
        ];
    }
}
