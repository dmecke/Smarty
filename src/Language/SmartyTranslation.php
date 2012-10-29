<?php
class Language_SmartyTranslation
{
    /**
     * @var string
     */
    private $_languageTag = null;

    /**
     * @var Database
     */
    private $_database = null;

    /**
     * @var McManager
     */
    private $_memcache = null;

    /**
     * @var array
     */
    private $_phrase = array();

    /**
     * @throws Exception
     * @param string $section
     * @param string $key
     * @param string $param1
     * @param string $param2
     * @param string $param3
     * @param string $param4
     * @param string $param5
     * @return string
     */
    public function getPhrase($section, $key, $param1 = '', $param2 = '', $param3 = '', $param4 = '', $param5 = '')
    {
        if (is_null($this->_languageTag)) {
            throw new Exception('no language set');
        }
        if (is_null($this->_database)) {
            throw new Exception('no database set');
        }
        if (is_null($this->_memcache)) {
            throw new Exception('no memcache set');
        }
        if (empty($this->_phrase[$section][$key])) {
            $memcacheKey = 'language|' . $this->_languageTag;
            $phrase = $this->_memcache->get($memcacheKey);
            if (empty($phrase[$section][$key])) {
                $q = 'SELECT p.translation
                      FROM Phrases p
                      JOIN Languages l ON p.languageId = l.id
                      JOIN TranslationKeys k ON p.keyId = k.id
                      LEFT JOIN Ratings r ON p.id = r.phraseId
                      WHERE l.tag = ? AND k.name = ? AND k.section = ?
                      GROUP BY p.id
                      ORDER BY SUM(IF (r.rating IS NULL, 0, r.rating)) DESC';
                $phrase[$section][$key] = $this->_database->queryScalar($q, $this->_languageTag, $key, $section);
                if (empty($phrase[$section][$key])) {
                    $q = 'SELECT p.translation
                          FROM Phrases p
                          JOIN Languages l ON p.languageId = l.id
                          JOIN TranslationKeys k ON p.keyId = k.id
                          LEFT JOIN Ratings r ON p.id = r.phraseId
                          WHERE l.tag = ? AND k.name = ? AND k.section = ?
                          GROUP BY p.id
                          ORDER BY SUM(IF (r.rating IS NULL, 0, r.rating)) DESC';
                    $phrase[$section][$key] = $this->_database->queryScalar($q, 'en', $key, $section);
                }
                $this->_memcache->set($memcacheKey, $phrase, Date_Delta::ONE_HOUR);
            }
            $q = 'UPDATE TranslationKeys
                  SET usages = usages + 1
                  WHERE name = ? AND section = ?';
            $this->_database->execute($q, $key, $section);
            $this->_phrase = $phrase;
        }
        $myPhrase = $this->_phrase[$section][$key];
        if (isset($param1)) {
            $myPhrase = str_replace('%param1%', $param1, $myPhrase);
        }
        if (isset($param2)) {
            $myPhrase = str_replace('%param2%', $param2, $myPhrase);
        }
        if (isset($param3)) {
            $myPhrase = str_replace('%param3%', $param3, $myPhrase);
        }
        if (isset($param4)) {
            $myPhrase = str_replace('%param4%', $param4, $myPhrase);
        }
        if (isset($param5)) {
            $myPhrase = str_replace('%param5%', $param5, $myPhrase);
        }
        return $myPhrase;
    }

    public function createUser($token)
    {
        if (is_null($this->_database)) {
            throw new Exception('no database set');
        }
        $q = 'INSERT INTO Users
              SET token = ?,
                  isAdmin = 0,
                  languageId = 1,
                  status = "PENDING",
                  insertDate = NOW()';
        $this->_database->execute($q, $token);
        $userId = $this->_database->getInsertID();
        return $userId;
    }

    /**
     * @param Database $database
     * @return void
     */
    public function setDatabase($database)
    {
        $this->_database = $database;
    }

    /**
     * @param string $languageTag
     * @return void
     */
    public function setLanguageTag($languageTag)
    {
        $this->_languageTag = strtolower($languageTag);
    }

    /**
     * @deprecated use self::setLanguageTag() instead
     * @param string $languageTag
     * @return void
     */
    public function setLanguage($languageTag)
    {
        $this->setLanguageTag($languageTag);
    }

    /**
     * @deprecated use self::getLanguageTag() instead
     * @return string
     */
    public function getLanguage()
    {
        return $this->getLanguageTag();
    }

    /**
     * @return string
     */
    public function getLanguageTag()
    {
        return $this->_languageTag;
    }

    /**
     * @todo implement real detection instead of just converting the language tag
     * @return string
     */
    public function getLocale()
    {
        switch ($this->getLanguageTag()) {
            case 'de':
                return 'de_DE';
                break;

            case 'es':
                return 'es_ES';
                break;

            case 'fr':
                return 'fr_FR';
                break;

            case 'it':
                return 'it_IT';
                break;

            case 'pt_br':
                return 'pt_BR';
                break;

            case 'pt':
                return 'pt_PT';
                break;

            case 'pl':
                return 'pl_PL';
                break;

            case 'nl':
                return 'nl_NL';
                break;

            case 'sr':
                return 'sr_RS';
                break;

            case 'sv':
                return 'sv_SE';
                break;

            case 'ru':
                return 'ru_RU';
                break;

            case 'ro':
                return 'ro_RO';
                break;

            case 'hu':
                return 'hu_HU';
                break;

            case 'cs':
                return 'cs_CZ';
                break;

            case 'hr':
                return 'hr_HR';
                break;

            case 'bs':
                return 'bs_BA';
                break;

            case 'tr':
                return 'tr_TR';
                break;

            case 'sk':
                return 'sk_SK';
                break;

            case 'lt':
                return 'lt_LT';
                break;

            case 'sl':
                return 'sl_SI';
                break;

            case 'no':
                return 'nb_NO';
                break;

            case 'fi':
                return 'fi_FI';
                break;

            case 'th':
                return 'th_TH';
                break;

            default:
                return 'en_US';
                break;
        }
    }

    /**
     * Whether a language tag is available or not.
     *
     * @param string $languageTag
     * @return bool
     */
    public function isAvailableLanguageTag($languageTag)
    {
        if (is_null($this->_memcache)) {
            throw new Exception('no memcache set');
        }
        $memcacheKey = 'language|availableLanguageTags';
        $availableLanguageTags = unserialize($this->_memcache->get($memcacheKey));
        if (empty($availableLanguageTags)) {
            $q = 'SELECT tag
                  FROM Languages
                  WHERE status = "ENABLED"';
            $availableLanguageTags = $this->_database->queryScalarArray($q);
            $this->_memcache->set($memcacheKey, serialize($availableLanguageTags), Date_Delta::ONE_HOUR);
        }
        return in_array($languageTag, $availableLanguageTags);
    }

    /**
     * @depracated dummy method; do not use!
     * @return array
     */
    public function getAllPhrases()
    {
        return array();
    }

    /**
     * Gets the user's browserlanguage.
     *
     * @return string
     */
    static public function getBrowserlanguage()
    {
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserlanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($browserlanguages as $browserlanguage) {
                if (strtolower($browserlanguage) == 'pt-br') {
                    $language = 'pt_br';
                    break;
                }
                $browserlanguage = substr($browserlanguage, 0, 2);
                switch ($browserlanguage) {
                    case 'de':
                        $language = 'de';
                        break 2;

                    case 'sv':
                        $language = 'sv';
                        break 2;

                    case 'nl':
                        $language = 'nl';
                        break 2;

                    case 'es':
                        $language = 'es';
                        break 2;

                    case 'fr':
                        $language = 'fr';
                        break 2;

                    case 'pt':
                        $language = 'pt';
                        break 2;

                    case 'it':
                        $language = 'it';
                        break 2;

                    case 'ru':
                        $language = 'ru';
                        break 2;

                    case 'ro':
                        $language = 'ro';
                        break 2;

                    case 'en':
                        $language = 'en';
                        break 2;

                    case 'pl':
                        $language = 'pl';
                        break 2;

                    case 'hu':
                        $language = 'hu';
                        break 2;

                    case 'cs':
                        $language = 'cs';
                        break 2;

                    default:
                        $language = 'en';
                        break 2;
                }
            }
        }
        if (empty($language)) {
            $language = 'en';
        }
        return $language;
    }

    /**
     * @param McManager $memcache
     * @return void
     */
    public function setMemcache($memcache)
    {
        $this->_memcache = $memcache;
    }

    /**
     * @return \McManager
     */
    public function getMemcache()
    {
        return $this->_memcache;
    }

    /**
     * @param string $tag
     * @return string
     */
    public function getNameByTag($tag)
    {
        switch ($tag) {
            case 'en':
                $name = 'English';
                break;

            case 'de':
                $name = 'Deutsch';
                break;

            case 'es':
                $name = 'Español';
                break;

            case 'fr':
                $name = 'Français';
                break;

            case 'pl':
                $name = 'Polski';
                break;

            case 'pt_br':
                $name = 'Brasileiro';
                break;

            case 'cs':
                $name = 'Česky';
                break;

            case 'hr':
                $name = 'Hrvatski';
                break;

            case 'it':
                $name = 'Italiano [beta]';
                break;

            case 'pt':
                $name = 'Português [beta]';
                break;

            case 'sv':
                $name = 'Svenska [beta]';
                break;

            case 'nl':
                $name = 'Nederlands [beta]';
                break;

            case 'ru':
                $name = 'Pyccĸий [beta]';
                break;

            case 'hu':
                $name = 'Magyar [beta]';
                break;

            case 'ro':
                $name = 'Română [beta]';
                break;

            case 'sk':
                $name = 'Slovenčina [beta]';
                break;

            case 'lt':
                $name = 'Lietuvių Kalba [beta]';
                break;

            case 'sr':
                $name = 'Српски [beta]';
                break;

            case 'lv':
                $name = 'Latviešu Valoda [beta]';
                break;

            case 'sl':
                $name = 'Slovenščina [beta]';
                break;

            case 'tr':
                $name = 'Türkçe [beta]';
                break;

            default:
                $name = '';
                break;
        }
        return $name;
    }
}
