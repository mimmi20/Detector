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

    <!-- Bootstrap -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php if (isset($ua->isMobile) && $ua->isMobile) : ?>
        <link href="/css/mobile.css" rel="stylesheet"/>
    <?php
    else : ?>
        <link href="/css/desktop.css" rel="stylesheet"/>
    <?php
    endif; ?>
    <link href="/css/general.css" rel="stylesheet"/>

    <!-- Modernizr + My Scripts --><?php
    use ModernizrServer\Modernizr;

    foreach (Modernizr::collectJsFiles() as $file) : ?>
        <script src="<?php echo $file; ?>"></script>
    <?php
    endforeach;
    ?>
    <?php if (null === $ua) : ?>
        <?php
        //$html .= Modernizr::buildConvertJs($cookieID, '', true);
        ?>
    <?php endif; ?>
</head>
<body>
<div class="container-fluid">
    <div class="content">
        <div class="page-header">
            <h1>
                <a href="/">Detector</a>
                <span class="label notice beta">beta</span>
                <small>combined browser- &amp; feature-detection for your app</small>
            </h1>
        </div>
        <div class="row1">
            <div class="col-xs-10">
                <p>
                    With the initial release of <a href="http://yiibu.com/">Yiibu's</a> <a
                        href="https://github.com/yiibu/profile">Profile</a>, <a
                        href="https://github.com/mimmi20/Detector">Detector</a>
                    is already <abbr title="Yet Another Browser- and Feature-Detection Library">YABFDL</abbr>
                    <em>(Yet Another Browser- and Feature-Detection Library)</em>. Ever since I heard Yiibu's talk,
                    <a href="http://www.slideshare.net/yiibu/adaptation-why-responsive-design-actually-begins-on-the-server">Adaptation</a>,
                    the core concepts & features of Detector have been floating around in my head. I've finally turned
                    those
                    ideas into <a href="https://github.com/mimmi20/Detector">code</a> and created this demo.
                    To learn more about <a href="https://github.com/mimmi20/Detector">Detector</a> and how it works
                    please check
                    out the <a href="https://github.com/mimmi20/Detector">README on GitHub</a>.
                </p>
                <p>
                    Both of the following Detector profiles were <strong>
                        <?php
                        if ($detector->whereFound() == 'archive') {
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

                <?php include 'web/templates/_browserprofile.inc.php'; ?>
                <?php include 'web/templates/_featureprofile.inc.php'; ?>
            </div>

            <div class="col-xs-4">
                <?php include 'web/templates/_about.inc.php'; ?>
                <?php include 'web/templates/_moreinfo.inc.php'; ?>
                <?php include 'web/templates/_credits.inc.php'; ?>
                <?php include 'web/templates/_socialmedia.inc.php'; ?>
                <?php include 'web/templates/_archive.inc.php'; ?>
            </div>

            <div id="forkme">
                <a href="https://github.com/mimmi20/Detector"><img src="/images/ForkMe_Wht.png" width="141" height="141"
                                                                   alt="Fork Me on GitHub"/></a>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; <a href="http://dmolsen.com/">Dave Olsen</a> 2012 | Design based on <a
                href="http://twitter.github.com/bootstrap/">Bootstrap</a></p>
    </footer>
</div>
<!-- /container -->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> -->
<!-- Include all compiled plugins (below), or include individual files as needed -->
<!-- <script src="js/bootstrap.min.js"></script> -->
</body>
</html>
