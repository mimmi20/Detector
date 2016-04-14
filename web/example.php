<?php

chdir(dirname(__DIR__));

$autoloadPaths = [
    'vendor/autoload.php',
    '../../autoload.php',
];

$foundVendorAutoload = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        $foundVendorAutoload = true;
        break;
    }
}

if (!$foundVendorAutoload) {
    throw new Exception('Could not find autoload path in any of the searched locations');
}

use ModernizrServer\Modernizr;

if (null === Modernizr::getData()) {
    echo "<html><head><script type='text/javascript'>";
    echo Modernizr::buildJsCode() . '</script></head><body></body></html>';
    exit;
}

echo 'The server knows:';
foreach (Modernizr::getData() as $feature => $value) {
    echo "<br/> $feature: ";
    print_r($value);
}
