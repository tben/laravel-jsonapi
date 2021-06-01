<?php

namespace Tben\LaravelJsonAPI;

class JsonApiError
{
    private $id = '';
    private $status = '';
    private $code = '';
    private $title = '';
    private $detail = '';
    private $source = '';
    private $meta = [];

    /**
     * Undocumented function
     *
     * @param integer $code
     * @param string $detail
     * @param string $title
     * @param [type] $id
     * @param string $source
     * @param array $meta
     */
    public function __construct(
        int $code,
        string $detail = null,
        string $title = null,
        $id = null,
        string $source = null,
        array $meta = null
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->title = $title;
        $this->detail = $detail;
        $this->source = $source;
        $this->meta = $meta;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'code' => $this->code,
            'title' => $this->title,
            'detail' => $this->detail,
            'source' => $this->source,
            'meta' => $this->meta,
        ];
    }
}
