<?php
class Core_TranslationFactory
{
    static private $translation    = null;

    /**
     * Gets a translation object.
     *
     * @return Language_SmartyTranslation
     */
    static public function getTranslation()
    {
        if (is_null(self::$translation)) {
            $translation = new Language_SmartyTranslation();
            if (isset($_SESSION['language'])) {
                $translation->setLanguageTag($_SESSION['language']);
            } else {
                $translation->setLanguageTag('en');
            }
            $translation->setDatabase(Core_Controller::getLanguageDatabase());
            $translation->setMemcache(Core_Controller::getMemcache());
            self::$translation = $translation;
        }
        return self::$translation;
    }
}
