<?php

declare(strict_types=1);

if (getenv('ITOP_TEST_MODE') !== '1' || getenv('ITOP_TEST_DB_HOST') !== 'test-db'
    || getenv('ITOP_TEST_DB_NAME') !== 'itop_test' || getenv('ITOP_TEST_DB_USER') !== 'itop_test') {
    throw new RuntimeException('Refusing to run outside the isolated iTop test environment.');
}
$records = glob('/var/www/html/log/install-*.xml');
if (!$records) {
    throw new RuntimeException('Missing iTop installation record.');
}
usort($records, static fn ($a, $b) => filemtime($b) <=> filemtime($a));
$record = simplexml_load_file($records[0]);
if ($record === false || !isset($record->selected_modules)) {
    throw new RuntimeException('Missing installed module selection.');
}
$modules = array_map('strval', iterator_to_array($record->selected_modules->children(), false));
if (!in_array('br-costcenter', $modules, true)) {
    throw new RuntimeException('Cost Center was not installed.');
}
foreach (['combodo-pmp-light', 'br-costcenter-bridge-for-combodo-pmp-light'] as $id) {
    if (in_array($id, $modules, true)) {
        throw new RuntimeException('Unexpected module installed: '.$id);
    }
}
echo "Cost Center installed without PMP or its bridge.\n";
