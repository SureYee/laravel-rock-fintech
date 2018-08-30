<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-28
 * Time: 18:50
 */

namespace Sureyee\LaravelRockFinTech\Requests;


use Illuminate\Support\Collection;
use Sureyee\LaravelRockFinTech\Contracts\TransformerInterface;

class ItemsRequest
{
    protected $items;

    protected $transformer;

    public function __construct($items, TransformerInterface $transformer)
    {
        $this->items = $items instanceof Collection ? $items : (new Collection($items));
        $this->transformer = $transformer;
    }

    public function transformer():array
    {
        $transformer = $this->transformer;

        return $this->items->map(function ($item) use ($transformer) {
            return $transformer->format($item);
        })->toArray();
    }

    public function count()
    {
        return count($this->items);
    }
}