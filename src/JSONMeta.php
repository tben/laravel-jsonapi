<?php

namespace Tben\LaravelJsonAPI;

class JsonMeta
{
    private $array = [];

    public function addMetaNotation(string $key, $value)
    {
        data_fill($this->array, $key, $value);
    }

    public function addArray(array $array)
    {
        $this->array = array_merge($this->array, $array);
    }

    public function viewMetaAll()
    {
        if (empty($this->array)) {
            return null;
        }

        return $this->array;
    }
}
