<p>
Both of the following Detector profiles were <strong>
<?php use \Detector\Detector;

    ?>
<?php
if ($detector->whereFound() == "archive") {
    print " pulled from a profile already in the system that you asked to view. Because it's an archived profile the browser-side tests were not run.";
} else if ($detector->whereFound() == 'cookie') {
    print " created when you first hit this page because Detector didn't recognize your user-agent. You may have experienced a very brief redirect when loading the page initially. The profiles have now been saved for use with other visitors.";
} else if ($detector->whereFound() == 'file') {
    print " created in the past when another user with the same user-agent visited this demo. Detector simply pulled the already existing information for your visit.";
} else if ($detector->whereFound() == 'nojs') {
    print " <span style='color: red'>created from a default, conservative profile because it appears JavaScript or Cookies are turned off and Detector didn't recognize the user-agent.</span>";
} else {
    print " pulled from session because you've visited this page before.";
}
?></strong>
</p>