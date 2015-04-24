<?php
require_once APPLICATION_PATH.'/../library/services/prayer/prayerTime.php';

class ServicesController extends Zend_Controller_Action
{

    public function init()
    {
        $format = 'xml';
        if(isset($_GET['type']) && $_GET['type'] == 'json')
        {
            $format = $_GET['type'];
        }
        
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('prayertimes', $format)
                      ->initContext($format);
    }

    public function prayertimesAction()
    {
        mb_internal_encoding('UTF-8');
        if(isset($_GET['city']))
                $city = $_GET['city'];
        else
                $city = '';

        $timezoneName = '';
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
            
            $date = $dateT->format('Y-m-d');
        }
        else
        {
            $timezoneName = 'Europe/Paris';
            $expires = gmdate('D, d M Y H:i:s \G\M\T', strtotime('Europe/Paris'));
            $date = date('Y-m-d', time());
        }
            
        if(isset($_GET['latitude']))
                $latitude = $_GET['latitude'];
        else
                $latitude = 48.85693;

        if(isset($_GET['longitude']))
                $longitude = $_GET['longitude'];
        else
                $longitude = 2.3412;

        if(isset($_GET['timezone']))
                $timezone = $_GET['timezone'];
        else
                $timezone = 1;

        if(isset($_GET['dst']))
                $dst = intval($_GET['dst']);
        else
                $dst = 0;

        if(isset($_GET['method']))
                $method = new $_GET['method'];
        else
                $method = new MethodUOIF();
        
        if(isset($_GET['asr']))
                $asrMethod = $_GET['asr'] == 'Standard' ? AsrMethod::Standard : AsrMethod::Hanafi;
        else
                $asrMethod = AsrMethod::Standard;
        
        if(isset($_GET['midnight']))
                $midnightMethod = $_GET['midnight'] == 'Standard' ? MidnightMethod::Standard : MidnightMethod::Jafari;
        else
                $midnightMethod = MidnightMethod::Standard;
        
        if(isset($_GET['lang']))
                $lang = $_GET['lang'];
        else
                $lang = 'en';
        
        if(isset($_GET['date']))
        {
            $dateToCompare = $_GET['date'];
            $f = new DateTime($dateToCompare, new DateTimeZone($timezoneName));
            $offset = intval($f->format('P'));
            $f2 = new DateTime('now', new DateTimeZone($timezoneName));
            $offset2 = intval($f2->format('P'));
            $diff = abs($offset - $offset2);
            if($diff > 0)
            {
                if($dst == 0)
                    $dst = 1;
                else if($dst == 1)
                    $dst = 0;
            }
        }
        
        $format = TimeFormat::Format24h;
        if($lang == 'en')
            $format = TimeFormat::Format12h;
        
        $p = new PrayerTime($method, $asrMethod, $midnightMethod);
        $prayerTimes = $p->GetTimes($date, $latitude, $longitude, $timezone, $dst, $format);

        if($lang == 'en')
        {
            $prayerTimes['Fajr'] .= ' AM';
            $prayerTimes['Asr'] .= ' PM';
            $prayerTimes['Sunrise'] .= ' AM';
            $prayerTimes['Maghrib'] .= ' PM';
            $prayerTimes['Dhuhr'] .= ' PM';
            $prayerTimes['Isha'] .= ' PM';
        }
        
        $this->getResponse()->setHeader('X-WNS-Expires', $expires);
        
        $format = 'xml';
        if(isset($_GET['type']))
        {
            $format = $_GET['type'];
        }
        
        $response = '';
        if($format == 'json')
        {
            $jArray = array('prayer_times' =>
                            array('Fajr' => $prayerTimes['Fajr'],
                            'Shuruq' => $prayerTimes['Sunrise'],
                            'Asr' => $prayerTimes['Asr'],
                            'Maghrib' => $prayerTimes['Maghrib'],
                            'Dhur' => $prayerTimes['Dhuhr'],
                            'Isha'=> $prayerTimes['Isha']
                            )
                      );
            $response = json_encode($jArray);
        }
        else
        {
            $response = '<?xml version="1.0" encoding="utf-8"?><tile><visual><binding template="TileWideText02"><text id="1">'.$city.'</text><text id="2">Fajr '.$prayerTimes['Fajr'].'</text><text id="3">Asr '.$prayerTimes['Asr'].'</text><text id="4">Shuruq '.$prayerTimes['Sunrise'].'</text><text id="5">Maghrib '.$prayerTimes['Maghrib'].'</text><text id="6">Dhur '.$prayerTimes['Dhuhr'].'</text><text id="7">Isha '.$prayerTimes['Isha'].'</text></binding></visual></tile>';
        }
        
        echo $response;
    }
    
    public function privacypolicyAction()
    {
        $this->_helper->layout->setLayout('index_layout');
    }
}

