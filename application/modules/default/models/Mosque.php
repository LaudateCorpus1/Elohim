<?php

class Default_Model_Mosque extends Zend_Db_Table_Abstract 
{
    protected $_name = 'mosque';
    
    public function get($id)
    {
        $id = (int)$id;
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
        {
            throw new Exception("Mosquée introuvable: $id");
        }
        return $row;
    }
    
    public function getByFormattedAddress($formattedAddress)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name)
              ->join('address', $this->_name.'.addressId = address.id', array('formatted', 'latitude', 'longitude'))
              ->where($this->getAdapter()->quoteInto('formatted LIKE ?', '%'.$formattedAddress.'%'))
              ->order($this->_name.'.creationDate ASC');
        
        return $query;
    }
    
    public function getByLocation($country, $route = null, $streetNo = null, $locality = null, $sublocality = null, $administrativeArea = null, $administrativeArea2 = null, $administrativeArea3 = null)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name)
              ->join('address', $this->_name.'.addressId = address.id', array('formatted', 'latitude', 'longitude'))
              ->order($this->_name.'.creationDate ASC');
        
        foreach(Default_Model_Address::$exceptionRules as $exception)
        {
            $key = key($exception);
            $paramValue = $$key;
            if($paramValue == $exception[$key])
            {
                $exceptionArray = $exception[0];
                $keys = array_keys($exceptionArray);
                foreach($keys as $exceptionCol)
                {
                    $$exceptionCol = $exceptionArray[$exceptionCol];
                }
            }
        }
        
        if($route != null && !empty($route))
            $query->where($this->getAdapter()->quoteInto('route = ?', $route));
        
        if($streetNo != null && !empty($streetNo))
            $query->where($this->getAdapter()->quoteInto('streetNo = ?', $streetNo));
        
        if($locality != null && !empty($locality))
            $query->where($this->getAdapter()->quoteInto('locality = ?', $locality));
                
        if($sublocality != null && !empty($sublocality))
            $query->where($this->getAdapter()->quoteInto('sublocality = ?', $sublocality));
        
        if($administrativeArea != null && !empty($administrativeArea))
            $query->where($this->getAdapter()->quoteInto('administrativeArea = ?', $administrativeArea));
        
        if($administrativeArea2 != null && !empty($administrativeArea2))
            $query->where($this->getAdapter()->quoteInto('administrativeArea2 = ?', $administrativeArea2));
        
        if($administrativeArea3 != null && !empty($administrativeArea3))
            $query->where($this->getAdapter()->quoteInto('administrativeArea3 = ?', $administrativeArea3));
        
        if($country != null && !empty($country))
            $query->where($this->getAdapter()->quoteInto('country = ?', $country));
        
        return $query;
    }
    
    public function addMosque($data, $addressId)
    {
        $website = isset($data['mosqueWebsite']) && $data['mosqueWebsite'] != '' ? $data['mosqueWebsite'] : null;
        $nbMenRooms = isset($data['nbMenRooms']) && $data['nbMenRooms'] != '' ? $data['nbMenRooms'] : null;
        $nbWomenRooms = isset($data['nbWomenRooms']) && $data['nbWomenRooms'] != '' ? $data['nbWomenRooms'] : null;
        $menAblutions = isset($data['menAblutions']) && $data['menAblutions'] != '' ? $data['menAblutions'] : null;
        $womenAblutions = isset($data['womenAblutions']) && $data['womenAblutions'] != '' ? $data['womenAblutions'] : null;
        $jumua = isset($data['jumua']) && $data['jumua'] != '' ? $data['jumua'] : null;
        $jumuaLanguage = isset($data['jumuaLanguage']) && $data['jumuaLanguage'] != '' ? $data['jumuaLanguage'] : null;
        $islamLesson = isset($data['islamLesson']) && $data['islamLesson'] != '' ? $data['islamLesson'] : null;
        $arabLesson = isset($data['arabLesson']) && $data['arabLesson'] != '' ? $data['arabLesson'] : null;
        $janaza = isset($data['janaza']) && $data['janaza'] != '' ? $data['janaza'] : null;
        $tarawih = isset($data['tarawih']) && $data['tarawih'] != '' ? $data['tarawih'] : null;
                
        if(strlen($website) > 0 && mb_substr($website, 0, 4, 'utf-8') !== "http") {
            $website = 'http://'.$website;
        }
        
        $dataToInsert = array(
            'name' => $data['mosque_name'],
            'type' => $data['mosque_type'],
            'website' => $website,
            'nbMenRooms' => $nbMenRooms,
            'nbWomenRooms' => $nbWomenRooms,
            'menAblutions' => $menAblutions,
            'womenAblutions' => $womenAblutions,
            'jumua' => $jumua,
            'jumuaLanguage' => $jumuaLanguage,
            'islamLesson' => $islamLesson,
            'arabLesson' => $arabLesson,
            'janaza' => $janaza,
            'tarawih' => $tarawih,
            'addressId' => $addressId,
            'creationDate' => gmdate('Y-m-d H:i:s', time())
        );
        return $this->insert($dataToInsert);
    }
}
?>
