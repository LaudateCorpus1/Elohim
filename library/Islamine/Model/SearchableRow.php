<?php
abstract class Islamine_Model_SearchableRow extends Zend_Db_Table_Row_Abstract implements Islamine_Interface_ISubject
{
    protected static $_observers = array();
    //all classes that inherit this must provide implementation for
    //the following:
    //Return the name of the model.
    abstract public function modelType();
    //Return the index fields. This is best known to the individual models.
    abstract public function getSearchFields();

    public static function register($o)
    {
        self::$_observers[] = $o;
    }
    
    public function notify($flag, $row)
    {
        foreach (self::$_observers as $observer)
        {
            $observer->update($flag, $row);
        }
    }
}

?>
