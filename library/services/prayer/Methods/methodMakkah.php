<?php

require_once 'methodBase.php';
require_once APPLICATION_PATH.'/../library/services/prayer/timeNames.php';

class MethodMakkah extends MethodBase
{
	public function __construct()
	{
		parent::__construct("Umm Al-Qura University, Makkah");
		$this->_parameters = array();
		$this->_parameters[TimeNames::Fajr] = 18.5;
		$this->_parameters[TimeNames::Isha] = "90 min";
	}
}
