<?php

namespace Scubawhere\Transformers;

class PriceTransformer extends Transformer
{

    /**
     * Transform an entity by removing unwanted data before returned as JSON
     *
     * @param $price
     * @return mixed
     */
    public function transform($price)
    {
        return array(
            'id'            => $price['id'],
            'price'         => $price['price'],
            'from'          => $price['from'],
            'until'         => $price['until'],
            'decimal_price' => $price['decimal_price']
        );
    }
}