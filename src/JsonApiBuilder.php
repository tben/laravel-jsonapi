<?php

namespace Tben\LaravelJsonAPI;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Undocumented class
 */
class JsonApiBuilder
{
    private Builder $query;
    private $request;
    private array $search = [];

    public function __construct(
        Model $model,
        private array $override = []
    ) {
        $this->query = $model->query();
        $this->request = app(Request::class);
    }

    public static function for(string|object $model, array $override = []): self
    {
        if (is_string($model)) {
            $model = new $model();
        }

        return new self($model, $override);
    }

    public function defaultSort(string $field, string $asc = 'ASC'): self
    {
        return $this;
    }

    public function field(string $key, bool $searchable = false, bool $sortable = false): self
    {
        $this->query->addSelect($key);

        if ($searchable && $this->request->has('filter.' . $key)) {
            $value = $this->request->input('filter.' . $key);

            if (is_string($value) || is_numeric($value) || $value == null) {
                $this->query->where($key, $value);
            }

            if (is_array($value)) {
                $this->query->whereIn($key, $value);
            }
        }

        if ($sortable) {
            $this->search[] = $key;
        }

        return $this;
    }

    public function relationship(string $name, array $fields): self
    {
        return $this;
    }

    public function finalize()
    {
        
    }

    public function pagination()
    {
        $this->finalize();

        $size = $this->request->input('page.size', 30);

        if ($this->request->has('page.cursor')) {
            return $this->query->cursorPaginate(
                perPage: $size,
                cursor: $this->request->input('page.cursor')
            );
        }

        $number = $this->request->input('page.number', 1);

        if ($this->request->input('page.type') == 'simple') {
            return $this->query->simplePaginate(perPage: $size, page: $number);
        }

        return $this->query->paginate(perPage: $size, page: $number);
    }

    public function get()
    {
        $this->finalize();

        return $this->query->get();
    }

    public function __call($name, $arguments)
    {
        $this->query->{$name}(...$arguments);
        return $this;
    }
}
