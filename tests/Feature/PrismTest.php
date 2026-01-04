<?php

use App\Services\PrismAnalyzer;

beforeEach(function () {
    $this->analyzer = new PrismAnalyzer();
});

it('analyzes php file successfully', function () {
    $code = '<?php function test() { return true; }';
    
    $result = $this->analyzer->analyze($code, 'php');
    
    expect($result)->toBeArray()
        ->toHaveKeys(['tokens', 'metrics'])
        ->and($result['metrics'])->toBeArray();
});

it('handles unsupported language', function () {
    $code = 'some random code';
    
    $result = $this->analyzer->analyze($code, 'unsupported');
    
    expect($result)->toBeNull();
});

it('calculates metrics correctly', function () {
    $code = '<?php
        function complexFunction() {
            if (true) {
                for ($i = 0; $i < 10; $i++) {
                    while(false) {}
                }
            }
        }';
    
    $result = $this->analyzer->analyze($code, 'php');
    
    expect($result['metrics'])
        ->toHaveKey('complexity')
        ->and($result['metrics']['complexity'])->toBeGreaterThan(1);
});