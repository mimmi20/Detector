<?php
/**
 * @param bool|string $value
 *
 * @return string
 */
function convertTF($value)
{
    if ($value === 'true' || $value === true) {
        return '<span class="label label-success">true</span>';
    } elseif ($value === 'false' || $value === false) {
        return '<span class="label label-danger">false</span>';
    } elseif ($value) {
        return '<span class="label label-warning">' . $value . '</span>';
    } else {
        return '<span class="label label-danger">false</span>';
    }
}
