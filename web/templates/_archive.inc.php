<?php
$i       = 0;
$include = '';
$uaList = $detector->getUaList();
foreach ($uaList as $key => $value) {
    $include .= '<li> <a href="/?pid=' . $key . '">' . trim(substr(strip_tags($value), 0, 28)) . '...</a></li>';
    $i++;
}
?>
<h3>Archive</h3>
<p>
    The following <strong><?php echo $i?></strong> user agent profiles are already in the system (<a href="archive.php">readable list</a>):
</p>
<ul>
    <?php echo $include?>
</ul>
