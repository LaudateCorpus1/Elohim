<?php

class Default_Model_Address extends Zend_Db_Table_Abstract 
{
    protected $_name = 'address';
    
    public static $exceptionRules = array(
        array('locality' => 'Paris',
            array('administrativeArea2' => '', 'administrativeArea3' => '')
        )
    );
       
    public function addAddress($data)
    {
        $dataToInsert = array(
            'route' => $data['route'],
            'streetNo' => $data['street_number'],
            'formatted' => $data['formatted_address'],
            'postalCode' => $data['postal_code'],
            'locality' => $data['locality'],
            'sublocality' => $data['sublocality'],
            'administrativeArea' => $data['administrative_area_level_1'],
            'administrativeArea2' => $data['administrative_area_level_2'],
            'administrativeArea3' => $data['administrative_area_level_3'],
            'country' => $data['country'],
            'latitude' => $data['lat'],
            'longitude' => $data['lng']
        );
        
        foreach(self::$exceptionRules as $exception)
        {
            $key = key($exception);
            if($dataToInsert[$key] == $exception[$key])
            {
                $exceptionArray = $exception[0];
                $keys = array_keys($exceptionArray);
                foreach($keys as $exceptionCol)
                {
                    $dataToInsert[$exceptionCol] = $exceptionArray[$exceptionCol];
                }
            }
        }
        
        return $this->insert($dataToInsert);
    }
}
?>
