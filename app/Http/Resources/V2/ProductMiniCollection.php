<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductMiniCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {


                return [
                    'id' => $data->id,
                    'name' => $data->getTranslation('name'),
                    'name_ar' => $data->getTranslation('name',"ar"),
                    'thumbnail_image' => uploaded_asset($data->thumbnail_img),
                    'has_discount' => home_base_price($data, false) != home_discounted_base_price($data, false) ,
                    'discount'=>"-".discount_in_percentage($data)."%",
                    'green_price'=>$data->green_price==null? $data->green_price:number_format($data->green_price, get_setting('no_of_decimals')) ,
                    'stroked_price' => home_base_price($data),
                    'correct_price' => number_format(home_discounted_base_price($data,false), get_setting('no_of_decimals')),
                    'main_price' => home_discounted_base_price($data),
                    'rating' => (double) $data->rating,
                    'sales' => (integer) $data->num_of_sale,
                    'links' => [
                        'details' => route('products.show', $data->id),
                    ]
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
