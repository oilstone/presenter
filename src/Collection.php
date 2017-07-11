<?php

namespace Oilstone\Presenter;

use ArrayIterator;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;

/**
 * Class Collection
 * @package Oilstone\Presenters
 */
class Collection extends BaseCollection
{
    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return isset($this->items[$key]) ? Factory::make($this->items[$key]) : null;
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[] = Factory::make($item);
        }

        return new ArrayIterator($items);
    }

    /**
     * Dynamically call a presenter function via property syntax.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (method_exists($this, $key)) {
            return $this->$key();
        }

        if(array_key_exists($key, $this->items)) {
            return Factory::make($this->items[$key]);
        }

        if(array_key_exists(Str::snake($key), $this->items)) {
            return Factory::make($this->items[Str::snake($key)]);
        }
    }

    /**
     * Determine if an attribute exists.
     *
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return method_exists($this, $key);
    }

    /**
     * Convert the collection instance to JSON.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the collection to it's string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Get the collection as a plain array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[] = Factory::make($item)->toArray();
        }

        return $items;
    }
}
