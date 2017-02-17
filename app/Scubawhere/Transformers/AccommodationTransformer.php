<?php

namespace Scubawhere\Transformers;

use Illuminate\Database\Eloquent\Model;

class AccommodationTransformer extends Transformer
{
    protected $priceTransformer;

    public function __construct(PriceTransformer $priceTransformer)
    {
        $this->priceTransformer = $priceTransformer;
    }

    /**
     * The need to merge base_prices and prices is that the eloquent relationships
     * are used througout the application so cannot be changed without alot of rewrite,
     * so to atleast allow the front end to be redesigned with only prices we merge them
     * when transforming
     * 
     * @param $accommodation
     * @return array
     */
    public function transform($accommodation)
    {
        if ($accommodation instanceof Model) {
            $accommodation = $accommodation->toArray();
        }
        return array(
            'id'          => $accommodation['id'],
            'name'        => $accommodation['name'],
            'description' => $accommodation['description'],
            'capacity'    => $accommodation['capacity'],
            'prices'      => $this->priceTransformer->transformMany($accommodation['prices']),
            'base_prices' => $this->priceTransformer->transformMany($accommodation['base_prices'])
        );
    }
}