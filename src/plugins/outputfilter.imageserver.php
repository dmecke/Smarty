<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty imageserver outputfilter plugin
 *
 * File:     outputfilter.imageserver.php<br>
 * Type:     outputfilter<br>
 * Name:     imageserver<br>
 * Date:     Jul 06, 2009<br>
 * Purpose:  prepend an imageserver to each image to
 *           move the load / traffic of static content
 *           to a seperated server.
 *           the server has to be given in the project.conf.
 * Install:  Drop into the plugin directory, call
 *           <code>$smarty->load_filter('output','imageserver');</code>
 *           from application.
 * @author   Daniel Mecke
 * @version  1.0
 * @param string $source
 * @param Smarty $smarty
 * @return string
 */
function smarty_outputfilter_imageserver($source, &$smarty)
{
    $imageserver = Core_Config::getProperty('imageserver');
    if (!empty($imageserver)) {
        $source = str_replace('src="/images/', 'src="' . $imageserver . '/', $source);
        $source = str_replace('background="/images/', 'background="' . $imageserver . '/', $source);
        $source = str_replace('url(/images/', 'url(' . $imageserver . '/', $source);
    }

    return $source;
}

?>
