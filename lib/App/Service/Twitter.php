<?php

class App_Service_Twitter extends Zend_Rest_Client
{
	protected $_authInitialized = false;
	protected $_username;
	protected $_password;
	protected $_cookieJar;

	function __construct($username, $password)
	{
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setUri('http://twitter.com');

		$client = self::getHttpClient();
		$client->setHeaders('Accept-Charset', 'ISO-8859-1,utf-8');
	}

	public function getUsername()
	{
		return $this->_username;
	}

	public function setUsername($username)
	{
		$this->_username = $username;
		$this->_authInitialized = false;
		return $this;
	}

	public function getPassword()
	{
		return $this->_password;
	}

	public function setPassword($password)
	{
		$this->_password = $password;
		$this->_authInitialized = false;
		return $this;
	}

	protected function _init()
	{
		$client = self::getHttpClient();

		$client->resetParameters();

		if ($this->_cookieJar === null)
		{
			$client->setCookieJar();
			$this->_cookieJar = $client->getCookieJar();
		}
		else
		{
			$client->setCookieJar($this->_cookieJar);
		}

		if (!$this->_authInitialized)
		{
			$client->setAuth($this->getUsername(), $this->getPassword());
			$this->_authInitialized = true;
		}
	}

	public function statusPublicTimeline()
	{
		$this->_init();
		$path = '/statuses/public_timeline.xml';
		$response = $this->restGet($path);
		return new Zend_Rest_Client_Result($response->getBody());
	}
}
