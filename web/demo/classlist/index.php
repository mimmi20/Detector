<?php
chdir(dirname(dirname(dirname(__DIR__))));

$autoloadPaths = array(
    'vendor/autoload.php',
    '../../autoload.php',
);

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

// require detector to get the family, autoloads the $ua var
use \Detector\Detector;
use \Detector\DetectorHelpers;

// if this is a request from features.js.php don't run the build function
$ua = Detector::build();//var_dump($ua);
?>

<html class="<?php echo DetectorHelpers::createHTMLList(
    $ua,
    'isMobile,geolocation,cssanimations,cssgradients,indexeddb',
    true
) ?>">
<head>
    <title>Demo of Including Detector Features in the HTML Tag</title>
</head>
<body>
View the source and you'll see the HTML tag is modified with the following attributes select attributes:<br/>
<br/>
<!-- by using true as the last object you're saying you want select UA attributes also shared -->
<?php echo DetectorHelpers::createHTMLList($ua, 'isMobile,geolocation,cssanimations,cssgradients,indexeddb', true) ?>
</body>
</html>