<?php

class Default_Model_Event extends Zend_Db_Table_Abstract
{
    protected $_name = 'events';
    
    public function __construct() 
    {
        $dbAdapter = Zend_Db::factory(Zend_Registry::get('config')->db2);
        //var_dump($dbAdapter); exit;
        parent::__construct(array('db' => $dbAdapter));
    }
    
    public function getEventsAmountInCurrentMonth()
    {
        $month = date('Y-m');
        $query = $this->select()
                      ->from($this->_name, 'count(*) as amount')
                      ->where($this->getAdapter()->quoteInto('start_date LIKE ?', $month.'%'));
        $res = $this->fetchRow($query);
        return $res->amount;
    }
}

