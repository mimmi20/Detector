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
require_once "lib/Detector/Detector.php";

// if this is a request from features.js.php don't run the build function
$ua = Detector::build();//var_dump($ua);

// require detector to get the family, autoloads the $ua var
require_once "../../../lib/Detector/lib/helpers/detectorHelper.php";

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>JavaScript Listing</title>
    <?php echo DetectorHelpers::createJavaScriptObj($ua,"browserFull,isMobile,geolocation,cssanimations,cssgradients,indexeddb")?>
  </head>

  <body>

    <p>An example of listing Detector properties via JS:</p>
    <p>
    <script type="text/javascript">
     var m=Detector;
     for(var f in m){
        if(f[0]=='_'){continue;}
        var t=typeof m[f];
        if(t=='function'){continue;}
            document.write(f+'='+m[f]);
            if(t=='object'){
                document.write('-><br />');
                  for(var s in m[f]){
                    if (typeof m[f][s]=='boolean') { document.write('&nbsp; &nbsp; &nbsp; '+s+'<br />'); }
                    else { document.write('&nbsp; &nbsp; &nbsp; '+s+'<br />'); }
                  }
            } else {
                document.write('<br />');
            }
          }
    </script>
    </p>
  </body>
</html>