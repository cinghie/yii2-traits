<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$host = new class {
    use \cinghie\traits\SequentialTrait;
};

if ($host->generateSequentialCode(42) !== 'A00000042') {
    throw new RuntimeException('Core trait smoke test failed with minimal runtime dependencies.');
}

echo "minimal runtime dependencies: ok\n";
