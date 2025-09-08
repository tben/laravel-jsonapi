<?php

namespace Tben\LaravelJsonAPI;

class JsonApiRelationship
{
    public function __construct(
        private string $relationshipName,
    ) {
        //
    }

    public function getRelationshipName(): string
    {
        return $this->relationshipName;
    }
}
