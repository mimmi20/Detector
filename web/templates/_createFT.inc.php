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
    print "<table class=\"zebra-striped col-xs-9\">
        <thead>
            <tr>
                <th>".$title."</th>
                <th>Your Browser</th>
                <th>Detector Profile</th>
            </tr>
        </thead>
        <tbody>";
    $check = 0;
    foreach ($ua as $key => $value) {
        if (!preg_match($match, $key)) {
            continue;
        }

        $check = 1;
        if (is_object($value)) {
            //$value_a = (array) $value;
            //ksort($value_a);
            //$value = (object) $value_a;

            foreach ($value as $vkey => $vvalue) {
                print '<tr>';
                print '<th class="col-xs-7">'. $key . '->' . $vkey . ':</th>';
                if ($detector->whereFound() == 'archive') {
                    print '<td class="col-xs-1"><span class="label label-info">N/A</span></td>';
                } else {
                    print "<td class=\"col-xs-1\">
                                <script type=\"text/javascript\">
                                    if (Modernizr['".$prefix.$key."']['".$vkey."'] === true) {
                                        document.write(\"<span class='label label-success'>\"+Modernizr['".$prefix.$key."']['".$vkey."']+\"</span>\");
                                    } else if (Modernizr['".$prefix.$key."']['".$vkey."']) {
                                        document.write(\"<span class='label label-warning'>\"+Modernizr['".$prefix.$key."']['".$vkey."']+\"</span>\");
                                    } else {
                                        document.write(\"<span class='label label-danger'>false</span>\");
                                    }
                                </script>
                               </td>";
                }
                print "<td class=\"col-xs-1\">".convertTF($vvalue)."</td>";
                print "</tr>";
            }
        } else {
            print "<tr>";
            print "<th class=\"col-xs-7\">".$key.":</th>";
            if ($detector->whereFound() == 'archive') {
                print "<td class=\"col-xs-1\"><span class='label label-info'>N/A</span></td>";
            } else {
                print "<td class=\"col-xs-1\">
                            <script type=\"text/javascript\">
                                ";
                if (($prefix == 'core-') && ($key == 'mediaqueries')) {
                    print "        if (Modernizr['mediaqueries']) {
                                            document.write(\"<span class='label label-success'>\"+Modernizr['mediaqueries']+\"</span>\");";
                } else {
                    print "        if (Modernizr['".$prefix.$key."']) {
                                            document.write(\"<span class='label label-success'>\"+Modernizr['".$prefix.$key."']+\"</span>\");";
                }
                print "        } else {
                                    document.write(\"<span class='label label-danger'>false</span>\");
                                }
                            </script>
                           </td>";
            }
            print "<td class=\"col-xs-1\">".convertTF($value)."</td>";
            print "</tr>";
        }
    }
    if ($check == 0) {
        print "<tr>";
        print "<td class=\"col-xs-9\" colspan=\"3\">Detector wasn't able to capture these features because they rely on a cookie that was set after the PHP script ran.</td>";
        print "</tr>";
    }
    print "</tbody>";
    print "</table>";
    if ($note != '') {
        print "<div class=\"featureNote col-xs-9\">";
        print "<small><em>".$note."</em></small>";
        print "</div>";
    }
}
