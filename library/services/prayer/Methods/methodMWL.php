<?php

require_once 'methodBase.php';
require_once APPLICATION_PATH.'/../library/services/prayer/timeNames.php';

class MethodMWL extends MethodBase
{
	public function __construct()
	{
		parent::__construct("Muslim World League");
		$this->_parameters = array();
		$this->_parameters[TimeNames::Fajr] = 18;
		$this->_parameters[TimeNames::Isha] = 17;
	}
}
