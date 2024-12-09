<?php

namespace App\Tool;

use EchoLabs\Prism\Tool;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class GetFunctionForFile extends Tool
{
    public function __construct()
    {
        Log::info('GetFunctionForFile: Constructor called');
        $this->as('get-functions')
            ->for('Retrieve detailed model information including relationships, properties, and methods')
            ->withStringParameter('model', 'The model name to analyze')
            ->using($this);
    }

    public function __invoke(string $model): string
    {
        Log::info('GetFunctionForFile: Starting analysis', ['model' => $model]);

        try {
            $fullClassName = "App\\Models\\{$model}";
            Log::info('GetFunctionForFile: Checking class', ['class' => $fullClassName]);

            if (! class_exists($fullClassName)) {
                Log::error('GetFunctionForFile: Model not found', ['class' => $fullClassName]);

                return json_encode(['error' => "Model {$model} not found"]);
            }

            $reflection = new ReflectionClass($fullClassName);
            Log::info('GetFunctionForFile: Created reflection class');

            if (! $reflection->isSubclassOf(Model::class)) {
                Log::error('GetFunctionForFile: Not an Eloquent model', ['class' => $model]);

                return json_encode(['error' => "Class {$model} is not an Eloquent model"]);
            }

            // Get relationships
            Log::info('GetFunctionForFile: Getting relationships');
            $relationships = $this->getRelationships($reflection);
            Log::info('GetFunctionForFile: Found relationships', ['count' => count($relationships)]);

            // Get properties
            Log::info('GetFunctionForFile: Getting properties');
            $properties = $this->getProperties($reflection);
            Log::info('GetFunctionForFile: Found properties', ['count' => count($properties)]);

            // Get model info
            Log::info('GetFunctionForFile: Getting model info');
            $instance = $reflection->newInstance();
            $modelInfo = $this->getModelInfo($instance);
            Log::info('GetFunctionForFile: Got model info', ['table' => $modelInfo['table']]);

            $result = [
                'model' => [
                    'name' => $model,
                    'namespace' => $fullClassName,
                    'table' => $modelInfo['table'],
                    'fillable' => $modelInfo['fillable'],
                    'casts' => $modelInfo['casts'],
                ],
                'relationships' => $relationships,
                'properties' => $properties,
                'traits' => array_map(fn ($trait) => $trait->getName(), $reflection->getTraits()),
            ];

            Log::info('GetFunctionForFile: Analysis complete', ['model' => $model]);

            return json_encode($result, JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            Log::error('GetFunctionForFile: Error during analysis', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function getRelationships(ReflectionClass $reflection): array
    {
        Log::info('GetFunctionForFile: Getting relationships for class', ['class' => $reflection->getName()]);

        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        Log::info('GetFunctionForFile: Found methods', ['count' => count($methods)]);

        $relationships = collect($methods)
            ->filter(function ($method) {
                $hasReturnType = $method->getReturnType();
                Log::info('GetFunctionForFile: Checking method', [
                    'method' => $method->getName(),
                    'hasReturnType' => (bool) $hasReturnType,
                ]);
                if (! $hasReturnType) {
                    return false;
                }

                return str_contains($method->getReturnType()->getName(), 'Relation');
            })
            ->map(function ($method) {
                return [
                    'name' => $method->getName(),
                    'type' => $method->getReturnType()->getName(),
                    'doc' => $method->getDocComment() ?: null,
                ];
            })
            ->values()
            ->toArray();

        Log::info('GetFunctionForFile: Found relationships', ['count' => count($relationships)]);

        return $relationships;
    }

    private function getProperties(ReflectionClass $reflection): array
    {
        Log::info('GetFunctionForFile: Getting properties');

        $properties = collect($reflection->getProperties(ReflectionProperty::IS_PUBLIC))
            ->map(function ($property) {
                return [
                    'name' => $property->getName(),
                    'type' => $property->getType() ? $property->getType()->getName() : 'mixed',
                    'doc' => $property->getDocComment() ?: null,
                ];
            })
            ->values()
            ->toArray();

        Log::info('GetFunctionForFile: Found properties', ['count' => count($properties)]);

        return $properties;
    }

    private function getModelInfo($instance): array
    {
        Log::info('GetFunctionForFile: Getting model info');

        $info = [
            'table' => $instance->getTable(),
            'fillable' => $instance->getFillable(),
            'casts' => $instance->getCasts(),
        ];

        Log::info('GetFunctionForFile: Model info retrieved', [
            'table' => $info['table'],
            'fillableCount' => count($info['fillable']),
            'castsCount' => count($info['casts']),
        ]);

        return $info;
    }
}
