<?php
$i       = 0;
$include = '';
$uaList = $detector->getUaList();
?>
<h3>Archive</h3>
<p>
    The following <strong><?php echo htmlentities(count($uaList));?></strong> user agent profiles are already in the system (<a href="archive.php">readable list</a>):
</p>
<ul>
    <?php
    foreach ($uaList as $key => $value) : ?>
        <li><a href="/?pid=<?php echo htmlentities($key); ?>"><?php echo htmlentities(trim(substr(strip_tags($value), 0, 28))); ?>...</a></li>
    <?php
    endforeach;
    ?>
</ul>
