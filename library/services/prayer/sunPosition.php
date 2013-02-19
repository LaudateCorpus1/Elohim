<?php

require_once 'utils.php';
require_once 'degreeMath.php';

class SunPosition
{
	private $_equation;
	private $_declinaison;
	// Compute declination angle of sun and equation of time
	// Ref: http://aa.usno.navy.mil/faq/docs/SunApprox.php
	public function __construct($julianDate)
	{
		$julianDate = doubleval($julianDate);
		$D = $julianDate - 2451545.0;
		$g = Utils::FixAngle(357.529 + 0.98560028 * $D);
		$q = Utils::FixAngle(280.459 + 0.98564736 * $D);
		$L = Utils::FixAngle($q + 1.915 * DegreeMath::Sin($g) + 0.020 * DegreeMath::Sin(2 * $g));

		$R = 1.00014 - 0.01671 * DegreeMath::Cos($g) - 0.00014 * DegreeMath::Cos(2 * $g);
		$e = 23.439 - 0.00000036 * $D;

		$RA = DegreeMath::Atan2(DegreeMath::Cos($e) * DegreeMath::Sin($L), DegreeMath::Cos($L)) / 15;
		$this->setEquation($q / 15 - Utils::FixHour($RA));
		$this->setDeclinaison(DegreeMath::Asin(DegreeMath::Sin($e) * DegreeMath::Sin($L)));
	}
	
	public function getEquation()
	{
		return $this->_equation;
	}
	
	public function setEquation($equation)
	{
		$this->_equation = $equation;
	}
	
	public function getDeclinaison()
	{
		return $this->_declinaison;
	}
	
	public function setDeclinaison($declinaison)
	{
		$this->_declinaison = $declinaison;
	}
}

?>