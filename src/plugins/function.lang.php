<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {lang} function plugin
 *
 * Type:     function<br>
 * Name:     lang<br>
 * Date:     August 16, 2008<br>
 * Purpose:  translate textphrase
 * @author   Daniel Mecke
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_lang($params, &$smarty)
{
    $lang = Core_TranslationFactory::getTranslation();

    if (!isset($params['key']))
    {
        $smarty->trigger_error("lang: missing 'key' parameter");
        return;
    }

    if (!isset($params['section']))
    {
        $smarty->trigger_error("lang: missing 'section' parameter");
        return;
    }

    if ($params['key'] == '')
    {
        return;
    }

    for ($i = 1; $i <= 5; $i++)
    {
        $params['param' . $i] = isset($params['param' . $i]) ? $params['param' . $i] : '';
    }

    $phrase = $lang->getPhrase($params['section'], $params['key'], $params['param1'], $params['param2'], $params['param3'], $params['param4'], $params['param5']);

    if (isset($params['escaped'])) {
        $phrase = addslashes($phrase);
    }

    return $phrase;
}
?>
