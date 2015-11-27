<?php

/**
 * Description of TopicTag
 *
 * @author jeremie
 */
class Api_Model_DeviceReminder extends Zend_Db_Table_Abstract
{
    protected $_name = 'device_reminder';

    protected $_referenceMap = array(
        'api_device' => array(
                'columns'       => array('device_id'),
                'refTableClass' => 'Api_Model_Device',
                'refColumns'    => array('device_id')
        ),
        'reminder'   => array(
                'columns'       => array('reminder_id'),
                'refTableClass' => 'Api_Model_Story',
                'refColumns'    => array('reminder_id')
        ));

   public function addRow($deviceId, $reminderId)
   {
       $data = array(
           'reminder_id' => $reminderId,
           'device_id'   => $deviceId,
           'is_new' => true
       );
       $this->insert($data);
   }
    public function setNotNew($deviceId, $reminderId)
    {
        $deviceModel = new Api_Model_Device();
        $id = $deviceModel->getIdFromGCMId($deviceId);
        if($id != null) {
            $where = array();
            $where[] = $this->getAdapter()->quoteInto('reminder_id = ?', $reminderId);
            $where[] = $this->getAdapter()->quoteInto('device_id = ?', $id);
            return $this->update(array('is_new' => false),  $where);
        }
        return 0;
    }
    
    public function getUnreadReminders($platform, $deviceId)
    {
        $query = $this->select();
        $query->from($this->_name, array(
                    'id' => 'reminder_id'
                ))
                ->setIntegrityCheck(false)
                ->join('api_device', $this->_name.'.device_id = api_device.id', null)
                ->where('is_new = 1')
                ->where($this->getAdapter()->quoteInto('platform = ?', $platform))
                ->where($this->getAdapter()->quoteInto('gcm_registration_id = ?', $deviceId));
        
        return $this->fetchAll($query);
    }
}
?>
