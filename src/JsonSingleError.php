<?php

namespace Tben\LaravelJsonAPI;

use Tben\LaravelJsonAPI\Response\Links;
use Tben\LaravelJsonAPI\Response\MetaObject;

class JsonSingleError
{
    /**
     * Single JSON:API Error
     *
     * @param integer $status
     * @param string $title
     * @param string|null $code
     * @param string|null $source
     * @param string|null $detail
     * @param string|null $id
     * @param Links|null $links
     * @param MetaObject|null $meta
     */
    public function __construct(
        private int $status = 500,
        private string $title = 'Error Occurred',
        private ?string $code = '500',
        private ?string $source = null,
        private ?string $detail = null,
        private ?string $id = null,
        private ?Links $links = null,
        private ?MetaObject $meta = null
    ) {}

    /**
     * Render JSON:API Error as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'code' => $this->code,
            'links' => $this->links?->toJsonApi(),
            'title' => $this->title,
            'detail' => $this->detail,
            'source' => $this->source,
            'meta' => $this->meta?->toJsonApi(),
        ];
    }
}
