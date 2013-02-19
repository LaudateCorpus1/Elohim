<?php
require_once 'utils.php';
require_once 'degreeMath.php';
require_once 'sunPosition.php';
require_once 'timeNames.php';
require_once 'Methods/methodBase.php';
require_once 'Methods/methodUOIF.php';
require_once 'Methods/methodEgypt.php';
require_once 'Methods/methodISNA.php';
require_once 'Methods/methodJafari.php';
require_once 'Methods/methodKarachi.php';
require_once 'Methods/methodMakkah.php';
require_once 'Methods/methodMWL.php';
require_once 'Methods/methodTehran.php';


class AsrMethod
{
	const Standard = 0; // Shafi`i, Maliki, Ja`fari, Hanbali
	const Hanafi = 1;   // Hanafi
}

class MidnightMethod
{
	const Standard = 0; // Mid Sunset to Sunrise
	const Jafari = 1;   // Mid Sunset to Fajr
}

class HighLatitudeMethod
{
	const NightMiddle = 0; // middle of night
	const AngleBased = 1;  // angle/60th of night
	const OneSeventh = 2;  // 1/7th of night
	const None = 3;         // No adjustment
}

class TimeFormat
{
	const Format24h = 0;         // 24-hour format
	const Format12h = 1;         // 12-hour format
	const Format12hNS = 2;       // 12-hour format with no suffix
	const FormatFloat = 3;        // floating point number 
}

class PrayerTime
{
	private $_timeFormat;
	private $_latitude;
	private $_longitude;
	private $_elv;
	private $_timeZone;
	private $_julianDate;
	private $_numIterations;

	private $_settings = array();
	private $_offsets = array();
	private $_defaultParams = array();
	private $_currentMethod;

	#region Properties
	/*public TimeFormat TimeFormat
	{
		get { return timeFormat; }
		set { timeFormat = value; }
	}

	public MethodBase CurrentMethod
	{
		get { return currentMethod; }
		set { currentMethod = value; Adjust(currentMethod.Parameters); }
	}*/

	
	public function __construct($method)
	{
		if ($method == null)
			throw new Exception("Method must be specified");
		else
			$this->_currentMethod = $method;

		$this->_timeFormat = TimeFormat::Format24h;

		// Do not change anything here; use adjust method instead
		$this->_settings[TimeNames::Imsak] = "10 min";
		$this->_settings[TimeNames::Dhuhr] = "0 min";
		$this->_settings[TimeNames::Asr] = AsrMethod::Standard;
		$this->_settings["HighLats"] = HighLatitudeMethod::AngleBased;

		//timeSuffixes = ['am', 'pm'],
		//invalidTime =  '-----',

		$this->_numIterations = 1;

		// Default Parameters in Calculation Methods
		$this->_defaultParams[TimeNames::Maghrib] = "0 min";
		$this->_defaultParams[TimeNames::Midnight] = MidnightMethod::Standard;

		$parameters = $this->_currentMethod->getParameters();
		foreach ($this->_defaultParams as $key => $item)
		{
			if(!isset($parameters[$key]))
				$parameters[$key] = $item;
		}
		$this->_currentMethod->setParameters($parameters);

		// Initialize settings
		foreach ($parameters as $key => $item)
		{
			$this->_settings[$key] = $item;
		}
		
		foreach (TimeNames::$TimeName as $timeName)
		{
			$this->_offsets[$timeName] = 0;
		}
	}
	
	public function Adjust(array $parameters)
	{
		foreach ($parameters as $key => $item)
		{
			$this->_settings[$key] = $item;
		}
	}

	public function Tune(array $timeOffsets)
	{
		foreach ($timeOffsets as $key => $item)
		{
			$this->_offsets[$key] = $item;
		}
	}

	public function GetFormattedTime($time, $format)
	{
		if ($format == TimeFormat::FormatFloat)
			return $time;

		$time = doubleval($time);
		$time = Utils::FixHour($time + 1.5 / 60); // add 0.5 minutes to round
		$hours = floor($time); 
		$minutes = floor(($time - $hours) * 60);
		$hour = ($format == TimeFormat::Format24h) ? Utils::TwoDigitsFormat($hours) : (($hours + 12 -1) % 12 + 1);
		return $hour.':'.Utils::TwoDigitsFormat($minutes);
	}
                
	public function GetTimes($date, $latitude, $longitude = null, $timeZone = null, $dst = null, $timeFormat = TimeFormat::Format24h, $elv = 0) 
	{
		// Pour les latitudes supérieures à 66, on prend l'équivalent à 45 sinon le calcul est impossible
		if ($latitude > 66)
			$this->_latitude = 45;
		else
			$this->_latitude = $latitude;

		if ($longitude != null)
			$this->_longitude = $longitude;

		$this->_elv = $elv;

		$this->_timeFormat = $timeFormat;

		if($timeZone == null)
			$timeZone = GetTimeZone($date);

		if ($dst == null)
			$dst = GetDst($date);

		$this->_timeZone = intval($timeZone) + intval($dst);
		$this->_julianDate = Utils::GregorianDateToJulianDay($date);

		return $this->ComputeTimes();
	}
	
	private function MidDay($time)
	{
		$sunPosition = new SunPosition(doubleval($this->_julianDate) + doubleval($time));
		$eqt = $sunPosition->getEquation();
		$noon = Utils::FixHour(12 - $eqt);
		return $noon;
	}

	// Compute the time at which sun reaches a specific angle below horizon
	private function SunAngleTime($angle, $time, $direction = "")
	{
		$sunPosition = new SunPosition(doubleval($this->_julianDate) + $time);
		$decl = $sunPosition->getDeclinaison();
		$noon = $this->MidDay($time);
		$t = 1/15.0 * DegreeMath::Acos((-DegreeMath::Sin($angle) - DegreeMath::Sin($decl)* DegreeMath::Sin($this->_latitude))/ 
				(DegreeMath::Cos($decl) * DegreeMath::Cos($this->_latitude)));
		return $noon + ($direction == "ccw" ? $t * -1 : $t);
	}

	// Compute asr time 
	private function AsrTime($factor, $time)
	{
		$sunPosition = new SunPosition(doubleval($this->_julianDate) + $time);
		$decl = $sunPosition->getDeclinaison();
		$angle = -DegreeMath::Acot($factor + DegreeMath::Tan(abs($this->_latitude - $decl)));
		return $this->SunAngleTime($angle, $time);
	}
	
	// Compute prayer times at given julian date
	private function ComputePrayerTimes($times)
	{
		$prayerTimes = array();
		$times = $this->DayPortions($times);

		$prayerTimes[TimeNames::Imsak]   = $this->SunAngleTime(Utils::Evalu($this->_settings[TimeNames::Imsak]), doubleval($times[TimeNames::Imsak]), "ccw");
		$prayerTimes[TimeNames::Fajr]    = $this->SunAngleTime(Utils::Evalu($this->_settings[TimeNames::Fajr]), doubleval($times[TimeNames::Fajr]), "ccw");
		$prayerTimes[TimeNames::Sunrise] = $this->SunAngleTime($this->RiseSetAngle(), doubleval($times[TimeNames::Sunrise]), "ccw");
		$prayerTimes[TimeNames::Dhuhr]   = $this->MidDay(Utils::Evalu($this->_settings[TimeNames::Dhuhr]));
		$prayerTimes[TimeNames::Asr]     = $this->AsrTime($this->AsrFactor($this->_settings[TimeNames::Asr]), doubleval($times[TimeNames::Asr]));
		$prayerTimes[TimeNames::Sunset]  = $this->SunAngleTime($this->RiseSetAngle(), doubleval($times[TimeNames::Sunset]));
		$prayerTimes[TimeNames::Maghrib] = $this->SunAngleTime(Utils::Evalu($this->_settings[TimeNames::Maghrib]), doubleval($times[TimeNames::Maghrib]));
		$prayerTimes[TimeNames::Isha]    = $this->SunAngleTime(Utils::Evalu($this->_settings[TimeNames::Isha]), doubleval($times[TimeNames::Isha]));

		return $prayerTimes;
	}

	// Compute prayer times
	private function ComputeTimes()
	{
		// Default times
		$times = array();
		$times[TimeNames::Imsak]   = 5;
		$times[TimeNames::Fajr]    = 5;
		$times[TimeNames::Sunrise] = 6;
		$times[TimeNames::Dhuhr]   = 12;
		$times[TimeNames::Asr]     = 13;
		$times[TimeNames::Sunset]  = 18;
		$times[TimeNames::Maghrib] = 18;
		$times[TimeNames::Isha]    = 18;

		for ($i = 1; $i <= $this->_numIterations; $i++)
		{
			$times = $this->ComputePrayerTimes($times);
		}

		$times = $this->AdjustTimes($times);

		// add midnight time
		$times[TimeNames::Midnight] = ($this->_settings[TimeNames::Midnight] == MidnightMethod::Jafari) ?
				doubleval($times[TimeNames::Sunset]) + Utils::TimeDiff(doubleval($times[TimeNames::Sunset]), doubleval($times[TimeNames::Fajr])) / 2 :
				doubleval($times[TimeNames::Sunset]) + Utils::TimeDiff(doubleval($times[TimeNames::Sunset]), doubleval($times[TimeNames::Sunrise])) / 2;

		$times = $this->TuneTimes($times);
		return $this->ModifyFormats($times);
	}

	private function ModifyFormats(array $times)
	{
		$formattedTimes = array();
		foreach ($times as $key => $item)
		{
			$value = $this->GetFormattedTime(doubleval($item), $this->_timeFormat);
			$formattedTimes[$key] = $value;
		}
		return $formattedTimes;
	}

	private function TuneTimes(array $times)
	{
		$tmp = array();
		foreach ($times as $key => $item)
		{
			$value = doubleval($item) + doubleval($this->_offsets[$key]) / 60;
			$tmp[$key] = $value;
		}
		return $tmp;
	}

	private function AdjustTimes(array $times)
	{
		$tmp = array();
		foreach ($times as $key => $item)
		{
			$value = $value = doubleval($item) + intval($this->_timeZone) - doubleval($this->_longitude) / 15;
			$tmp[$key] = $value;
		}

		if ($this->_settings["HighLats"] != HighLatitudeMethod::None)
			$tmp = $this->AdjustHighLatitudes($tmp);
		 
		if (Utils::ContainsMin($this->_settings[TimeNames::Imsak]))
			$tmp[TimeNames::Imsak] = doubleval($tmp[TimeNames::Fajr]) - Utils::Evalu($this->_settings[TimeNames::Imsak]) / 60;
		if (Utils::ContainsMin($this->_settings[TimeNames::Maghrib]))
			$tmp[TimeNames::Maghrib] = doubleval($tmp[TimeNames::Sunset]) + Utils::Evalu($this->_settings[TimeNames::Maghrib]) / 60;
		if (Utils::ContainsMin($this->_settings[TimeNames::Isha]))
			$tmp[TimeNames::Isha] = doubleval($tmp[TimeNames::Maghrib]) + Utils::Evalu($this->_settings[TimeNames::Isha]) / 60;

		$tmp[TimeNames::Dhuhr] = doubleval($tmp[TimeNames::Dhuhr]) + Utils::Evalu($this->_settings[TimeNames::Dhuhr]) / 60;
		return $tmp;
	}

	private function AdjustHighLatitudes($times)
	{
		$nightTime = Utils::TimeDiff(doubleval($times[TimeNames::Sunset]), doubleval($times[TimeNames::Sunrise])); 

		$times[TimeNames::Imsak] = $this->AdjustHLTime(doubleval($times[TimeNames::Imsak]), doubleval($times[TimeNames::Sunrise]), Utils::Evalu($this->_settings[TimeNames::Imsak]), $nightTime, "ccw");
		$times[TimeNames::Fajr]  = $this->AdjustHLTime(doubleval($times[TimeNames::Fajr]), doubleval($times[TimeNames::Sunrise]), Utils::Evalu($this->_settings[TimeNames::Fajr]), $nightTime, "ccw");
		$times[TimeNames::Isha]  = $this->AdjustHLTime(doubleval($times[TimeNames::Isha]), doubleval($times[TimeNames::Sunset]), Utils::Evalu($this->_settings[TimeNames::Isha]), $nightTime);
		$times[TimeNames::Maghrib] = $this->AdjustHLTime(doubleval($times[TimeNames::Maghrib]), doubleval($times[TimeNames::Sunset]), Utils::Evalu($this->_settings[TimeNames::Maghrib]), $nightTime);
	
		return $times;
	}

	private function AdjustHLTime($time, $based, $angle, $nightTime, $direction = "")
	{
		
		$portion = $this->NightPortion($angle, $nightTime);
		$timeDiff = ($direction == "ccw") ? Utils::TimeDiff($time, $based) : Utils::TimeDiff($based, $time);
		if (is_nan($time) || $timeDiff > $portion) 
			$time = $based + ($direction == "ccw" ? $portion * -1 : $portion);
		return $time;
	}

	private function NightPortion($angle, $nightTime)
	{
		$method = $this->_settings["HighLats"];
		$portion = 1/2.0; // MidNight
		if ($method == HighLatitudeMethod::AngleBased)
			$portion = 1/60.0 * $angle;
		if ($method == HighLatitudeMethod::OneSeventh)
			$portion = 1/7.0;
		return $portion * $nightTime;
	}

	// Convert hours to day portions 
	private function DayPortions(array $times)
	{
		$tmp = array();
		foreach ($times as $key => $item)
		{
			$value = doubleval($item) / 24.0;
			$tmp[$key] = $value;
		}
		return $tmp;
	}

	// Get asr shadow factor
	private function AsrFactor($asrParam)
	{
		return $asrParam == AsrMethod::Standard ? 1 : 0;
	}

	// Return sun angle for sunset/sunrise
	private function RiseSetAngle()
	{
		//var earthRad = 6371009; // in meters
		//var angle = DegreeMath.Acos(earthRad/(earthRad+ elv));
		$angle = 0.0347 * sqrt($this->_elv); // an approximation
		return 0.833 + $angle;
	}
	#endregion

	#region Time Zone methods

	// Get daylight saving for a given date
	private function GetDst($date)
	{
		if (($timestamp = strtotime($date)) !== false)
		{
			$php_date = getdate($timestamp);
			$year = intval($php_date['year']);
			$month = intval($php_date['mon']);
			$day = intval($php_date['mday']);
		}
		return intval(($this->GmtOffset($year, $month, $day) != $this->GetTimeZone($date)));
	}

	// GMT offset for a given date
	private function GmtOffset($year, $month, $day)
	{
		$localDate = new DateTime($year.'-'.$month.'-'.$day);
		$localDate->setTime(12, 0, 0);
		$format = 'Y-m-d H:i:s';
		$GMTDate = DateTime::createFromFormat($format, gmdate('Y-m-d H:i:s', strtotime($date)));
		$timeSpan = $localDate->diff($GMTDate);
		return $timeSpan->h;
	}

	private function GetTimeZone($date)
	{
		if (($timestamp = strtotime($date)) !== false)
		{
			$php_date = getdate($timestamp);
			$year = intval($php_date['year']);
			$month = intval($php_date['mon']);
			$day = intval($php_date['mday']);
		}
		
		$t1 = $this->GmtOffset($year, 1, 1);
		$t2 = $this->GmtOffset($year, 7, 1);
		return min(intval($t1), intval($t2));
	}
}
	
?>