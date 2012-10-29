<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty signed modifier plugin
 *
 * Type:     modifier<br>
 * Name:     signed<br>
 * Purpose:  signes an integer:
 *            3 => +3
 *           -3 => -3
 *            0 =>  0
 * @author   Daniel Mecke <daniel dot mecke at cunningsoft dot de>
 * @param string
 * @return string
 */
function smarty_modifier_signed($int)
{
    $int = (int)$int;
    if ($int > 0)
    {
        $string = '+' . $int;
    }
    else
    {
        $string = (string)$int;
    }
    return $string;
}

?>
