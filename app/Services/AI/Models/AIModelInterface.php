<?php

namespace App\Services\AI\Models;

interface AIModelInterface
{
    public function analyze(string $code, array $context = []): array;
    public function suggest(string $context, array $options = []): array;
    public function review(string $code, array $standards = []): array;
    public function explain(string $code, string $format = 'markdown'): string;
    public function optimize(string $code, array $constraints = []): string;
}