<?php

require_once 'methodBase.php';
require_once APPLICATION_PATH.'/../library/services/prayer/timeNames.php';

class MethodUOIF extends MethodBase
{
	public function __construct()
	{
		parent::__construct("Union des organisations islamiques de France");
		$this->_parameters = array();
		$this->_parameters[TimeNames::Fajr] = 12;
		$this->_parameters[TimeNames::Isha] = 12;
	}
}