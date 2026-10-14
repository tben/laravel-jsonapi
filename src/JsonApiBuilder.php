<?php

namespace Tben\LaravelJsonAPI;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use InvalidArgumentException;

class JsonApiBuilder
{
    private Builder $query;
    private $request;
    private array $allowedSort = [];
    private array $allowedIncludes = [];
    private array $allowedFilters = [];
    private array $allowedScopes = [];
    private array $fieldOperators = [];
    private ?string $defaultSortField = null;
    private string $defaultSortDirection = 'ASC';
    private array $selectedColumns = [];

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
        $this->defaultSortField = $field;
        $this->defaultSortDirection = strtoupper($asc);
        return $this;
    }

    public function field(
        string $column,
        bool $searchable = false,
        bool $sortable = false,
        bool $filled = false,
        int $priority = 0,
        ?array $allowedOperators = null,
    ): self {
        $this->selectedColumns[] = $column;

        if ($searchable) {
            $this->allowedFilters[] = $column;
            
            if ($allowedOperators !== null) {
                $this->fieldOperators[$column] = $allowedOperators;
            }

            $filterKey = sprintf('filter.%s', $column);
            if ($this->request->has($filterKey)) {
                $value = $this->request->input($filterKey);
                $this->applyFieldFilter($this->query, $column, $value);
            }
        }

        if ($sortable) {
            $this->allowedSort[] = $column;
        }

        return $this;
    }

    public function scope(string $name): self
    {
        $this->allowedScopes[] = $name;
        
        $filterKey = sprintf('filter.%s', $name);
        if ($this->request->has($filterKey)) {
            $value = $this->request->input($filterKey);
            $this->query->{$name}($value);
        }

        return $this;
    }

    protected function applyFieldFilter(Builder $query, string $column, mixed $value): void
    {
        if (!in_array($column, $this->allowedFilters)) {
            throw new InvalidArgumentException("Filtering by column '$column' is not allowed.");
        }

        if (is_string($value) || is_numeric($value) || is_null($value)) {
            if (isset($this->fieldOperators[$column]) && !in_array('$eq', $this->fieldOperators[$column])) {
                throw new InvalidArgumentException("Exact matching (=) is not allowed for field '$column'.");
            }
            $query->where($column, $value);
            return;
        }

        if (is_array($value)) {
            $operators = [
                '$eq' => '=',
                '$neq' => '!=',
                '$ne' => '!=',
                '$gt' => '>',
                '$gte' => '>=',
                '$lt' => '<',
                '$lte' => '<=',
            ];

            foreach ($operators as $op => $sqlOp) {
                if (array_key_exists($op, $value)) {
                    if (isset($this->fieldOperators[$column]) && !in_array($op, $this->fieldOperators[$column])) {
                        throw new InvalidArgumentException("Operator '$op' is not allowed for field '$column'.");
                    }

                    $val = $value[$op];
                    // Automatically cast numeric query strings for safe DB comparisons
                    if (in_array($op, ['$gt', '$gte', '$lt', '$lte']) && is_numeric($val)) {
                        $val = $val + 0;
                    }

                    $query->where($column, $sqlOp, $val);
                    unset($value[$op]);
                }
            }

            // $contains: Both-side wildcard match (%term%)
            if (array_key_exists('$contains', $value)) {
                if (isset($this->fieldOperators[$column]) && !in_array('$contains', $this->fieldOperators[$column])) {
                    throw new InvalidArgumentException("Operator '\$contains' is not allowed for field '$column'.");
                }
                $query->where($column, 'LIKE', '%' . $value['$contains'] . '%');
                unset($value['$contains']);
            }

            // $begin_with: Starts-with match (term%)
            if (array_key_exists('$begin_with', $value)) {
                if (isset($this->fieldOperators[$column]) && !in_array('$begin_with', $this->fieldOperators[$column])) {
                    throw new InvalidArgumentException("Operator '\$begin_with' is not allowed for field '$column'.");
                }
                $query->where($column, 'LIKE', $value['$begin_with'] . '%');
                unset($value['$begin_with']);
            }

            // $end_with: Ends-with match (%term)
            if (array_key_exists('$end_with', $value)) {
                if (isset($this->fieldOperators[$column]) && !in_array('$end_with', $this->fieldOperators[$column])) {
                    throw new InvalidArgumentException("Operator '\$end_with' is not allowed for field '$column'.");
                }
                $query->where($column, 'LIKE', '%' . $value['$end_with']);
                unset($value['$end_with']);
            }

            // $in: Strict array match with no null values allowed
            if (isset($value['$in'])) {
                if (isset($this->fieldOperators[$column]) && !in_array('$in', $this->fieldOperators[$column])) {
                    throw new InvalidArgumentException("Operator '\$in' is not allowed for field '$column'.");
                }
                if (is_array($value['$in'])) {
                    foreach ($value['$in'] as $item) {
                        if (is_null($item)) {
                            throw new InvalidArgumentException("Null values are not permitted in '\$in' filter for field '$column'.");
                        }
                    }
                    $query->whereIn($column, $value['$in']);
                } else {
                    throw new InvalidArgumentException("Invalid filter value for '$column'");
                }
                unset($value['$in']);
            }

            // $nin: Strict array exclusion with no null values allowed
            if (isset($value['$nin'])) {
                if (isset($this->fieldOperators[$column]) && !in_array('$nin', $this->fieldOperators[$column])) {
                    throw new InvalidArgumentException("Operator '\$nin' is not allowed for field '$column'.");
                }
                if (is_array($value['$nin'])) {
                    foreach ($value['$nin'] as $item) {
                        if (is_null($item)) {
                            throw new InvalidArgumentException("Null values are not permitted in '\$nin' filter for field '$column'.");
                        }
                    }
                    $query->whereNotIn($column, $value['$nin']);
                } else {
                    throw new InvalidArgumentException("Invalid filter value for '$column'");
                }
                unset($value['$nin']);
            }

            if (!empty($value)) {
                if (isset($this->fieldOperators[$column]) && !in_array('$in', $this->fieldOperators[$column])) {
                    throw new InvalidArgumentException("Array matching is not allowed for field '$column'.");
                }
                foreach ($value as $item) {
                    if (is_null($item)) {
                        throw new InvalidArgumentException("Null values are not permitted in array filters for field '$column'.");
                    }
                }
                $query->whereIn($column, $value);
            }
        }
    }

    /**
     * Parse condition filters with depth guard protection against stack overflow / DoS
     */
    protected function parseConditionFilter(array $filters, Builder $query, int $depth = 0): void
    {
        if ($depth > 5) {
            throw new InvalidArgumentException("Filter nesting depth exceeds the maximum allowed limit of 5.");
        }

        foreach ($filters as $key => $value) {
            if ($key === '$and' && is_array($value)) {
                $query->where(function ($q) use ($value, $depth) {
                    foreach ($value as $condition) {
                        $q->where(function ($subQ) use ($condition, $depth) {
                            $this->parseConditionFilter($condition, $subQ, $depth + 1);
                        });
                    }
                });
            } elseif ($key === '$or' && is_array($value)) {
                $query->orWhere(function ($q) use ($value) {
                    foreach ($value as $condition) {
                        $q->where(function ($subQ) use ($condition, $depth) {
                            $this->parseConditionFilter($condition, $subQ, $depth + 1);
                        });
                    }
                });
            } elseif (in_array($key, $this->allowedScopes)) {
                $query->{$key}($value);
            } else {
                $this->applyFieldFilter($query, $key, $value);
            }
        }
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
        // 1. Handle Sparse Fieldsets
        // Only when fields were declared. Otherwise select([]) would select no columns at all and replace any select
        // the caller made on the query
        if (!empty($this->selectedColumns)) {
            $model = $this->query->getModel();
            $requestedFields = $this->request->input("fields.{$model->getTable()}");
            $validFields = $requestedFields
                ? array_values(array_intersect(explode(',', $requestedFields), $this->selectedColumns))
                : [];

            if (!empty($validFields)) {
                // Models cannot be serialised without their key
                $this->query->select(array_values(array_unique([$model->getKeyName(), ...$validFields])));
            } else {
                $this->query->select($this->selectedColumns);
            }
        }

        // 2. Handle Advanced Condition Filters
        $globalFilter = $this->request->input('filter');
        if (is_string($globalFilter)) {
            $decoded = json_decode($globalFilter, true);
            if (is_array($decoded)) {
                $this->parseConditionFilter($decoded, $this->query);
            }
        } elseif (is_array($globalFilter) && (isset($globalFilter['$and']) || isset($globalFilter['$or']))) {
            $this->parseConditionFilter($globalFilter, $this->query);
        }

        // 3. Handle Sorting
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
        } elseif ($this->defaultSortField && in_array($this->defaultSortField, $this->allowedSort)) {
            $this->query->orderBy($this->defaultSortField, $this->defaultSortDirection);
        }

        // 4. Handle Includes
        if ($this->request->filled('include')) {
            $includes = explode(',', $this->request->input('include'));

            $validIncludes = [];
            foreach ($includes as $include) {
                if (array_key_exists($include, $this->allowedIncludes)) {
                    $validIncludes[] = $include;
                }
            }

            if (!empty($validIncludes)) {
                $this->query->with($validIncludes);
            }
        }
    }

    public function paginate()
    {
        $this->finalize();
        $size = $this->request->input('page.size', 30);

        if ($this->request->has('page.cursor')) {
            return $this->query->cursorPaginate(perPage: $size, cursor: $this->request->input('page.cursor'));
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
