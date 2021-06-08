<?php

namespace Tben\LaravelJsonAPI;

use Illuminate\Database\Eloquent\Collection as EloquentCollectionObject;
use Illuminate\Database\Eloquent\Model as EloquentModelObject;
use Illuminate\Pagination\LengthAwarePaginator as EloquentPaginationObject;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as CollectionObject;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tben\LaravelJsonAPI\Exceptions\CannotTransform;
use Tben\LaravelJsonAPI\Facades\JsonMeta;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentCollection;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentModel;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentPagination;
use Tben\LaravelJsonAPI\Transformer\Response\Collection;

class JsonApiResponse extends JsonResponse
{
    /**
     * Constructor.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return void
     */
    public function __construct($data = null, $status = 200, $headers = [], $options = 0)
    {
        $this->encodingOptions = $options;

        if (is_array($headers) && !isset($headers["Content-Type"])) {
            $headers["Content-Type"] = "application/vnd.api+json";
        }

        parent::__construct($data, $status, $headers);
    }

    /**
     * Sets the JSONP callback.
     *
     * @param  string|null  $callback
     * @return $this
     */
    public function withCallback($callback = null)
    {
        return $this->setCallback($callback);
    }

    /**
     * Get the json_decoded data from the response.
     *
     * @param  bool  $assoc
     * @param  int  $depth
     * @return mixed
     */
    public function getData($assoc = false, $depth = 512)
    {
        return json_decode($this->data, $assoc, $depth);
    }

    /**
     * Transform data depending on the data sent
     *
     * @param mixed $data
     * @return mixed
     */
    public function setData($data = [])
    {
        $this->orginal = $data;

        if (is_string($data)) {
            $data = collect(Arr::wrap($data));
        }

        if (is_array($data)) {
            $data = collect($data);
        }

        switch (true) {
            case $data instanceof EloquentCollectionObject:
                $response = EloquentCollection::handle($data);
                break;
            case $data instanceof EloquentModelObject:
                $response = EloquentModel::handle($data);
                break;
            case $data instanceof EloquentPaginationObject:
                $response = EloquentPagination::handle($data);
                break;
            case $data instanceof CollectionObject:
                $response = Collection::handle($data);
                break;
            default:
                throw new CannotTransform();
        }

        // Add Metadata to response
        $response['meta'] = JsonMeta::viewMetaAll();
        if ($response['meta'] == null) {
            unset($response['meta']);
        }

        $this->data = json_encode($response);

        if (! $this->hasValidJson(json_last_error())) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        return parent::update();
    }

    /**
     * Determine if an error occurred during JSON encoding.
     *
     * @param  int  $jsonError
     * @return bool
     */
    protected function hasValidJson($jsonError)
    {
        if ($jsonError === JSON_ERROR_NONE) {
            return true;
        }

        return $this->hasEncodingOption(JSON_PARTIAL_OUTPUT_ON_ERROR) &&
            in_array($jsonError, [
                JSON_ERROR_RECURSION,
                JSON_ERROR_INF_OR_NAN,
                JSON_ERROR_UNSUPPORTED_TYPE,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setEncodingOptions($options)
    {
        $this->encodingOptions = (int) $options;

        return $this->setData($this->getData());
    }

    /**
     * Determine if a JSON encoding option is set.
     *
     * @param  int  $option
     * @return bool
     */
    public function hasEncodingOption($option)
    {
        return (bool) ($this->encodingOptions & $option);
    }
}
