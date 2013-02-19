<?php

require_once 'methodBase.php';
require_once APPLICATION_PATH.'/../library/services/prayer/timeNames.php';

class MethodJafari extends MethodBase
{
	public function __construct()
	{
		parent::__construct("Shia Ithna-Ashari, Leva Institute, Qum");
		$this->_parameters = array();
		$this->_parameters[TimeNames::Fajr] = 16;
		$this->_parameters[TimeNames::Isha] = 14;
		$this->_parameters[TimeNames::Maghrib] = 4;
		$this->_parameters[TimeNames::Midnight] = 1; // Jafari
	}
}

