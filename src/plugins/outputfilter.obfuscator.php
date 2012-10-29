<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty obfuscator outputfilter plugin
 *
 * File:     outputfilter.obfuscator.php<br>
 * Type:     outputfilter<br>
 * Name:     obfuscator<br>
 * Date:     Aug 22, 2008<br>
 * Purpose:  Obfuscates the given template.
 * Install:  Drop into the plugin directory, call
 *           <code>$smarty->load_filter('output','obfuscator');</code>
 *           from application.
 * @author   Daniel Mecke
 * @version  1.0
 * @param string
 * @param Smarty
 */
function smarty_outputfilter_obfuscator($source, &$smarty)
{
    $source = str_replace("\r\n", '', $source);
    $source = str_replace("\n", '', $source);

    return $source;
}

?>
