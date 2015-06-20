<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of String
 *
 * @author jeremie
 */
class Islamine_Geocode 
{
    public static $languagesByCountry = array(
        'France' => array('fr', 'eu'),
        'Belgium' => array('nl', 'fr'),
        'Germany' => array('de'),
        'Saudi Arabia' => array('ar'),
        'Bulgaria' => array('bg'),
        'Bangladesh' => array('bn'),
        'India' => array('bn', 'gu', 'hi', 'kn', 'ml', 'mr', 'ta', 'te'),
        'Spain' => array('es', 'ca', 'eu', 'gl'),
        'Czech Republic' => array('cz'),
        'Denmark' => array('da'),
        'Greece' => array('el'),
        'Iran' => array('ar', 'fa'),
        'Afghanistan' => array('ar', 'fa'),
        'Finland' => array('fi'),
        'Philippines' => array('fil', 'tl'),
        'Croatia' => array('hr'),
        'Hungary' => array('hu'),
        'Indonesia' => array('id'),
        'Italy' => array('it'),
        'Israel' => array('iw'),
        'Luxembourg' => array('de', 'fr'),
        'Austria' => array('de'),
        'Switzerland' => array('de', 'fr', 'it'),
        'Japan' => array('ja'),
        'Korea' => array('ko'),
        'Lithuania' => array('lt'),
        'Latvia' => array('lv'),
        'Netherlands' => array('nl'),
        'Norway' => array('no'),
        'Poland' => array('pl'),
        'Portugal' => array('pt'),
        'Brazil' => array('pt-BR'),
        'Romania' => array('ro'),
        'Russia' => array('ru'),
        'Slovakia' => array('sk'),
        'Slovenia' => array('sl'),
        'Serbia' => array('sr'),
        'Sweden' => array('sv'),
        'Sri Lanka' => array('ta'),
        'Singapore' => array('ta'),
        'Malaysia' => array('ta'),
        'Thailand' => array('th'),
        'Ukraine' => array('uk'),
        'Vietnam' => array('vi'),
        'China' => array('zh-CN'),
        'Taiwan' => array('zh-TW'),
        'Turkey' => array('tr')
    );
    
    public static function geocode($address, $language = 'en')
    {
        $addressclean = str_replace(" ", "+", $address);
        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$addressclean."&sensor=false&language=".$language;

//        $client = new Zend_Http_Client();
//        $client->setUri($url);
//        $client->setAdapter('Zend_Http_Client_Adapter_Curl');
//        $adapter = $client->getAdapter();
//        $response = $client->request();
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);

        if($response['status'] === 'OK' && count($response['results']) > 0)
        {
            $values = $response['results'][0];
            $returnValues = array(
                'streetNo' => '',
                'route' => '',
                'locality' => '',
                'sublocality' => '',
                'administrativeArea' => '',
                'administrativeArea2' => '',
                'administrativeArea3' => '',
                'country' => '',
                'postalCode' => '',
                'formatted' => '',
                'latitude' => '',
                'longitude' => ''
            );
            
            foreach ($values['address_components'] as $addressComponent) 
            {
                if($addressComponent['types'][0] == 'street_number')
                    $returnValues['streetNo'] = $addressComponent['long_name'];
                else if($addressComponent['types'][0] == 'route')
                    $returnValues['route'] = $addressComponent['long_name'];
                else if($addressComponent['types'][0] == 'locality')
                    $returnValues['locality'] = $addressComponent['long_name'];
                else if($addressComponent['types'][0] == 'sublocality')
                    $returnValues['sublocality'] = $addressComponent['long_name'];
                else if($addressComponent['types'][0] == 'administrative_area_level_3')
                    $returnValues['administrativeArea3'] = $addressComponent['long_name'];
                else if($addressComponent['types'][0] == 'administrative_area_level_2')
                    $returnValues['administrativeArea2'] = $addressComponent['long_name'];
                else if($addressComponent['types'][0] == 'administrative_area_level_1')
                    $returnValues['administrativeArea'] = $addressComponent['long_name'];
                else if($addressComponent['types'][0] == 'country')
                    $returnValues['country'] = $addressComponent['long_name'];
                else if($addressComponent['types'][0] == 'postal_code')
                    $returnValues['postalCode'] = $addressComponent['long_name'];
            }
            
            $returnValues['formatted'] = $values['formatted_address'];
            $returnValues['latitude'] = $values['geometry']['location']['lat'];
            $returnValues['longitude'] = $values['geometry']['location']['lng'];
            $returnValues['count'] = count($response['results']);
            return $returnValues;
        }
        else
            return false;
    }
}

?>
