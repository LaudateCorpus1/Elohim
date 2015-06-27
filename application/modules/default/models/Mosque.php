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
    
    public function getByLocalizedLocation($streetNo, $route, $locality, $formattedAddress)
    {
        $wheres = array();
        
        // Most of the time there is only one city of the same name
        // In this case only use the city name
        $addressData = Islamine_Geocode::geocode($locality, 'en');
        if(empty($streetNo) && empty($route) && $addressData['count'] == 1)
        {
            $wheres[] = $this->buildWhere(
                    null,
                    null,
                    null,
                    $addressData['locality'],
                    null,
                    $addressData['administrativeArea'],
                    null,
                    null,
                    true
            );

            if(isset(Islamine_Geocode::$languagesByCountry[$addressData['country']]))
            {
                $languages = Islamine_Geocode::$languagesByCountry[$addressData['country']];
                foreach($languages as $language)
                {
                    $addressData = Islamine_Geocode::geocode($locality, $language);
                    $wheres[] = $this->buildWhere(
                            null,
                            null,
                            null,
                            $addressData['locality'],
                            null,
                            $addressData['administrativeArea'],
                            null,
                            null,
                            true
                    );
                }
            }
        }
        else
        {
            $addressData = Islamine_Geocode::geocode($formattedAddress, 'en');
        
            if($addressData === false)
                return null;

            $wheres[] = $this->buildWhere(
                        $addressData['country'],
                        $addressData['route'],
                        $addressData['streetNo'],
                        $addressData['locality'],
                        $addressData['sublocality'],
                        $addressData['administrativeArea'],
                        $addressData['administrativeArea2'],
                        $addressData['administrativeArea3']
                );
            
            if(isset(Islamine_Geocode::$languagesByCountry[$addressData['country']]))
            {
                $languages = Islamine_Geocode::$languagesByCountry[$addressData['country']];
                foreach($languages as $language)
                {
                    $addressData = Islamine_Geocode::geocode($formattedAddress, $language);
                    $wheres[] = $this->buildWhere(
                            $addressData['country'],
                            $addressData['route'],
                            $addressData['streetNo'],
                            $addressData['locality'],
                            $addressData['sublocality'],
                            $addressData['administrativeArea'],
                            $addressData['administrativeArea2'],
                            $addressData['administrativeArea3']
                    );
                }
            }
        }
        
//        Zend_Debug::dump($wheres); exit;
        $orWhere = implode(' OR ', $wheres);
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name)
              ->join('address', $this->_name.'.addressId = address.id', array('formatted', 'latitude', 'longitude'))
              ->where($orWhere)
              ->order($this->_name.'.creationDate ASC');
        
        
        return $query;
    }
    
    public function getByLocalizedRoute($formattedAddress)
    {
        $wheres = array();
        $addressData = Islamine_Geocode::geocode($formattedAddress, 'en');
        if($addressData === false)
            return null;

        $wheres[] = $this->buildWhere(
                    $addressData['country'],
                    $addressData['route'],
                    null,
                    $addressData['locality']
            );

        if(isset(Islamine_Geocode::$languagesByCountry[$addressData['country']]))
        {
            $languages = Islamine_Geocode::$languagesByCountry[$addressData['country']];
            foreach($languages as $language)
            {
                $addressData = Islamine_Geocode::geocode($formattedAddress, $language);
                $wheres[] = $this->buildWhere(
                        $addressData['country'],
                        $addressData['route'],
                        null,
                        $addressData['locality']
                );
            }
        }
        
        $orWhere = implode(' OR ', $wheres);
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, 'name')
              ->join('address', $this->_name.'.addressId = address.id', 'formatted')
              ->where($orWhere)
              ->limit(5)
              ->order($this->_name.'.creationDate ASC');
        
        return $this->fetchAll($query);
    }
    
    private function buildWhere($country, $route = null, $streetNo = null, $locality = null, $sublocality = null, $administrativeArea = null, $administrativeArea2 = null, $administrativeArea3 = null, $useLocalityAndCheck = false)
    {
        // useLocalityAndCheck used when the locality is empty but not administrativeArea
        // ex. Sultanahmet camii in Istanbul
        // Then we need to search : locality = 'Istanbul' OR (locality = '' AND administrativeArea = 'Istanbul')
        
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
        
        $where = '(';
        
        if($useLocalityAndCheck && $locality != null && !empty($locality))
        {
            $where .= $this->getAdapter()->quoteInto('locality = ?', $locality);
            
            if($administrativeArea != null && !empty($administrativeArea))
            {
                $where .= " OR ((locality IS NULL OR locality = '') AND ";
                $where .= $this->getAdapter()->quoteInto('administrativeArea = ?', $administrativeArea).')';
            }
        }
        else
        {
            if($route != null && !empty($route))
                $where .= $this->getAdapter()->quoteInto('route = ?', $route).' AND ';

            if($streetNo != null && !empty($streetNo))
                $where .= $this->getAdapter()->quoteInto('streetNo = ?', $streetNo).' AND ';

            if($locality != null && !empty($locality))
                $where .= $this->getAdapter()->quoteInto('locality = ?', $locality).' AND ';

            if($sublocality != null && !empty($sublocality))
                $where .= $this->getAdapter()->quoteInto('sublocality = ?', $sublocality).' AND ';

            if($administrativeArea != null && !empty($administrativeArea))
                $where .= $this->getAdapter()->quoteInto('administrativeArea = ?', $administrativeArea).' AND ';

            if($administrativeArea2 != null && !empty($administrativeArea2))
                $where .= $this->getAdapter()->quoteInto('administrativeArea2 = ?', $administrativeArea2).' AND ';

            if($administrativeArea3 != null && !empty($administrativeArea3))
                $where .= $this->getAdapter()->quoteInto('administrativeArea3 = ?', $administrativeArea3).' AND ';

            if($country != null && !empty($country))
                $where .= $this->getAdapter()->quoteInto('country = ?', $country);
        }
        
        if(substr($where, -5) == ' AND ')
                $where = substr($where, 0, -5);
                
        return $where .= ')';
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
