<?php

namespace App\Tool;

use EchoLabs\Prism\Tool;
use ReflectionClass;

class GetFunctionForFile extends Tool
{
    public function __construct()
    {
        $this->as('get-functions')
            ->for('useful whenever getting reflection data seems helpful')
            ->withStringParameter('fqcn', 'provide the fully qualified class name')
            ->using($this);
    }

    public function __invoke(string $fqcn): array
    {
        $reflection = new ReflectionClass($fqcn);
        $methods = $reflection->getMethods();

        return $methods;
    }
}
