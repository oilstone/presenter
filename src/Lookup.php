<?php

namespace Oilstone\Presenter;

/**
 * Class Lookup
 * @package Oilstone\Presenters
 */
class Lookup extends Collection
{
    /**
     * @var array
     */
    protected $lookup = [];

    /**
     * @param mixed $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        foreach ($this->items as $item) {
            if (isset($item->lookup_key)) {
                $this->lookup[self::convertKey($item->lookup_key)] = $item;
            }
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    protected static function convertKey($key)
    {
        return str_replace('_', '-', snake_case($key));
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $hyphenedKey = self::convertKey($key);

        if (array_key_exists($hyphenedKey, $this->lookup)) {

            return Factory::make($this->lookup[$hyphenedKey]);
        }

        return parent::__get($key);
    }
}
