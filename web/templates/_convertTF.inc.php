<?php
/**
 * @param bool|string $value
 *
 * @return string
 */
function convertTF($value)
{
    if ($value === 'true' || $value === true) {
        return '<span class="label success">true</span>';
    } elseif ($value === 'false' || $value === false) {
        return '<span class="label important">false</span>';
    } elseif ($value) {
        return '<span class="label warning">' . $value . '</span>';
    } else {
        return '<span class="label important">false</span>';
    }
}
