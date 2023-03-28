<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

use Illuminate\Database\Eloquent\Model;
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

        // Add Includes
        if (! empty(self::$include) && is_iterable(self::$include)) {
            self::$response['included'] = self::generateIncluded();
        }

        return self::$response;
    }

    /**
     * Generate a JSON:API-compliant array from an Eloquent model
     */
    public static function generateFromModel(Model $model): array
    {
        $data = [];

        // If the model doesn't exist, return an empty array
        if (! $model->exists) {
            return [];
        }

        // Add the model ID to the data
        $data['id'] = $model->getKey();

        // Add the JSON:API type to the data
        $data['type'] = (string) $model->jsonApiType ?? 'Unknown';

        // Add the model's attributes to the data
        $attributes = $model->attributesToArray();
        unset($attributes[$model->getKeyName()]);

        if (! empty($attributes)) {
            $data['attributes'] = $attributes;
        }

        // Add links to the data, if available
        if (method_exists($model, 'jsonApiLink')) {
            $data['links'] = [
                'self' => $model->jsonApiLink(),
            ];
        }

        // Add relationships to the data, if available
        if (method_exists($model, 'jsonApiRelationships')) {
            foreach ($model->jsonApiRelationships() as $key => $relationship) {
                data_fill($data, "relationships.{$key}", [
                    'links' => [
                        'related' => $relationship,
                    ],
                ]);
            }
        }

        // Hide relations
        $relations = $model->getRelations();

        if (count($model->getVisible()) > 0) {
            $relations = array_intersect_key($relations, array_flip($model->getVisible()));
        }

        if (count($model->getHidden()) > 0) {
            $relations = array_diff_key($relations, array_flip($model->getHidden()));
        }

        // Add related models to the data, if available.
        foreach ($relations as $key => $relationship) {
            if ($relationship instanceof EloquentCollectionObject) {
                // If the relationship is a collection, add each related model's data to the data array.
                data_fill($data, "relationships.{$key}.data", []);

                foreach ($relationship as $relationshipModel) {
                    $data['relationships'][$key]['data'][] = [
                        'id' => $relationshipModel->getKey(),
                        'type' => (string) $relationshipModel->jsonApiType ?? 'Unknown',
                    ];

                    // Add the related model to the include array
                    self::$include[] = $relationshipModel;
                }
            } elseif ($relationship instanceof EloquentModelObject) {
                // If the relationship is a single model, add its data to the data array
                data_fill($data, "relationships.{$key}.data", [
                    'id' => $relationship->getKey(),
                    'type' => (string) $relationship->jsonApiType ?? 'Unknown',
                ]);

                // Add the related model to the include array
                self::$include[] = $relationship;
            }
        }

        return $data;
    }

    /**
     * Generate Included
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
