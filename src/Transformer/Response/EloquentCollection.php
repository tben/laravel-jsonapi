<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection as EloquentCollectionObject;
use Illuminate\Database\Eloquent\Model as EloquentModelObject;

class EloquentCollection
{
    private static $response = [];
    private static $include = [];

    /**
     * Undocumented function
     *
     * @param Model $model
     * @return array
     */
    public static function handle($models): array
    {
        self::$response['data'] = [];

        foreach ($models as $model) {
            if ($model->exists == false) {
                continue;
            }

            $data = self::generateFromModel($model, null);

            self::$response['data'][] = $data;
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
