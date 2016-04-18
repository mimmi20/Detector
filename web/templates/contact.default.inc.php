<?php include 'web/templates/_header.inc.php'; ?>

    <div class="col-xs-10">
        <?php
        if ($_POST['post']) {
            include 'web/templates/_contactty.inc.php';
        } else {
            include 'web/templates/_contactform.inc.php';
        }
        ?>
    </div>

    <div class="col-xs-4">
        <?php include 'web/templates/_about.inc.php'; ?>
        <?php include 'web/templates/_moreinfo.inc.php'; ?>
        <?php include 'web/templates/_credits.inc.php'; ?>
    </div>

<?php include 'web/templates/_footer.inc.php'; ?>