<?php

declare(strict_types=1);

$descriptor = simplexml_load_file(dirname(__DIR__, 2).'/extension.xml');
if ($descriptor === false || count($descriptor->modules->module) !== 6) {
    throw new RuntimeException('Expected six extension modules.');
}
echo "Extension module list is available.\n";
