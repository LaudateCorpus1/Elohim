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
            'streetNo' => $data['streetNo'],
            'formatted' => $data['formatted'],
            'postalCode' => $data['postalCode'],
            'locality' => $data['locality'],
            'sublocality' => $data['sublocality'],
            'administrativeArea' => $data['administrativeArea'],
            'administrativeArea2' => $data['administrativeArea2'],
            'administrativeArea3' => $data['administrativeArea3'],
            'country' => $data['country'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude']
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
