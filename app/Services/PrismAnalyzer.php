<?php

namespace App\Services;

class PrismAnalyzer
{
    public function analyze(string $code, string $language = 'php'): array
    {
        if ($language !== 'php') {
            throw new \InvalidArgumentException("Unsupported language: {$language}");
        }

        $tokens = token_get_all($code);
        $complexity = $this->calculateComplexity($tokens);

        return [
            'language' => $language,
            'tokens' => $tokens,
            'metrics' => [
                'lines' => substr_count($code, "\n") + 1,
                'functions' => $this->countType($tokens, T_FUNCTION),
                'classes' => $this->countType($tokens, T_CLASS),
                'methods' => $this->countMethods($tokens),
                'complexity' => $complexity,
            ],
        ];
    }

    private function countType(array $tokens, int $type): int
    {
        $count = 0;
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === $type) {
                $count++;
            }
        }
        return $count;
    }

    private function countMethods(array $tokens): int
    {
        $count = 0;
        for ($i = 0; $i < count($tokens) - 1; $i++) {
            if (is_array($tokens[$i]) && 
                $tokens[$i][0] === T_STRING && 
                isset($tokens[$i-1]) && 
                is_array($tokens[$i-1]) && 
                $tokens[$i-1][0] === T_FUNCTION) {
                $count++;
            }
        }
        return $count;
    }

    private function calculateComplexity(array $tokens): int
    {
        $complexity = 1; // Base complexity
        $conditionals = [T_IF, T_ELSEIF, T_CASE, T_WHILE, T_FOR, T_FOREACH, T_CATCH];

        foreach ($tokens as $token) {
            if (!is_array($token)) {
                continue;
            }

            if (in_array($token[0], $conditionals)) {
                $complexity++;
            }

            // Add complexity for logical operators
            if (in_array($token[0], [T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_AND, T_LOGICAL_OR])) {
                $complexity++;
            }
        }

        return $complexity;
    }
}