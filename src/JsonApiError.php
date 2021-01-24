<?php

namespace Tben\LaravelJsonAPI;

class JsonApiError
{
    private $id = "";
    private $status = "";
    private $code = "";
    private $title = "";
    private $detail = "";
    private $source = "";
    private $meta = [];

    public function __construct($code, $title = null, $detail = null, $id = null, array $meta = null)
    {
        $this->id = $id;
        $this->code = $code;
        $this->title = $title;
        $this->detail = $detail;
        $this->meta = $meta;
    }

    public function toArray()
    {
        return [
            "id" => $this->id,
            "status" => $this->status,
            "code" => $this->code,
            "title" => $this->title,
            "detail" => $this->detail,
            "source" => $this->source,
            "meta" => $this->meta,
        ];
    }
}
