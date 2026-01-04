<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class CheckModelStandards extends Command
{
    protected $signature = 'model:check';
    protected $description = 'Check if all models follow the project standards';

    private array $requiredTraits = [
        \Illuminate\Database\Eloquent\Factories\HasFactory::class,
        \Spatie\Activitylog\Traits\LogsActivity::class,
    ];

    private array $standardMethods = [
        'getActivitylogOptions',
    ];

    public function handle(): int
    {
        $modelFiles = File::allFiles(app_path('Models'));
        $errors = [];

        foreach ($modelFiles as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = "App\\Models\\" . str_replace('.php', '', $file->getRelativePathname());
            $className = str_replace('/', '\\', $className);

            if (!class_exists($className)) {
                continue;
            }

            try {
                $reflection = new ReflectionClass($className);
                
                // Skip abstract classes
                if ($reflection->isAbstract()) {
                    continue;
                }

                // Check if extends BaseModel or DataModel
                $parent = $reflection->getParentClass();
                if (!$parent || !in_array($parent->getName(), [
                    \App\Models\BaseModel::class,
                    \App\Models\DataModel::class
                ])) {
                    $errors[] = "$className should extend BaseModel or DataModel";
                }

                // Check required traits
                $traits = $reflection->getTraits();
                foreach ($this->requiredTraits as $trait) {
                    if (!array_key_exists($trait, $traits)) {
                        $errors[] = "$className should use $trait";
                    }
                }

                // Check required methods
                foreach ($this->standardMethods as $method) {
                    if (!$reflection->hasMethod($method)) {
                        $errors[] = "$className should implement $method()";
                    }
                }

                // Check property definitions
                if (!$reflection->hasProperty('fillable')) {
                    $errors[] = "$className should define \$fillable property";
                }

                if (!$reflection->hasProperty('casts')) {
                    $errors[] = "$className should define \$casts property";
                }

                // Check docblock completeness
                if (!$reflection->getDocComment()) {
                    $errors[] = "$className should have a class docblock";
                }

            } catch (\Exception $e) {
                $this->error("Error checking $className: " . $e->getMessage());
            }
        }

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        $this->info('All models follow the project standards.');
        return Command::SUCCESS;
    }
}