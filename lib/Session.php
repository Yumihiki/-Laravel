<?php

class Session
{
    protected $bag;

    public function __construct($namespace = 'app')
    {
        if (!session_id()) {
            session_start();
        }

        $this->bag = &$_SESSION[$namespace];

        if (!isset($this->bag)) {
            $this->bag[$this->getAppDataKey()] = [];
            if (!$this->getCsrToken()) {
                $this->bag[$this->getCsrTokenKey()] = $this->generateCsrToken();
            }
        }
    }

    public function getAppDataKey()
    {
        return 'app_data';
    }

    public function getCsrTokenKey()
    {
        return 'csrf_token';
    }

    public function getRequestCsrTokenKey()
    {
        return '__csrf_token';
    }

    public function generateCsrfToken()
    {
        return sha1(uniqid(rand(), true));
    }

    public function getCsrToken()
    {
        return array_get($this->bag, $this->getCsrTokenKey());
    }

    public function verifyCsrToken()
    {
        $request_token = request_get($this->getRequestCsrTokenKey());
        $valid_token   = $this->getCsrfToken();

        return $request_token === $valid_token;
    }

    public function get($key, $default = null)
    {
        return array_get($this->bag[$this->getAppDataKey()], $key, $default);
    }

    public function set($key, $value)
    {
        return $this->bag[$this->getAppDatakey()][$key] = $value;
    }

    public function unset($key)
    {
        unset($this->bag[$this->getAppDataKey()][$key]);
    }

    public function unsetAll()
    {
        $this->bag[$this->getAppDataKey()] = [];
    }

    public function flash($key, $default)
    {
        $value = $this->get($key, $default);
        $this->unset($key);

        return $value;
    }
}
