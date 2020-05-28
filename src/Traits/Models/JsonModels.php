<?php

namespace Tben\LaravelJsonAPI\Traits\Models;

trait JsonModels
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return !is_null($this->getKey());
    }

    /**
     * @return string
     */
    public function getId()
    {
        $this->makeHidden($this->getKeyName());

        return $this->getKey();
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Filter model information according to the request parameters.
     *
     * @param Request $request
     * @return void
     */
    public function handleJSONAPI(Request $request)
    {
        $handle = $this;

        if ($request->has("include")) {
            $relationships = explode(",", $request->include);
            $handle = $handle->with($relationships);
        }

        return $handle;
    }

    /**
     * Convert a model into JSONAPI array.
     *
     * @return array
     */
    public function toJsonApiArray() : array
    {
        $this->makeHidden(array_keys($this->getRelations()));

        $data = [
            'type' => $this->getType(),
            'id' => null,
            'attributes' => null,
            'links' => [
                "self" => $this->getLink(),
            ],
            'relationships' => $this->getJsonApiRelationship()
        ];

        if ($this->hasId()) {
            $data['id'] = $this->getId();
        }

        $attributes = $this->toArray();
        if (!empty($attributes)) {
            $data['attributes'] = $attributes;
        }
        return $data;
    }

    /**
     * Get the relationship information.
     *
     * @return array
     */
    public function getJsonApi() : array
    {
        return [
            "type" => $this->type,
            "id" => $this->getKey()
         ];
    }

    /**
     * Get all the relationships links related to this model.
     *
     * @return array
     */
    public function getJsonApiRelationship() : array
    {
        $return = [];
        foreach ($this->getRelations() as $name => $includes) {
            if ($name == "pivot") {
                continue;
            }

            $return[$name] = [
                "data" => null
            ];

            if (is_a($includes, '\Illuminate\Database\Eloquent\Model')) {
                $return[$name]["data"] = $includes->getJsonApi();
            } elseif (is_a($includes, 'Illuminate\Database\Eloquent\Collection')) {
                $return[$name]["data"] = [];
                foreach ($includes as $include) {
                    $return[$name]["data"][] = $include->getJsonApi();
                }
            }
        }

        return $return;
    }

    /**
     * Get relationships models in their form.
     *
     * @return void
     */
    public function getJsonApiIncludes()
    {
        $return = [];
        foreach ($this->getRelations() as $key => $includes) {
            if ($includes === null || $key == "pivot") {
                continue;
            }

            if (is_a($includes, '\Illuminate\Database\Eloquent\Model')) {
                $return[] = $includes->toJsonApiArray();
                $return = array_merge($return, $includes->getJsonApiIncludes());
            } else {
                foreach ($includes as $include) {
                    $return[] = $include->toJsonApiArray();
                    $return = array_merge($return, $include->getJsonApiIncludes());
                }
            }
        }

        return $return;
    }

    /**
     * Default getlink.
     *
     * @return string
     */
    public function getLink() : string
    {
        return config('app.url') ."{$this->type}/" . $this->getKey();
    }
}
