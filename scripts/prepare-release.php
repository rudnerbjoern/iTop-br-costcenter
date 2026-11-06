<?php

declare(strict_types=1);

/** Module IDs shipped together in the cost center release package. */
function releaseModuleIds(): array
{
    return [
        'br-costcenter',
        'br-costcenter-bridge-for-br-medicaldevice',
        'br-costcenter-bridge-for-br-orderrequest',
        'br-costcenter-bridge-for-combodo-pmp-light',
        'br-costcenter-bridge-for-itop-request-mgmt-itil',
        'br-costcenter-bridge-for-itop-tickets',
    ];
}

/** Match complete internal module references, including dependency alternatives. */
function releaseModuleReferencePattern(): string
{
    return '~(?<=[\'"|])(' . implode('|', array_map(
        static fn (string $id): string => preg_quote($id, '~'),
        releaseModuleIds()
    )) . ')/([^\'"|]+)(?=[\'"|])~';
}

/** Validate all inputs before producing either updated file. */
function prepareReleaseContents(string $manifest, string $module, string $version, string $date): array
{
    if (!preg_match('/^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)$/D', $version)) {
        throw new RuntimeException('Version must use X.Y.Z format without leading zeros.');
    }
    $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    if ($parsedDate === false || $parsedDate->format('Y-m-d') !== $date) {
        throw new RuntimeException('Date must be a valid YYYY-MM-DD date.');
    }
    $previous = libxml_use_internal_errors(true);
    try {
        $xml = simplexml_load_string($manifest, SimpleXMLElement::class, LIBXML_NONET);
        if ($xml === false || $xml->getName() !== 'extension'
            || count($xml->version) !== 1 || count($xml->release_date) !== 1
            || trim((string) $xml->extension_code) !== 'br-costcenter') {
            throw new RuntimeException('Expected one version and release_date in the cost center extension manifest.');
        }
        $currentVersion = trim((string) $xml->version);
    } finally {
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
    }
    $modulePattern = "~'br-costcenter/([^']+)'~";
    if (preg_match_all($modulePattern, $module, $matches) !== 1) {
        throw new RuntimeException('Expected exactly one cost center module version.');
    }
    if ($matches[1][0] !== $currentVersion) {
        throw new RuntimeException('Extension and module versions must match before preparing a release.');
    }
    if (!version_compare($version, $currentVersion, '>')) {
        throw new RuntimeException('The release version must be newer than ' . $currentVersion . '.');
    }
    foreach (['version' => $version, 'release_date' => $date] as $element => $value) {
        $manifest = preg_replace_callback(
            '~<' . $element . '>.*?</' . $element . '>~s',
            static fn (): string => "<$element><![CDATA[$value]]></$element>",
            $manifest,
            -1,
            $count
        );
        if ($manifest === null || $count !== 1) {
            throw new RuntimeException('Expected exactly one editable ' . $element . ' element.');
        }
    }
    $module = preg_replace_callback(
        $modulePattern,
        static fn (): string => "'br-costcenter/$version'",
        $module
    );
    return [$manifest, $module];
}

/** Update all packaged modules and their internal dependency versions together. */
function prepareProjectReleaseContents(array $contents, string $version, string $date): array
{
    $expected = ['extension.xml'];
    foreach (releaseModuleIds() as $id) {
        $expected[] = "$id/module.$id.php";
    }
    if (array_diff($expected, array_keys($contents)) || array_diff(array_keys($contents), $expected)) {
        throw new RuntimeException('Expected the manifest, base module, and all five bridge modules.');
    }
    $base = 'br-costcenter/module.br-costcenter.php';
    [$manifest] = prepareReleaseContents($contents['extension.xml'], $contents[$base], $version, $date);
    $updated = $contents;
    $updated['extension.xml'] = $manifest;
    foreach ($contents as $path => $content) {
        if ($path === 'extension.xml') {
            continue;
        }
        $id = basename(dirname($path));
        if (preg_match_all("~'" . preg_quote($id, '~') . "/([^']+)'~", $content, $matches) !== 1) {
            throw new RuntimeException('Expected exactly one module registration in ' . $path);
        }
        if (!version_compare($version, $matches[1][0], '>')) {
            throw new RuntimeException('Release version must be newer than every module version.');
        }
        preg_match_all(releaseModuleReferencePattern(), $content, $references, PREG_SET_ORDER);
        foreach ($references as $reference) {
            if (!preg_match('/^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)$/D', $reference[2])) {
                throw new RuntimeException('Invalid internal dependency version in ' . $path);
            }
        }
        $updated[$path] = preg_replace_callback(
            releaseModuleReferencePattern(),
            static fn (array $match): string => $match[1] . '/' . $version,
            $content
        );
    }
    return $updated;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        if ($argc < 2 || $argc > 3) {
            throw new RuntimeException('Usage: php scripts/prepare-release.php X.Y.Z [YYYY-MM-DD]');
        }
        $date = $argv[2] ?? (new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin')))->format('Y-m-d');
        $root = dirname(__DIR__);
        $paths = ['extension.xml'];
        foreach (releaseModuleIds() as $id) {
            $paths[] = "$id/module.$id.php";
        }
        $originals = [];
        foreach ($paths as $path) {
            $content = file_get_contents($root . '/' . $path);
            if ($content === false || !is_writable($root . '/' . $path)) {
                throw new RuntimeException('Cannot read and write ' . $path);
            }
            $originals[$path] = $content;
        }
        $updated = prepareProjectReleaseContents($originals, $argv[1], $date);
        try {
            foreach ($paths as $path) {
                if (file_put_contents($root . '/' . $path, $updated[$path], LOCK_EX) !== strlen($updated[$path])) {
                    throw new RuntimeException('Cannot write ' . $path);
                }
            }
        } catch (Throwable $error) {
            foreach ($paths as $path) {
                if (file_put_contents($root . '/' . $path, $originals[$path], LOCK_EX) !== strlen($originals[$path])) {
                    fwrite(STDERR, "Could not restore $path; inspect it manually.\n");
                }
            }
            throw $error;
        }
        echo "Prepared version {$argv[1]} with release date $date. Running tests...\n";
        passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($root . '/tests/run.php'), $status);
        if ($status !== 0) {
            fwrite(STDERR, "Tests failed. Version edits are retained for inspection; do not publish yet.\n");
        }
        exit($status);
    } catch (Throwable $error) {
        fwrite(STDERR, $error->getMessage() . "\n");
        exit(1);
    }
}
