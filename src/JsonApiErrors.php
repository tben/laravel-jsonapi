<?php

namespace Tben\LaravelJsonAPI;

use ArrayAccess;
use ArrayObject;
use InvalidArgumentException;

class JsonApiErrors extends ArrayObject implements ArrayAccess
{
    protected $errors = [];

    /**
     * Undocumented function
     *
     * @param mixed $errors
     */
    public function __construct($errors = null)
    {
        if ($errors == null) {
            $errors = [];
        } elseif (! is_array($errors)) {
            $errors = func_get_args();
        }

        foreach ($errors as $error) {
            $this[] = $error;
        }
    }

    /**
     * Undocumented function
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->errors[$offset]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->errors[$offset] ?? null;
    }

    /**
     * Undocumented function
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        if (! $value instanceof JsonSingleError) {
            throw new InvalidArgumentException('Invalid type');
        }

        if ($key===null) {
            $this->errors[] = $value;
        } else {
            $this->errors[$key] = $value;
        }
    }

    /**
     * Undocumented function
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->errors[$offset]);
    }

    /**
     * Undocumented function
     *
     * @param callable $cb
     * @return self
     */
    public function each(callable $cb): self
    {
		foreach ($this as $key => $value) {
            $cb($value, $key, $this);
        }
    
		return $this;
	}

    /**
     * Undocumented function
     *
     * @param callable $cb
     * @return array
     */
    public function map(callable $cb): array
    {
        return array_map(fn($item) => $cb($item), $this->errors);
	}


    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'errors' => $this->map(function ($item) {
                return $item->toArray();
            }),
        ];
    }
}