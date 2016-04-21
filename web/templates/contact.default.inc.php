<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Detector [BETA] - combined browser- &amp; feature-detection for your app</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <?php /*if (isset($ua->isMobile) && $ua->isMobile) : ?>
        <link href="/css/mobile.css" rel="stylesheet"/>
    <?php
    else : ?>
        <link href="/css/desktop.css" rel="stylesheet"/>
    <?php
    endif;/**/ ?>
    <link href="/css/general.css" rel="stylesheet"/>

    <!-- My Scripts -->
    <script type="text/javascript" src="<?php echo htmlentities($detector->buildFeaturesScriptLink()); ?>"></script>
</head>

<body>
<div class="container">
    <div class="content clearfix">
        <div class="page-header col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 clearfix">
            <h1>
                <a href="/" style="color: black;">Detector</a>
                <span class="label notice beta">beta</span>
                <small>combined browser- &amp; feature-detection for your app</small>
            </h1>

            <div id="forkme">
                <a href="https://github.com/mimmi20/Detector">
                    <img src="images/ForkMe_Wht.png" width="141" height="141" alt="Fork Me on GitHub"/>
                </a>
            </div>
        </div>
        <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 clearfix">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 col-xl-9">
                <?php
                if (!empty($_POST['post'])) {
                    include 'web/templates/_contactty.inc.php';
                } else {
                    include 'web/templates/_contactform.inc.php';
                }
                ?>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <?php include 'web/templates/_about.inc.php'; ?>
                <?php include 'web/templates/_moreinfo.inc.php'; ?>
                <?php include 'web/templates/_credits.inc.php'; ?>
            </div>
        </div>
    </div>
    <footer class="clearfix">
        <p>&copy; <a href="http://dmolsen.com/">Dave Olsen</a> 2012 | Design based on <a
                href="http://twitter.github.com/bootstrap/">Bootstrap</a></p>
    </footer>
</div>
<!-- /container -->
</body>
</html>