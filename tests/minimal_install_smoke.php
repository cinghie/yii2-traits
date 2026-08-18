<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$root = dirname(__DIR__);
$traitFiles = array_merge(
    glob($root . DIRECTORY_SEPARATOR . '*Trait.php') ?: [],
    glob($root . DIRECTORY_SEPARATOR . '*Traits.php') ?: []
);

if ($traitFiles === []) {
    throw new RuntimeException('No trait files were discovered for minimal runtime smoke testing.');
}

foreach ($traitFiles as $file) {
    $traitName = pathinfo($file, PATHINFO_FILENAME);
    $fqcn = 'cinghie\\traits\\' . $traitName;

    if (!trait_exists($fqcn)) {
        throw new RuntimeException('Unable to autoload trait with minimal runtime dependencies: ' . $fqcn);
    }
}

$host = new class {
    use \cinghie\traits\SequentialTrait;
};

if ($host->generateSequentialCode(42) !== 'A00000042') {
    throw new RuntimeException('Core trait behavior smoke test failed with minimal runtime dependencies.');
}

if (!class_exists(\cinghie\traits\services\AttachmentService::class)) {
    throw new RuntimeException('Unable to autoload core AttachmentService with minimal runtime dependencies.');
}

if (!class_exists(\cinghie\traits\services\RuntimeConfig::class)) {
    throw new RuntimeException('Unable to autoload core RuntimeConfig with minimal runtime dependencies.');
}

echo 'minimal runtime dependencies: ok (' . count($traitFiles) . " traits)\n";
