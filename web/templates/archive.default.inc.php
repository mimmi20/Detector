<?php include "web/templates/_header.inc.php"; ?>

<div class="span10">
    <h2>Archive of Detector Profiles</h2>
    <p>The following profiles were created by Detector when the first user with that particular browser visited the system:</p>
    <ul>
    <?php
    $uaList = $detector->getUaList();
    foreach ($uaList as $key => $value) {
        print "<li> <a href=\"/?pid=".$key."\">".strip_tags($value)."</a></li>";
    }
    ?>
    </ul>
</div>

<div class="span4">
    <?php include "web/templates/_about.inc.php"; ?>
    <?php include "web/templates/_moreinfo.inc.php"; ?>
    <?php include "web/templates/_credits.inc.php"; ?>
</div>

<?php include "web/templates/_footer.inc.php"; ?>

