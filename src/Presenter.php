<?php

namespace Oilstone\Presenter;

/**
 * Class Presenters
 * @package Oilstone\Presenters
 */
abstract class Presenter
{
    /**
     * The object to be presented.
     *
     * @var string
     */
    protected $presentable;

    /**
     * The case convention used by the client code.
     *
     * @var string
     */
    protected $accessorCase = 'camel';

    /**
     * The case convention used by the presentable object.
     *
     * @var string
     */
    protected $presentableCase = 'snake';

    /**
     * Create a new Presenters instance.
     *
     * @param mixed $presentable
     */
    public function __construct($presentable)
    {
        $this->presentable = $presentable;
    }

    /**
     * Dynamically retrieve attributes.
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        if (method_exists($this, $key)) {
            return $this->$key();
        }

        $key = $this->transformAccessor($key);

        return $this->get($key);
    }

    /**
     * Transform the accessor to the convention expected by the presentable object.
     *
     * @param $accessor
     * @return string
     */
    protected function transformAccessor($accessor)
    {
        if ($this->conventionsMatch()) {

            return $accessor;
        }

        return call_user_func($this->presentableCase . '_case', $accessor);
    }

    /**
     * Check whether we have a match between our accessor and our presenter conventions.
     *
     * @return bool
     */
    protected function conventionsMatch()
    {
        return ($this->accessorCase == $this->presentableCase);
    }

    /**
     * Get the property.
     *
     * @param $accessor
     * @return mixed
     */
    abstract protected function get(string $accessor);

    /**
     * Determine if an attribute exists.
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        if (method_exists($this, $key)) {
            return TRUE;
        }

        $key = $this->transformAccessor($key);

        return ($this->get($key) !== null);
    }

    /**
     * Get the presentable object.
     *
     * @return mixed
     */
    public function getPresentable()
    {
        return $this->presentable;
    }

    /**
     * Get the presenter as a plain array.
     *
     * @return array
     */
    abstract public function toArray();

    /**
     * Convert the presenter to it's string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert the model instance to JSON.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->presentable->toArray());
    }
}
