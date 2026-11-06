<?php

declare(strict_types=1);

require_once __DIR__ . '/../scripts/prepare-release.php';

$manifest = file_get_contents(__DIR__ . '/../extension.xml');
$module = file_get_contents(__DIR__ . '/../br-costcenter/module.br-costcenter.php');
$xml = simplexml_load_string($manifest);
$current = trim((string) $xml->version);
$parts = explode('.', $current);
$next = $parts[0] . '.' . $parts[1] . '.' . ((int) $parts[2] + 1);

$tests = [
    'prepare matching release metadata without changing other content' => static function () use ($manifest, $module, $current, $next): void {
        [$updatedManifest, $updatedModule] = prepareReleaseContents($manifest, $module, $next, '2028-02-29');
        $xml = simplexml_load_string($updatedManifest);
        if ((string) $xml->version !== $next || (string) $xml->release_date !== '2028-02-29'
            || $updatedModule !== str_replace("'br-costcenter/$current'", "'br-costcenter/$next'", $module)
            || preg_replace('~<(version|release_date)>.*?</\1>~s', '', $updatedManifest)
                !== preg_replace('~<(version|release_date)>.*?</\1>~s', '', $manifest)) {
            throw new RuntimeException('Unexpected release metadata or unrelated edits.');
        }
    },
];

$cases = [
    'reject malformed version' => [$manifest, $module, 'v3.2.3', '2028-02-29'],
    'reject version with leading zeros' => [$manifest, $module, '03.2.3', '2028-02-29'],
    'reject unchanged version' => [$manifest, $module, $current, '2028-02-29'],
    'reject older version' => [$manifest, $module, '0.0.0', '2028-02-29'],
    'reject invalid calendar date' => [$manifest, $module, $next, '2027-02-29'],
    'reject ambiguous module version' => [$manifest, $module . $module, $next, '2028-02-29'],
    'reject mismatched module version' => [$manifest, str_replace("'br-costcenter/$current'", "'br-costcenter/0.0.0'", $module), $next, '2028-02-29'],
    'reject missing date element' => [preg_replace('~<release_date>.*?</release_date>~s', '', $manifest), $module, $next, '2028-02-29'],
];
foreach ($cases as $name => $arguments) {
    $tests[$name] = static function () use ($arguments): void {
        try {
            prepareReleaseContents(...$arguments);
        } catch (RuntimeException $error) {
            return;
        }
        throw new RuntimeException('Invalid release input was accepted.');
    };
}

$tests['prepare all six modules and internal dependencies without editing originals'] = static function () use ($next): void {
    $contents = ['extension.xml' => file_get_contents(__DIR__ . '/../extension.xml')];
    foreach (glob(__DIR__ . '/../br-costcenter*/module.*.php') as $path) {
        $contents[basename(dirname($path)) . '/' . basename($path)] = file_get_contents($path);
    }
    $updated = prepareProjectReleaseContents($contents, $next, '2028-02-29');
    if (count($updated) !== 7) {
        throw new RuntimeException('Expected manifest and six modules.');
    }
    foreach ($contents as $path => $original) {
        if ($path === 'extension.xml') {
            continue;
        }
        $expected = preg_replace('~(br-costcenter(?:-bridge-for-[a-z0-9-]+)?)/[0-9]+\.[0-9]+\.[0-9]+~', '$1/' . $next, $original);
        if ($updated[$path] !== $expected || file_get_contents(__DIR__ . '/../' . $path) !== $original) {
            throw new RuntimeException('Unexpected module edits: ' . $path);
        }
    }
};

$projectContents = ['extension.xml' => $manifest];
foreach (releaseModuleIds() as $id) {
    $path = "$id/module.$id.php";
    $projectContents[$path] = file_get_contents(__DIR__ . '/../' . $path);
}
$tests['update internal alternatives while preserving external dependency versions'] = static function () use ($projectContents, $next): void {
    $updated = prepareProjectReleaseContents($projectContents, $next, '2028-02-29');
    $order = $updated['br-costcenter-bridge-for-br-orderrequest/module.br-costcenter-bridge-for-br-orderrequest.php'];
    $medical = $updated['br-costcenter-bridge-for-br-medicaldevice/module.br-costcenter-bridge-for-br-medicaldevice.php'];
    foreach ([
        [$order, "'br-costcenter-bridge-for-br-orderrequest/$next'"],
        [$order, "'br-costcenter-bridge-for-itop-tickets/$next'"],
        [$order, "'br-costcenter/$next||br-orderrequest/0.0.1'"],
        [$medical, "'br-costcenter/$next||br-medicaldevice/3.2.1'"],
    ] as [$content, $expected]) {
        if (!str_contains($content, $expected)) {
            throw new RuntimeException('Missing expected reference: ' . $expected);
        }
    }
    foreach ($projectContents as $path => $original) {
        if ($path === 'extension.xml') {
            continue;
        }
        preg_match_all('~(?<=[\'|])(?:itop-[a-z-]+|br-orderrequest|br-medicaldevice|combodo-pmp-light)/[0-9.]+~', $original, $before);
        preg_match_all('~(?<=[\'|])(?:itop-[a-z-]+|br-orderrequest|br-medicaldevice|combodo-pmp-light)/[0-9.]+~', $updated[$path], $after);
        if ($before !== $after) {
            throw new RuntimeException('External dependency changed: ' . $path);
        }
    }
};
$bridgePath = 'br-costcenter-bridge-for-br-orderrequest/module.br-costcenter-bridge-for-br-orderrequest.php';
$missing = $projectContents;
unset($missing[$bridgePath]);
$invalid = $projectContents;
$invalid[$bridgePath] = str_replace('br-costcenter/3.2.2', 'br-costcenter/invalid', $invalid[$bridgePath]);
$newer = $projectContents;
$newer[$bridgePath] = str_replace('br-costcenter-bridge-for-br-orderrequest/0.0.1', 'br-costcenter-bridge-for-br-orderrequest/99.0.0', $newer[$bridgePath]);
foreach (['reject missing bridge module' => $missing, 'reject malformed internal dependency' => $invalid,
    'reject target older than a bridge' => $newer] as $name => $contents) {
    $tests[$name] = static function () use ($contents, $next): void {
        try {
            prepareProjectReleaseContents($contents, $next, '2028-02-29');
        } catch (RuntimeException $error) {
            return;
        }
        throw new RuntimeException('Invalid project release was accepted.');
    };
}

return $tests;
