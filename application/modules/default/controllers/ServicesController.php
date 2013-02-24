<?php
require_once APPLICATION_PATH.'/../library/services/prayer/prayerTime.php';

class ServicesController extends Zend_Controller_Action
{

    public function init()
    {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('prayertimes', 'xml')
                      ->initContext('xml');
    }

    public function prayertimesAction()
    {
        mb_internal_encoding('UTF-8');
               
        if(isset($_GET['city']))
                $city = $_GET['city'];
        else
                $city = '';

        if(isset($_GET['date']))
                $date = $_GET['date'];
        else
                $date = date('Y-m-d', time());

        if(isset($_GET['timezonename']))
        {
            $timezoneName = str_replace('-', '/', $_GET['timezonename']);
            $dateT = new DateTime(null, new DateTimeZone($timezoneName));
            $year = $dateT->format('Y');
            $month = $dateT->format('m');
            $day = $dateT->format('d');
            $offset = $dateT->format('P');
            $offsets = explode(':', $offset);
            $hourOffset = intval($offsets[0]);
            $minutesOffset = intval($offsets[1]);
//            $dateTimeZone = new DateTimeZone($_GET['timezonename']);
//            $dateTime = new DateTime('now');
            $expires = date('D, d M Y H:i:s \G\M\T', mktime(23 - $hourOffset, 59 - $minutesOffset, 59, intval($month), intval($day), intval($year)));
        }
        else
            $expires = gmdate('D, d M Y H:i:s \G\M\T', strtotime('Europe/Paris'));
        
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

        $this->getResponse()->setHeader('X-WNS-Expires', $expires);
        
        $xml = '<?xml version="1.0" encoding="utf-8"?><tile><visual><binding template="TileWideText02"><text id="1">'.$city.'</text><text id="2">Fajr '.$prayerTimes['Fajr'].'</text><text id="3">Asr '.$prayerTimes['Asr'].'</text><text id="4">Shuruq '.$prayerTimes['Sunrise'].'</text><text id="5">Maghrib '.$prayerTimes['Maghrib'].'</text><text id="6">Dhur '.$prayerTimes['Dhuhr'].'</text><text id="7">Isha '.$prayerTimes['Isha'].'</text></binding></visual></tile>';
        echo $xml;
    }
}

