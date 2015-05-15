<?php

class Default_Model_Address extends Zend_Db_Table_Abstract 
{
    protected $_name = 'address';
       
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
            'country' => $data['country'],
            'latitude' => $data['lat'],
            'longitude' => $data['lng']
        );
        return $this->insert($dataToInsert);
    }
}
?>
