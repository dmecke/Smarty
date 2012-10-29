<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Include the {@link shared.make_timestamp.php} plugin
 */
require_once $smarty->_get_plugin_filepath('shared','make_timestamp');
/**
 * Smarty duration_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     duration_format<br>
 * Purpose:  format datestamps via strftime<br>
 * Input:<br>
 *         - string: input duration in seconds
 *         - format: output in %H hours (two digits)
 *                             %h hours (one or two digits)
 *                             %M minutes (two digits)
 *                             %m minutes (one or two digits)
 *                             %S seconds (two digits)
 *                             %s seconds (one or two digits)
 * @author Daniel Mecke
 * @param int
 * @param string
 * @return string
 * @uses smarty_make_timestamp()
 */
function smarty_modifier_duration_format($seconds, $format = "%H:%M:%S")
{
    $time = array('H' => floor($seconds / 3600),
                  'M' => floor($seconds % 3600 / 60),
                  'S' => $seconds % 60,
                  'h' => floor($seconds / 3600),
                  'm' => floor($seconds % 3600 / 60),
                  's' => $seconds % 60);
    if ($time['H'] < 10) $time['H'] = '0' . $time['H'];
    if ($time['M'] < 10) $time['M'] = '0' . $time['M'];
    if ($time['S'] < 10) $time['S'] = '0' . $time['S'];
    $string = $format;
    $string = str_replace('%H', $time['H'], $string);
    $string = str_replace('%M', $time['M'], $string);
    $string = str_replace('%S', $time['S'], $string);
    $string = str_replace('%h', $time['h'], $string);
    $string = str_replace('%m', $time['m'], $string);
    $string = str_replace('%s', $time['s'], $string);
    return $string;
}

/* vim: set expandtab: */

?>
