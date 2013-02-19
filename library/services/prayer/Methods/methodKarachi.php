<?php

require_once 'methodBase.php';
require_once APPLICATION_PATH.'/../library/services/prayer/timeNames.php';

class MethodKarachi extends MethodBase
{
	public function __construct()
	{
		parent::__construct("University of Islamic Sciences, Karachi");
		$this->_parameters = array();
		$this->_parameters[TimeNames::Fajr] = 18;
		$this->_parameters[TimeNames::Isha] = 18;
	}
}

