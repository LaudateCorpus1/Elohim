<?php
class Utils
{
	private static function Fix($a, $b)
	{
		$a = doubleval($a);
		$a = $a - $b * (floor($a / $b));
		return ($a < 0) ? $a + $b : $a;
	}

	public static function FixHour($hour)
	{
		return self::Fix($hour, 24);
	}

	public static function FixAngle($angle)
	{
		return self::Fix($angle, 360);
	}

	public static function GregorianDateToJulianDay($date)
	{
		if (($timestamp = strtotime($date)) !== false)
		{
			$php_date = getdate($timestamp);
			$year = intval($php_date['year']);
			$month = intval($php_date['mon']);
			$day = intval($php_date['mday']);
		}
		else
		{
			throw new Exception("Invalid date");
		}
		if ($month <= 2)
		{
			$year -= 1;
			$month += 12;
		};
		$A = floor($year / 100.0);
		$B = 2 - $A + floor($A / 4);

		$JD = floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $B - 1524.5;
		//echo "GregorianDateToJulianDay : ".$JD."<br>";
                return $JD;
	}

	public static function Evalu($str)
	{
		$number = preg_replace("/[^0-9]/", '', $str);
		return doubleval($number);
	}

	public static function ContainsMin($str)
	{
		return strpos($str, 'min') !== false;
	}

	public static function TimeDiff($time1, $time2)
	{
		return self::FixHour($time2 - $time1);
	}

	// Add a leading 0 if necessary
	public static function TwoDigitsFormat($num)
	{
		return (intval($num) < 10) ? '0'.$num : $num;
	}
}
?>