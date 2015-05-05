<?php

require_once 'methodBase.php';
require_once APPLICATION_PATH.'/../library/services/prayer/timeNames.php';

class MethodMorocco extends MethodBase
{
	public function __construct()
	{
		parent::__construct("Maroc, Ministère des Habous et des Affaires Islamiques");
		$this->_parameters = array();
		$this->_parameters[TimeNames::Fajr] = 19;
		$this->_parameters[TimeNames::Dhuhr] = "6 min";
		$this->_parameters[TimeNames::Maghrib] = "5 min";
		$this->_parameters[TimeNames::Isha] = 17;
	}
}