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
        $this->items = $items instanceof Collection ? $items->toArray() : $items;
        $this->transformer = $transformer;
    }

    public function transformer():array
    {
        $transformer = $this->transformer;

        return array_map(function ($item) use ($transformer) {
            return $transformer->format($item);
        }, $this->items);
    }

    public function count()
    {
        return count($this->items);
    }
}