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

// require mustache for the templates
require_once "web/demo/mustache/lib/mustache-php/Mustache.php";
require_once "web/demo/mustache/lib/mustache-php/MustacheLoader.php";

// require detector to get the family, autoloads the $ua var
require_once "lib/Detector/Detector.php";

$template = file_get_contents("web/demo/mustache/templates/index.mustache");
$data     = array(
    'title'       => 'Hello, World!',
    'description' => 'This extremely simple demo is meant to show how Detector & Mustache can be combined to create a Responsive Web Design + Server Side Component (RESS) System. By using the requesting browser\'s Detector family classification a responsive template & partials that match the browser\'s features are rendered server-side via Mustache. Choose a different layout below to see how this page & the included images change depending upon the browser family.',
    'link'        => 'https://github.com/dmolsen/Detector/wiki/Templating-with-Detector-&-Mustache-Tutorial',
    'viewDesktop' => '/demo/mustache/?pid=13ee8513d6fb7f97aef6635309b91f40',
    'viewMA'      => '/demo/mustache/?pid=e1bd58cc186d3a2156b6ebddb558fd41',
    'viewMB'      => '/demo/mustache/?pid=658e6d9b003bb3f3a3d9ae6e5ca1a42a',
    'images'      => array(
        array('index'   => '1',
              'title'   => 'Automobile',
              'alt'     => 'auto',
              'img'     => 'images/automobile.jpg',
              'img_sml' => 'images/automobile_sml.jpg',
              'src'     => 'http://farm4.staticflickr.com/3347/3411775886_fcf0af1a42_z.jpg'
        ),
        array('index'   => '2',
              'title'   => 'Bus',
              'alt'     => 'bus',
              'img'     => 'images/bus.jpg',
              'img_sml' => 'images/bus_sml.jpg',
              'src'     => 'http://www.flickr.com/photos/d0a98042/3774873571/sizes/z/in/photostream/'
        ),
        array('index'   => '3',
              'title'   => 'Train',
              'alt'     => 'train',
              'img'     => 'images/train.jpg',
              'img_sml' => 'images/train_sml.jpg',
              'src'     => 'http://www.flickr.com/photos/30827349@N02/3965800996/sizes/z/in/photostream/'
        ),
    ),
);

// if this is a request from features.js.php don't run the build function
$ua = Detector::build();//var_dump($ua);

// include the browserFamily library to classify the browser by features
require_once 'lib/Detector/lib/feature-family/featureFamily.php';
$ua->family = featureFamily::find($ua);

$m        = new Mustache();
$partials = new MustacheLoader("web/demo/mustache/templates/partials/" . $ua->family, "mustache", "web/demo/mustache/templates/partials/base");

print $m->render($template, $data, $partials);

?>