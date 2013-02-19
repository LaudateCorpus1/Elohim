<?php

require_once 'methodBase.php';
require_once APPLICATION_PATH.'/../library/services/prayer/timeNames.php';

class MethodISNA extends MethodBase
{
	public function __construct()
	{
		parent::__construct("Islamic Society of North America (ISNA)");
		$this->_parameters = array();
		$this->_parameters[TimeNames::Fajr] = 15;
		$this->_parameters[TimeNames::Isha] = 15;
	}
}
