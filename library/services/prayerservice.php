<?php
//session_start();
ini_set('display_errors', 'On');
error_reporting(E_ALL);

mb_internal_encoding('UTF-8');

require_once 'prayer/prayerTime.php';

if(isset($_GET['city']))
	$city = $_GET['city'];
else
	$city = '';

if(isset($_GET['date']))
	$date = $_GET['date'];
else
	$date = date('Y-m-d', time());
	
if(isset($_GET['expires']))
	$expires = date('D, d M Y H:i:s \G\M\T', strtotime($_GET['expires']));
else
	$expires = date('D, d M Y H:i:s \G\M\T', mktime(23, 59, 59));

if(isset($_GET['latitude']))
	$latitude = $_GET['latitude'];
else
	$latitude = 48.8667;

if(isset($_GET['longitude']))
	$longitude = $_GET['longitude'];
else
	$longitude = 2.3333;

if(isset($_GET['timezone']))
	$timezone = $_GET['timezone'];
else
	$timezone = 1;

if(isset($_GET['dst']))
	$dst = $_GET['dst'];
else
	$dst = 1;
	
if(isset($_GET['method']))
	$method = new $_GET['method'];
else
	$method = new MethodUOIF();

$p = new PrayerTime($method);
$prayerTimes = $p->GetTimes($date, $latitude, $longitude, $timezone, $dst);

$xml = '<?xml version="1.0" encoding="utf-8"?><tile><visual><binding template="TileWideText02"><text id="1">'.$city.'</text><text id="2">Fajr '.$prayerTimes['Fajr'].'</text><text id="3">Asr '.$prayerTimes['Asr'].'</text><text id="4">Sunrise '.$prayerTimes['Sunrise'].'</text><text id="5">Maghrib '.$prayerTimes['Maghrib'].'</text><text id="6">Dhur '.$prayerTimes['Dhuhr'].'</text><text id="7">Isha '.$prayerTimes['Isha'].'</text></binding></visual></tile>';

header("Content-Type: text/xml");
//header("X-WNS-Expires: ".$expires);

echo $xml;
exit;