<?php

function scanTestFiles($directory) {
    $files = [];
    $items = glob($directory . '/*');
    
    foreach ($items as $item) {
        if (is_dir($item)) {
            $files = array_merge($files, scanTestFiles($item));
        } else if (basename($item) !== 'Pest.php' && 
                   basename($item) !== 'TestCase.php' && 
                   basename($item) !== basename(__FILE__) &&
                   str_ends_with($item, '.php')) {
            $files[] = $item;
        }
    }
    
    return $files;
}

function analyzeTest($file) {
    $content = file_get_contents($file);
    preg_match_all('/function\s+test_([^\(]+)/m', $content, $matches);
    
    return [
        'file' => $file,
        'is_pest' => str_contains($content, 'it('),
        'test_count' => count($matches[0]),
        'has_setup' => str_contains($content, 'setUp'),
        'assertions' => [
            'assert' => preg_match_all('/\$this->assert/', $content),
            'expect' => preg_match_all('/expect\(/', $content)
        ]
    ];
}

$files = scanTestFiles(__DIR__);
$analysis = [];
$stats = [
    'total' => 0,
    'pest' => 0,
    'phpunit' => 0,
    'test_count' => 0
];

foreach ($files as $file) {
    $result = analyzeTest($file);
    $analysis[] = $result;
    $stats['total']++;
    $stats['pest'] += $result['is_pest'] ? 1 : 0;
    $stats['phpunit'] += $result['is_pest'] ? 0 : 1;
    $stats['test_count'] += $result['test_count'];
}

echo "Test Analysis:\n";
echo "Total files: {$stats['total']}\n";
echo "Pest style: {$stats['pest']}\n";
echo "PHPUnit style: {$stats['phpunit']}\n";
echo "Total test methods: {$stats['test_count']}\n\n";

echo "Files needing conversion:\n";
foreach ($analysis as $result) {
    if (!$result['is_pest']) {
        echo "- {$result['file']}\n";
        echo "  Tests: {$result['test_count']}\n";
        echo "  Setup: " . ($result['has_setup'] ? 'Yes' : 'No') . "\n";
        echo "  Assertions: " . $result['assertions']['assert'] . "\n";
        echo "\n";
    }
}
