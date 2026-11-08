#!/usr/bin/env php
<?php

if (PHP_VERSION_ID < 70100) {
    fwrite(STDERR, "PHP >= 7.1.0 required\n");
    exit(1);
}

date_default_timezone_set('UTC');
setlocale(LC_NUMERIC, 'C');

$rootDir = dirname(__DIR__, 2);
if (!file_exists($rootDir . '/tcpdf.php')) {
    fwrite(STDERR, "Could not locate tcpdf.php under $rootDir\n");
    exit(1);
}

if (!defined('K_TCPDF_THROW_EXCEPTION_ERROR')) {
    define('K_TCPDF_THROW_EXCEPTION_ERROR', true);
}

require_once $rootDir . '/tcpdf.php';
require_once $rootDir . '/tcpdf_barcodes_1d.php';
require_once $rootDir . '/tcpdf_barcodes_2d.php';
require_once $rootDir . '/include/tcpdf_filters.php';

require_once __DIR__ . '/assert.php';

$testFiles = array(
    'static_test.php',
    'color_test.php',
    'font_test.php',
    'filter_test.php',
    'barcode_1d_test.php',
    'barcode_2d_test.php',
    'document_test.php',
);

$passed = 0;
$failed = 0;
$failures = array();

foreach ($testFiles as $file) {
    $path = __DIR__ . '/' . $file;
    if (!file_exists($path)) {
        echo "FAIL bootstrap: missing $file\n";
        ++$failed;
        continue;
    }
    require_once $path;
}

$testFunctions = get_defined_functions();
$testFunctions = $testFunctions['user'];
foreach ($testFunctions as $fn) {
    if (strpos($fn, 'test_') !== 0) {
        continue;
    }
    echo "RUN $fn\n";
    try {
        call_user_func($fn);
        echo "PASS $fn\n";
        ++$passed;
    } catch (Exception $e) {
        echo "FAIL $fn: " . $e->getMessage() . "\n";
        $failures[] = array('name' => $fn, 'message' => $e->getMessage());
        ++$failed;
    } catch (Throwable $e) {
        echo "FAIL $fn: " . $e->getMessage() . "\n";
        $failures[] = array('name' => $fn, 'message' => $e->getMessage());
        ++$failed;
    }
}

echo $passed . ' passed, ' . $failed . ' failed' . PHP_EOL;
exit($failed === 0 ? 0 : 1);
