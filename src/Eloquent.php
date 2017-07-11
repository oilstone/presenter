<?php

namespace Oilstone\Presenters;

use Oilstone\Support\Arr as ArrayHelper;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Class Eloquent
 * @package Oilstone\Presenters
 */
class Eloquent extends Presenter
{
    /**
     * Get the presenter as a plain array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = $this->transformAttributes($this->presentable->getAttributes());

        foreach ($this->presentable->getRelations() as $key => $relation) {

            $array[$key] = Factory::make($relation)->toArray();
        }

        return $array;
    }

    /**
     * Transform array keys to the convention expected by the presentable
     * object and transform values using available presenter functions.
     *
     * @param array $array
     * @return array
     */
    protected function transformAttributes(array $array): array
    {
        if (!$this->conventionsMatch()) {

            $staticCall = 'keysTo' . ucfirst($this->accessorCase) . 'Case';

            $array = ArrayHelper::$staticCall($array);
        }

        foreach ($array as $key => &$value) {

            if (method_exists($this, $key)) {

                $value = $this->$key();
            }
        }

        unset($value);

        return $array;
    }

    /**
     * Format long form dates.
     *
     * @param $date
     * @return string
     */
    public function longDate($date): string
    {
        return (new Carbon($date))->format("j F Y");
    }

    /**
     * Get the property.
     *
     * @param string $key
     * @return mixed
     */
    protected function get(string $key)
    {
        if (($value = $this->presentable->$key) !== null) {
            return Factory::make($value);
        }

        if ($value = $this->presentable->getRelationValue(Str::camel($key))) {

            return Factory::make($value);
        }

        return null;
    }

    /**
     * Format the created at timestamp.
     *
     * @return string
     */
    protected function createdAt(): string
    {
        return $this->date($this->presentable->created_at);
    }

    /**
     * Format date.
     *
     * @param $date
     * @return string
     */
    public function date($date): string
    {
        return (new Carbon($date))->format("j M Y");
    }

    /**
     * Format the updated at timestamp.
     *
     * @return string
     */
    protected function updatedAt(): string
    {
        return $this->date($this->presentable->updated_at);
    }
}
