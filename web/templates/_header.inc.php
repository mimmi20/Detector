<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Detector [BETA] - combined browser- &amp; feature-detection for your app</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <?php use \Detector\Detector;

    ?>
    <?php if (isset($ua->isMobile) && $ua->isMobile) : ?>
    <meta name="viewport" content="width=device-width">
    <?php
endif; ?>

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <link href="/css/bootstrap.min.css" rel="stylesheet"/>
    <?php if (isset($ua->isMobile) && $ua->isMobile) : ?>
    <link href="/css/mobile.css" rel="stylesheet"/>
    <?php
else : ?>
    <link href="/css/desktop.css" rel="stylesheet"/>
    <?php
endif; ?>
    <link href="/css/general.css" rel="stylesheet"/>

    <!-- My Scripts -->
    <?php echo $detector->buildFeaturesScriptLink(); ?>
    <script type="text/javascript" src="/js/modernizr.2.8.3.min.custom.js"></script>
    <script type="text/javascript" src="/js/tests.demo.js"></script>
</head>

<body>
    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>
                    <a href="/" style="color: black;">Detector</a>
                    <span class='label notice beta'>beta</span>
                    <?php if (!((isset($ua->isMobile) && $ua->isMobile))) : ?>
                    <small>combined browser- &amp; feature-detection for your app</small>
                    <?php
endif; ?>
                </h1>
            </div>
            <div class="row">