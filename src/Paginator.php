<?php

namespace Oilstone\Presenter;

use Illuminate\Pagination\AbstractPaginator;

/**
 * Class Paginator
 * @package Oilstone\Presenters
 */
class Paginator extends Collection
{
    /**
     * @var AbstractPaginator
     */
    protected $paginator;

    /**
     * Paginator constructor.
     * @param AbstractPaginator $paginator
     */
    public function __construct(AbstractPaginator $paginator)
    {
        parent::__construct($paginator->items());

        $this->paginator = $paginator;
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

        if (method_exists($this->paginator, $key)) {
            return $this->paginator->$key();
        }
    }

    /**
     * @param int $pageNo
     * @return string
     */
    public function url(int $pageNo): string
    {
        return $this->paginator->url($pageNo);
    }
}