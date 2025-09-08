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
    private array $allowedIncludes = [];

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
                    $actioned = false;
                    if (isset($value['$gte'])) {
                        if (is_numeric($value['$gte'])) {
                            $this->query->where($column, '>=', $value['$gte']);
                        } else {
                            throw new \InvalidArgumentException("Invalid filter value for '$column'");
                        }

                        unset($value['$gte']);
                    }
                    if (isset($value['$gt'])) {
                        if (is_numeric($value['$gt'])) {
                            $this->query->where($column, '>', $value['$gt']);
                        } else {
                            throw new \InvalidArgumentException("Invalid filter value for '$column'");
                        }
                        unset($value['$gt']);
                    }
                    if (isset($value['$lt'])) {
                        if (is_numeric($value['$lt'])) {
                            $this->query->where($column, '<', $value['$lt']);
                        } else {
                            throw new \InvalidArgumentException("Invalid filter value for '$column'");
                        }
                        unset($value['$lt']);
                    }
                    if (isset($value['$lte'])) {
                        if (is_numeric($value['$lte'])) {
                            $this->query->where($column, '<=', $value['$lte']);
                        } else {
                            throw new \InvalidArgumentException("Invalid filter value for '$column'");
                        }
                        unset($value['$lte']);
                    }
                    if (isset($value['$in'])) {
                        if (is_array($value['$in'])) {
                            $this->query->whereIn($column, $value['$in']);
                        } else {
                            throw new \InvalidArgumentException("Invalid filter value for '$column'");
                        }
                        unset($value['$in']);
                    }
                    if (isset($value['$nin'])) {
                        if (is_array($value['$nin'])) {
                            $this->query->whereNotIn($column, $value['$nin']);
                        } else {
                            throw new \InvalidArgumentException("Invalid filter value for '$column'");
                        }
                        unset($value['$nin']);
                    }

                    if (! empty($value)) {
                        $this->query->whereIn($column, $value); 
                    }
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
        $this->allowedIncludes[$relationship->getRelationshipName()] = $relationship;

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

        if ($this->request->filled('include')) {
            $includes = explode(',', $this->request->input('include'));

            $validIncludes = [];
            foreach ($includes as $include) {
                if (array_key_exists($include, $this->allowedIncludes)) {
                    $validIncludes[] = $include;
                }
            }

            if (! empty($validIncludes)) {
                $this->query->with($validIncludes);
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
