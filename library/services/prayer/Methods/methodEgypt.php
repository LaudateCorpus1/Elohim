<?php

require_once 'methodBase.php';
require_once APPLICATION_PATH.'/../library/services/prayer/timeNames.php';

class MethodEgypt extends MethodBase
{
	public function __construct()
	{
		parent::__construct("Egyptian General Authority of Survey");
		$this->_parameters = array();
		$this->_parameters[TimeNames::Fajr] = 19.5;
		$this->_parameters[TimeNames::Isha] = 17.5;
	}
}
