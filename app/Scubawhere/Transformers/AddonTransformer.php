<?php

namespace Scubawhere\Transformers;

use Illuminate\Database\Eloquent\Model;

class AddonTransformer extends Transformer
{
    protected $priceTransformer;

    public function __construct(PriceTransformer $priceTransformer)
    {
        $this->priceTransformer = $priceTransformer;
    }

    /**
     * The need to merge base_prices and prices is that the eloquent relationships
     * are used throughout the application so cannot be changed without a lot of rewrite,
     * so to at least allow the front end to be redesigned with only prices we merge them
     * when transforming
     *
     * @param $addon
     * @return array
     */
    public function transform($addon)
    {
        if ($addon instanceof Model) {
            $addon = $addon->toArray();
        }
        return array(
            'id'          => $addon['id'],
            'name'        => $addon['name'],
            'description' => $addon['description'],
            'base_prices'      => $this->priceTransformer->transformMany($addon['base_prices']),
        );
    }
}
