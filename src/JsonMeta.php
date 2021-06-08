<?php

namespace Tben\LaravelJsonAPI;

class JsonMeta
{
    private $array = [];

    /**
     * Add values using dot nototions
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addMetaNotation(string $key, $value)
    {
        data_set($this->array, $key, $value);
    }

    /**
     * Array merge values into arrays
     *
     * @param array $array
     * @return void
     */
    public function addArray(array $array)
    {
        $this->array = array_merge($this->array, $array);
    }

    /**
     * Output data helds in the jsonmeta array
     *
     * @return null|array
     */
    public function viewMetaAll()
    {
        if (empty($this->array)) {
            return null;
        }

        return $this->array;
    }
}
