<h3><?php echo  ($detector->whereFound() == 'archive') ? 'Archived' : 'Your'; ?> Detector Browser Profile</h3>
<p>
    The following browser profile was created using <a href="https://github.com/ua-parser/uap-php">PHP implementation of ua-parser</a>. This information
    is derived solely from the user agent string for your browser.
</p>
<?php
if ($detector->whereFound() == 'archive') {
    $uaList = $detector->getUaList();
    $i      = 0;
    $oldkey = '';

    foreach ($uaList as $key => $value) {
        if ($i == 1) {
            $next = $key;
            break;
        }
        if ($key == $ua->uaHash) {
            $previous = $oldkey;
            $i = 1;
        }
        $oldkey = $key;
    }
}
?>

<table class="zebra-striped span9">
    <thead>
    <tr>
        <th colspan="2">Browser Properties <span style="float: right; font-weight: normal; font-size: 12px;">
            <?php if (isset($previous) && ($previous != '')) { ?>
                <a href="/?pid=<?php echo $previous?>">Previous Profile</a>
            <?php
            } ?>
                <?php if ((isset($next) && ($next != '')) && (isset($previous) && ($previous != ''))) { ?>
                    |
                <?php
                } ?>
                <?php if (isset($next) && ($next != '')) { ?>
                    <a href="/?pid=<?php echo $next?>">Next Profile</a>
                <?php
                } ?></span></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="span3">User Agent:</th>
        <?php if (is_string($ua->originalUserAgent)): ?>
        <td><?php echo $ua->originalUserAgent; ?></td>
        <?php else: ?>
        <td>
            Browser Information: <?php echo $ua->originalUserAgent->browser; ?><br/>
            Device Information: <?php echo $ua->originalUserAgent->device; ?>
        </td>
        <?php endif; ?>
    </tr>
    <tr>
        <th class="span3">UA Hash:</th>
        <td><?php echo $ua->uaHash; ?></td>
    </tr>
    <?php if (isset($ua->ua) && ($ua->ua != '')) { ?>
        <tr>
            <th>Browser:</th>
            <td><?php echo $ua->ua->family . ' ' . $ua->ua->major . '.' . $ua->ua->minor; ?></td>
        </tr>
    <?php
    } ?>
    <?php if (isset($ua->os) && ($ua->os != '')) { ?>
        <tr>
            <th>OS:</th>
            <td><?php echo $ua->os->family; ?></td>
        </tr>
    <?php
    } ?>
    <?php if (isset($ua->device) && ($ua->device != '')) { ?>
        <tr>
            <th>Device:</th>
            <td><?php echo $ua->device->family; ?></td>
        </tr>
    <?php
    } ?>
    <?php if (isset($ua->browser) && ($ua->browser == 'Mobile Safari')) { ?>
        <tr>
            <th>Is UIWebview?</th>
            <td><?php echo convertTF($ua->isUIWebview)?></td>
        </tr>
    <?php
    } ?>

    <tr>
        <th>Is Mobile?</th>
        <td>
            <?php
            if (isset($ua->isMobile)) {
                print convertTF($ua->isMobile);
            } else {
                print "<span class='label important'>false</span>";
            }
            ?>
        </td>
    </tr>
    <tr>
        <th>Is Mobile Device?</th>
        <td>
            <?php
            if (isset($ua->isMobileDevice)) {
                print convertTF($ua->isMobileDevice);
            } else {
                print "<span class='label important'>false</span>";
            }
            ?>
        </td>
    </tr>
    <tr>
        <th>Is Tablet?</th>
        <td>
            <?php
            if (isset($ua->isTablet)) {
                print convertTF($ua->isTablet);
            } else {
                print "<span class='label important'>false</span>";
            }
            ?>
        </td>
    </tr>
    <tr>
        <th>Is Computer?</th>
        <td>
            <?php
            if (isset($ua->isComputer)) {
                print convertTF($ua->isComputer);
            } else {
                print "<span class='label important'>false</span>";
            }
            ?>
        </td>
    </tr>
    <tr>
        <th>Is Spider?</th>
        <td>
            <?php
            if (isset($ua->isSpider)) {
                print convertTF($ua->isSpider);
            } else {
                print "<span class='label important'>false</span>";
            }
            ?>
        </td>
    </tr>
    </tbody>
</table>

<p>
    <strong>Something wrong with this profile?</strong> Please, <a href="/contact.php?cid=<?php echo $ua->uaHash?>">let me know</a>. Note that
    the <strong>"tablet" classification may be incorrect</strong> for those Android tablets using an OS older than Android 3.0.
</p>