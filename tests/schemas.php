<?php

declare(strict_types=1);

$schemaDirectory = getenv('ITOP_SCHEMA_DIR') ?: __DIR__ . '/../.tmp/schemas';
$documents = [
    'extension manifest matches its XSD' => ['extension.xml', 'extension.xsd'],
];
foreach (glob(__DIR__ . '/../br-costcenter*/datamodel.*.xml') as $model) {
    $path = basename(dirname($model)) . '/' . basename($model);
    $documents[$path . ' matches the iTop 3.2 XSD'] = [$path, '3.2/itop_design.xsd'];
}

$tests = [];
$tests['extension modules are declared as an iTop array'] = static function (): void {
    $manifest = simplexml_load_file(__DIR__ . '/../extension.xml', SimpleXMLElement::class, LIBXML_NONET);
    if ($manifest === false || (string) $manifest->modules['type'] !== 'array') {
        throw new RuntimeException('Extension modules must be declared as an array for iTop.');
    }
};
foreach ($documents as $name => [$documentPath, $schemaPath]) {
    $tests[$name] = static function () use ($schemaDirectory, $documentPath, $schemaPath): void {
        $schema = $schemaDirectory . '/' . $schemaPath;
        if (!is_file($schema)) {
            throw new RuntimeException("Missing schema: $schema. See tests/README.md for setup.");
        }
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();
        try {
            $document = new DOMDocument();
            if (!$document->load(__DIR__ . '/../' . $documentPath, LIBXML_NONET)
                || !$document->schemaValidate($schema, LIBXML_NONET)) {
                $messages = array_map(
                    static fn (LibXMLError $error): string =>
                        'Line ' . $error->line . ': ' . trim($error->message),
                    libxml_get_errors()
                );
                throw new RuntimeException(implode('; ', $messages));
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    };
}

return $tests;
