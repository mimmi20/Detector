<?php
use Detector\Detector;

/**
 * @param \Detector\Detector $detector
 * @param \stdClass          $ua
 * @param string             $match
 * @param string             $title
 * @param string             $prefix
 * @param string             $note
 */
function createFT(Detector $detector, $ua, $match, $title, $prefix = '', $note = '')
{
    ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table">
    <div class="text-center clearfix">
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
            <?php
            echo htmlentities($title);
            ?>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">Your Browser</div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">Detector Profile</div>
    </div>
    <?php
    $check = 0;
    foreach ($ua as $key => $value) {
        if (!preg_match($match, $key)) {
            continue;
        }

        $check = 1;
        if (is_object($value)) {
            foreach ($value as $vkey => $vvalue) {
                ?>
                <div class="clearfix">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 text-left">
                        <?php
                        echo htmlentities($key . '->' . $vkey);
                        ?>
                    </div>
                    <?php
                    if ($detector->whereFound() == 'archive') :
                        ?>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4"><span
                                class="label label-info">N/A</span></div>
                    <?php
                    else:
                        ?>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                            <script type="text/javascript">
                                if (Modernizr['<?php echo $prefix.$key; ?>']['<?php echo $vkey; ?>'] === true) {
                                    document.write("<span class='label label-success'>" + Modernizr['<?php echo $prefix.$key; ?>']['<?php echo $vkey; ?>'] + "</span>");
                                } else if (Modernizr['<?php echo $prefix.$key; ?>']['<?php echo $vkey; ?>'] === false) {
                                    document.write("<span class='label label-danger'>" + Modernizr['<?php echo $prefix.$key; ?>']['<?php echo $vkey; ?>'] + "</span>");
                                } else if (Modernizr['<?php echo $prefix.$key; ?>']['<?php echo $vkey; ?>']) {
                                    document.write("<span class='label label-warning'>" + Modernizr['<?php echo $prefix.$key; ?>']['<?php echo $vkey; ?>'] + "</span>");
                                } else {
                                    document.write("<span class='label label-info'>unknown</span>");
                                }
                            </script>
                        </div>
                    <?php
                    endif;
                    ?>

                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4"><?php echo convertTF($vvalue); ?></div>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="clearfix">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 text-left">
                    <?php
                    echo htmlentities($key);
                    ?>
                </div>
                <?php
                if ($detector->whereFound() == 'archive') :
                    ?>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4"><span class="label label-info">N/A</span>
                    </div>
                <?php
                else:
                    ?>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                        <script type="text/javascript">
                            if (Modernizr['<?php echo $prefix.$key; ?>'] === true) {
                                document.write("<span class='label label-success'>" + Modernizr['<?php echo $prefix.$key; ?>'] + "</span>");
                            } else if (Modernizr['<?php echo $prefix.$key; ?>'] === false) {
                                document.write("<span class='label label-danger'>" + Modernizr['<?php echo $prefix.$key; ?>'] + "</span>");
                            } else if (Modernizr['<?php echo $prefix.$key; ?>']) {
                                document.write("<span class='label label-warning'>" + Modernizr['<?php echo $prefix.$key; ?>'] + "</span>");
                            } else {
                                document.write("<span class='label label-info'>unknown</span>");
                            }
                        </script>
                    </div>
                <?php
                endif;
                ?>

                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4"><?php echo convertTF($value); ?></div>
            </div>
        <?php
        }
    }
    if ($check == 0) : ?>
        <div class="clearfix text-left">
            Detector wasn't able to capture these features because they rely on a cookie that was set after the PHP
            script ran.
        </div>
    <?php
    endif;
    ?>
    </div>
    <?php
    if ($note != '') : ?>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 clearfix">
            <small><em><?php echo $note; ?></em></small>
        </div>
    <?php
    endif;
}
