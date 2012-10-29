<?php
class McManager
{
    private $memcache = null;

    /**
     * @var bool
     */
    private $_debug = false;

    private function __construct()
    {
        if (class_exists('Memcache')) {
            $this->memcache = new Memcache();
            $this->memcache->connect(Core_Config::getProperty('MC_HOST'), Core_Config::getProperty('MC_PORT'));
        }
    }

    public function __destruct()
    {
        if (isset($this->memcache)) $this->memcache->close();
    }

    static public function getInstance()
    {
        static $instance;
        if ($instance == null)
        {
            $instance = new McManager();

        }
        return $instance;
    }

    /**
     * @return void
     */
    public function enableDebugging()
    {
        $this->_debug = true;
    }

    public function set($key, $value, $expire)
    {
        if (isset($this->memcache)) {
            $start = microtime(true);
            $result = $this->memcache->set(Core_Config::getProperty('projectID') . '_' . $key, $value, 0, $expire);
            $end = microtime(true);

            if ($this->_debug) {
                $query = new Util_Logger_Query_Memcache();
                $query->setExecutionTime($end - $start);
                $query->setKey($key);
                $query->setBacktrace(debug_backtrace());
                Util_Logger_Logger::getInstance()->addMemcacheKeySave($query);
            }

            return $result;
        } else {
            return false;
        }
    }

    public function get($key)
    {
        if (isset($this->memcache)) {
            $start = microtime(true);
            $result = $this->memcache->get(Core_Config::getProperty('projectID') . '_' . $key);
            $end = microtime(true);

            if ($this->_debug) {
                $query = new Util_Logger_Query_Memcache();
                $query->setExecutionTime($end - $start);
                $query->setKey($key);
                $query->setBacktrace(debug_backtrace());
                Util_Logger_Logger::getInstance()->addMemcacheKeyLoad($query);
            }

            return $result;
        } else {
            return false;
        }
    }

    public function delete($key)
    {
        if (isset($this->memcache)) {
            return $this->memcache->delete(Core_Config::getProperty('projectID') . '_' . $key);
        } else {
            return false;
        }
    }

    /**
     * Gets a part of a stored array.
     *
     * @param $key string
     * @param $offset int[optional]
     * @param $limit int[optional]
     * @return array
     */
    public function getArray($key, $offset = 0, $limit = null)
    {
        $array = array();
        $data = $this->get($key);
        for ($i = $offset; isset($data[$i]) && (is_null($limit) || $i < $offset + $limit); $i++) {
            $array[] = $data[$i];
        }
        return $array;
    }

    public function getServerStatus($host, $port = 11211)
    {
        if (isset($this->memcache)) {
            return $this->memcache->getServerStatus($host, $port);
        } else {
            return false;
        }
    }

    private function getStats()
    {
        if (isset($this->memcache))
        {
            return $this->memcache->getStats();
        }
        else
        {
            return false;
        }
    }

    public function getUptime()
    {
        $stats = $this->getStats();
        $time = $stats['uptime'];

        $days = floor($time / 86400);
        $time -= $days * 86400;

        $hours = floor($time / 3600);
        $time -= $hours * 3600;

        $minutes = floor($time / 60);
        $time -= $minutes * 60;

        $seconds = $time;

        return $days . 'd ' . $hours . 'h ' . $minutes . 'm ' . $seconds . 's';
    }

    public function getCurrentItems()
    {
        $stats = $this->getStats();

        return $stats['curr_items'];
    }

    public function getTotalItems()
    {
        $stats = $this->getStats();

        return $stats['total_items'];
    }

    public function getBytes()
    {
        $stats = $this->getStats();

        return floor($stats['bytes'] / 1024 / 1024) . 'MB';
    }

    public function getVersion()
    {
        if (isset($this->memcache))
        {
            return $this->memcache->getVersion();
        }
        else
        {
            return false;
        }
    }
}
