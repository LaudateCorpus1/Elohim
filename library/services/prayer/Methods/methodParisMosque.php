<?php

require_once 'methodBase.php';
require_once APPLICATION_PATH.'/../library/services/prayer/timeNames.php';

class MethodParisMosque extends MethodBase
{
	public function __construct()
	{
		parent::__construct("Grande mosquée de Paris");
		$this->_parameters = array();
		$this->_parameters[TimeNames::Fajr] = 18;
		$this->_parameters[TimeNames::Dhuhr] = "5 min";
		$this->_parameters[TimeNames::Maghrib] = "4 min";
		$this->_parameters[TimeNames::Isha] = 17;
	}
}