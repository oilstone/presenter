<?php

namespace Oilstone\Presenters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Traits\Macroable;

/**
 * Class Factory
 * @package Oilstone\Presenters
 */
class Factory
{
    use Macroable;

    /**
     * Create a presenter.
     *
     * @param mixed $presentable
     * @return mixed
     */
    public static function make($presentable)
    {
        if ($presentable instanceof Presenter) {
            return $presentable;
        }

        if ($presentable instanceof Collection) {
            return $presentable;
        }

        if ($presentable instanceof Model) {
            return self::model($presentable);
        }

        if ($presentable instanceof HasOne) {
            return self::model($presentable);
        }

        if ($presentable instanceof BaseCollection) {
            return self::collection($presentable);
        }

        if ($presentable instanceof AbstractPaginator) {
            return self::paginator($presentable);
        }

        if (is_array($presentable)) {
            return array_map(function ($item) {
                return self::make($item);
            }, $presentable);
        }

        return $presentable;
    }

    /**
     * @param $model
     * @return Eloquent
     */
    public static function model($model)
    {
        $modelMethod = camel_case(class_basename($model));

        if(is_callable("self::$modelMethod")) {
            return self::$modelMethod($model);
        }

        return new Eloquent($model);
    }

    /**
     * @param $collection
     * @return Collection
     */
    public static function collection($collection)
    {
        return new Collection($collection);
    }

    /**
     * @param $paginator
     * @return Paginator
     */
    public static function paginator($paginator)
    {
        return new Paginator($paginator);
    }
}