<?php

namespace Tben\LaravelJsonAPI;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Tben\LaravelJsonAPI\Exceptions\CannotTransformException;
use Tben\LaravelJsonAPI\Facades\JsonMeta;
use Tben\LaravelJsonAPI\Transformer\Response;

class JsonApiResponse implements Responsable
{
    private ?int $status = null;
    private array $headers = [];

    public function __construct(private mixed $value)
    {
        //
    }

    public static function make(...$args): self
    {
        return new self(...$args);
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function toResponse($request): JsonResponse
    {
        if (is_string($this->value)) {
            $this->value = collect(Arr::wrap($this->value));
        }

        // If it's a raw array, keep it as an array if already structured
        if (is_array($this->value)) {
            $this->value = isset($this->value['data']) || isset($this->value['errors']) 
                ? $this->value 
                : collect($this->value);
        }

        // Transform various object types
        $response = match (true) {
            ($this->value === null) => ['data' => null],
            is_array($this->value) => $this->value,
            ($this->value instanceof JsonApiErrors) => $this->value->toArray(),
            ($this->value instanceof \Illuminate\Database\Eloquent\Collection) => Response\EloquentCollection::handle($this->value),
            ($this->value instanceof \Illuminate\Database\Eloquent\Model) => Response\EloquentModel::handle($this->value),
            ($this->value instanceof \Illuminate\Pagination\LengthAwarePaginator) => Response\EloquentLengthAwarePagination::handle($this->value),
            ($this->value instanceof \Illuminate\Pagination\Paginator) => Response\EloquentPagination::handle($this->value),
            ($this->value instanceof \Illuminate\Pagination\CursorPaginator) => Response\EloquentCursorPagination::handle($this->value),
            ($this->value instanceof \Illuminate\Support\Collection) => Response\Collection::handle($this->value),
            default => throw new CannotTransformException('Cannot transform the given value.'),
        };

        // Add Metadata to response if present
        $meta = JsonMeta::viewMetaAll();
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        // Ensure default content-type header while allowing user overrides
        $headers = array_merge([
            'content-type' => 'application/vnd.api+json'
        ], $this->headers);

        $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

        return new JsonResponse($response, $this->status ?? 200, $headers, $jsonFlags);
    }
}
