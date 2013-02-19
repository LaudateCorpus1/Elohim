<?php

require_once 'methodBase.php';
require_once APPLICATION_PATH.'/../library/services/prayer/timeNames.php';

class MethodTehran extends MethodBase
{
	public function __construct()
	{
		parent::__construct("Institute of Geophysics, University of Tehran");
		$this->_parameters = array();
		$this->_parameters[TimeNames::Fajr] = 17.7;
		$this->_parameters[TimeNames::Isha] = 14;
		$this->_parameters[TimeNames::Maghrib] = 4.5;
		$this->_parameters[TimeNames::Midnight] = 1; // Jafari
	}
}

