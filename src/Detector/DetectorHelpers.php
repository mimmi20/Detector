<?php
/*!
 * Detector Helpers v0.1
 *
 * Features that can be used to add extra functionality to your page but aren't required
 *
 * Copyright (c) 2011-2012 Dave Olsen, http://dmolsen.com
 * Licensed under the MIT license
 */

namespace Detector;

class DetectorHelpers
{

    /**
     * Adds classes to the HTML tag if necessary
     *
     * @param \stdClass   $obj          the user agent features
     * @param string|null $features     list of browser features to include in the css, bad idea to leave features blank...
     * @param bool        $printUAProps
     *
     * @return string
     */
    public static function createHTMLList(
        $obj,
        $features = null,
        $printUAProps = false
    ) {
        $features_a = array();
        $output     = '';

        if ($features !== null) {
            $features_a = explode(',', $features);
            array_walk($features_a, create_function('&$val', '$val = trim($val);'));
        }

        foreach ($obj as $key => $value) {
            if (is_object($value)) {
                foreach ($value as $vkey => $vvalue) {
                    $vkey = $key . '-' . $vkey;
                    if (!$features || in_array($vkey, $features_a)) {
                        $result = ($vvalue) ? $vkey : 'no-' . $vkey;
                        $output .= $result . ' ';
                    }
                }
            } else {
                if (!$features || in_array($key, $features_a)) {
                    $result = ($value) ? $key : 'no-' . $key;
                    $output .= $result . ' ';
                }
            }
        }

        if ($printUAProps) {
            $uaProps = array('os', 'osFull', 'browserFull', 'device', 'deviceFull');

            foreach ($uaProps as $uaProp) {
                if (!isset($obj->$uaProp) || !is_string($obj->$uaProp)) {
                    continue;
                }

                $output .= str_replace(' ', '-', strtolower($obj->$uaProp)) . ' ';
            }
        }

        return $output;
    }

    /**
     * Adds a JavaScript object to the page so features collected on the server can be used client-side
     *
     * @param  \stdClass   $obj      the user agent features
     * @param  string|null $features list of browser features to include in the css, bad idea to leave features blank...
     *
     * @return string
     */
    public static function createJavaScriptObj(
        $obj,
        $features = null
    ) {
        $output  = '<script type="text/javascript">';
        $output .= 'Detector=new Object();';

        $features_a = array();

        if ($features) {
            $features_a = explode(',', $features);
            array_walk($features_a, create_function('&$val', '$val = trim($val);'));
        }
        foreach ($obj as $key => $value) {
            if (is_object($value)) {
                $i = 0;
                foreach ($value as $vkey => $vvalue) {
                    if (!$features || in_array($key . '-' . $vkey, $features_a)) {
                        if ($i == 0) {
                            $output .= 'Detector.' . $key . "=new Object();\n";
                            $i++;
                        }
                        $vkey = str_replace('-', '', $vkey);
                        if ($vvalue) {
                            $output .= 'Detector.' . $key . '.' . $vkey . "=true;\n";
                        } else {
                            $output .= 'Detector.' . $key . '.' . $vkey . "=false;\n";
                        }
                    }
                }
            } else {
                if (!$features || in_array($key, $features_a)) {
                    $key = str_replace('-', '', $key);
                    if ($value === true) {
                        $output .= 'Detector.' . $key . "=true;\n";
                    } else if ($value == false) {
                        $output .= 'Detector.' . $key . "=false;\n";
                    } else {
                        $output .= 'Detector.' . $key . "='" . $value . "';\n";
                    }
                }
            }
        }

        $output .= '</script>';

        return $output;
    }
}
