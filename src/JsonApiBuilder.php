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
    private array $allowedSort = [];

    public function __construct(
        Model $model,
        private array $override = []
    ) {
        $this->query = $model->query();
        $this->request = app(Request::class);
    }

    /**
     * Create a new instance of JsonApiBuilder for the given model.
     *
     * @template T of Model
     * @param class-string<T>|Model $model
     * @param array $override
     * @return static<T>
     */
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

    public function field(
        string $column,
        bool $searchable = false,
        bool $sortable = false,
        bool $filled = false,
        int $priority = 0,
    ): self {
        $this->query->addSelect($column);

        if ($searchable) {
            $filter = sprintf('filter.%s', $column);
            if ($this->request->{$filled ? 'filled' : 'has'}($filter)) {
                $value = $this->request->input($filter);

                if (is_string($value) || is_numeric($value) || $value == null) {
                    $this->query->where($column, $value);
                }

                if (is_array($value)) {
                    $this->query->whereIn($column, $value);
                }
            }
        }

        if ($sortable) {
            $this->allowedSort[] = $column;
        }

        return $this;
    }

    public function relationship(JsonApiRelationship $relationship): self
    {
        return $this;
    }

    public function relationships(JsonApiRelationship ...$relationships): self
    {
        foreach ($relationships as $relationship) {
            $this->relationship($relationship);
        }

        return $this;
    }

    public function finalize()
    {
        if ($this->request->filled('sort')) {
            $sorts = explode(',', $this->request->input('sort'));

            foreach ($sorts as $sort) {
                $direction = 'ASC';
                if (str_starts_with($sort, '-')) {
                    $direction = 'DESC';
                    $sort = substr($sort, 1);
                }

                if (in_array($sort, $this->allowedSort)) {
                    $this->query->orderBy($sort, $direction);
                }
            }
        }
    }

    public function paginate()
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

    public function simplePaginate()
    {
        return $this->paginate();
    }

    public function cursorPaginate() 
    {
        return $this->paginate();
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
