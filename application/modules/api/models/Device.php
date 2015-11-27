<?php

/**
 * Description of Tag
 *
 * @author jeremie
 */
class Api_Model_Device extends Zend_Db_Table_Abstract {

    protected $_name = 'api_device';

    //------------------------------
    // Methods for Google Cloud Messaging (GCM)
    // http://developer.android.com/google/gcm/
    //------------------------------
    public function getGCMRegistrationIds() {
        $query = $this->select();
        $query->from($this->_name, array(
                    'gcm_registration_id',
                    'os_version'
                ))
                ->where('platform = "Android"')
                ->where('gcm_registration_id IS NOT NULL');

        $ids = $this->fetchAll($query);
        $arrayIds = array();
        foreach($ids as $id) {
            $arrayIds[] = $id->gcm_registration_id;
        }
        
        return $arrayIds;
    }
    
    public function get() {
        $query = $this->select();
        $query->from($this->_name, array(
                    'id',
                    'gcm_registration_id',
                    'os_version'
                ));

        return $this->fetchAll($query);
    }
    
    public function getIdFromGCMId($gcmId) {
        $query = $this->select();
        $query->from($this->_name)
              ->where($this->getAdapter()->quoteInto($this->_name.'.gcm_registration_id = ?', $gcmId));
        
        $row = $this->fetchRow($query);
        if($row == null)
        {
            return null;
        }
        return $row->id;
    }

    public function addGCMRegistrationId($registrationId, $platform, $osVersion) {
        if (!empty($registrationId) && !empty($platform)) {
            if ($this->doesGCMRegistrationIdExist($registrationId) === false) {
                $device = array(
                    'gcm_registration_id' => $registrationId,
                    'platform' => $platform,
                    'os_version' => $osVersion
                );
                return $this->insert($device);
            }
        }
        return null;
    }

    public function doesGCMRegistrationIdExist($registrationId) {
        $query = $this->select();
        $query->from($this->_name)
                ->where('gcm_registration_id = ?', $registrationId);
        $res = $this->fetchRow($query);
        if ($res == null) {
            return false;
        } else {
            return $res->id;
        }
    }

}
