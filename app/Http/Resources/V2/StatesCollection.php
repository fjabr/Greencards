<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StatesCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id'      => (int) $data->id,
                    'country_id' => (int) $data->country_id,
                    'name' =>  $data->getTranslation('name'),
                    'name_ar' => $data->getTranslation('name','ar'),
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
