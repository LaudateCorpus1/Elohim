<?php

class DegreeMath
{
	// degree sin
	public static function Sin($d)
	{
		return sin(deg2rad(doubleval($d)));
	}

	// degree cos
	public static function Cos($d)
	{
            //echo "Cos : ".cos(deg2rad(doubleval($d)))."<br>";
		return cos(deg2rad(doubleval($d)));
	}

	// degree tan
	public static function Tan($d)
	{
            //echo "Tan : ".tan(deg2rad(doubleval($d)))."<br>";
		return tan(deg2rad(doubleval($d)));
	}

	// degree arcsin
	public static function Asin($x)
	{
		return rad2deg(asin(doubleval($x)));
	}

	// degree arccos
	public static function Acos($x)
	{
		return rad2deg(acos(doubleval($x)));
	}

	// degree arctan
	public static function Atan($x)
	{
		return rad2deg(atan(doubleval($x)));
	}

	// degree arctan2
	public static function Atan2($y, $x)
	{
		return rad2deg(atan2(doubleval($y), doubleval($x)));
	}

	// degree arccot
	public static function Acot($x)
	{
            //echo "Acot : ".rad2deg(atan(1 / doubleval($x)))."<br>";
		return rad2deg(atan(1 / doubleval($x)));
	}
}
?>