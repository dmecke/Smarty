<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty imageversion outputfilter plugin
 *
 * File:     outputfilter.imageversion.php<br>
 * Type:     outputfilter<br>
 * Name:     imageversion<br>
 * Date:     Aug 22, 2008<br>
 * Purpose:  add a version number to the end of each image to
 *           be able to force users to reload them. the version
 *           has to be given in the project.conf.
 * Install:  Drop into the plugin directory, call
 *           <code>$smarty->load_filter('output','imageversion');</code>
 *           from application.
 * @author   Daniel Mecke
 * @version  1.0
 * @param string $source
 * @param Smarty $smarty
 * @return string
 */
function smarty_outputfilter_imageversion($source, &$smarty)
{
    $cacheVersion = Core_GameInfo::getInstance()->getSvnRevision();
    if (!empty($cacheVersion)) {
        $source = str_replace('.jpg', '.jpg?v=' . $cacheVersion, $source);
        $source = str_replace('.png', '.png?v=' . $cacheVersion, $source);
        $source = str_replace('.gif', '.gif?v=' . $cacheVersion, $source);
        $source = str_replace('.js',  '.js?v=' . $cacheVersion,  $source);
        $source = str_replace('.css', '.css?v=' . $cacheVersion, $source);
    }

    return $source;
}

?>
