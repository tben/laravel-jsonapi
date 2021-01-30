<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

use Illuminate\Database\Eloquent\Model;
use Tben\LaravelJsonAPI\Facades\JsonMeta;

use Illuminate\Database\Eloquent\Collection as EloquentCollectionObject;
use Illuminate\Database\Eloquent\Model as EloquentModelObject;

class EloquentModel
{
    private static $response = [];
    private static $include = [];

    /**
     * Undocumented function
     *
     * @param Model $model
     * @return array
     */
    public static function handle(Model $model): array
    {
        self::$response['data'] = self::generateFromModel($model);

        // Add Metadata
        self::$response['meta'] = JsonMeta::viewMetaAll();
        if (self::$response['meta'] == null) {
            unset(self::$response['meta']);
        }

        // Add Includes
        if (!empty(self::$include) && is_iterable(self::$include)) {
            self::$response['included'] = self::generateIncluded();
        }

        return self::$response;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public static function generateFromModel(Model $model): array
    {
        $data = [];

        // Don't generate data if the model has been deleted
        if ($model->exists == false) {
            return $data;
        }

        $data['id'] = $model->getKey();
        $data['type'] = (string) $model->jsonApiType ?? 'Unknown';

        // Attributes
        $attributes = $model->attributesToArray();
        unset($attributes[$model->getKeyName()]);

        if (!empty($attributes)) {
            $data['attributes'] = $attributes;
        }

        // Build link
        if (method_exists($model, 'jsonApiLink')) {
            $data['links'] = [
                'self' => $model->jsonApiLink(),
            ];
        }

        // Load Relationships
        if (method_exists($model, 'jsonApiRelationships')) {
            foreach ($model->jsonApiRelationships() as $key => $relationship) {
                data_fill($data, "relationships.$key", [
                    'links' => [
                        'related' => $relationship,
                    ],
                ]);
            }
        }

        foreach ($model->getRelations() as $key => $relationship) {
            if ($relationship instanceof EloquentCollectionObject) {
                data_fill($data, "relationships.{$key}.data", []);

                foreach ($relationship as $relationshipModel) {
                    $data['relationships'][$key]['data'][] = [
                        'id' => $relationshipModel->getKey(),
                        'type' => (string) $relationshipModel->jsonApiType ?? 'Unknown',
                    ];

                    self::$include[] = $relationshipModel;
                }
            } elseif ($relationship instanceof EloquentModelObject) {
                data_fill($data, "relationships.{$key}.data", [
                    'id' => $relationship->getKey(),
                    'type' => (string) $relationship->jsonApiType ?? 'Unknown',
                ]);

                self::$include[] = $relationship;
            }
        }

        return $data;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function generateIncluded()
    {
        if (count(self::$include) == 0) {
            return null;
        }

        $data = [];

        foreach (self::$include as $included) {
            $data[] = self::generateFromModel($included);
        }

        return $data;
    }
}
