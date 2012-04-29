<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zend_View_Helper_FavoriteTag
 *
 * @author jeremie
 */
class Zend_View_Helper_Scheduler extends Zend_View_Helper_Abstract
{
    public function scheduler()
    {
        include (APPLICATION_PATH.'/../library/scheduler/codebase/connector/scheduler_connector.php');
        include (APPLICATION_PATH.'/../library/scheduler/commons/config.php');
        $res=mysql_connect($server, $user, $pass);
        mysql_select_db($db_name);

        $scheduler = new schedulerConnector($res);
        $scheduler->render_table("events","event_id","start_date,end_date,event_name,details,user_id");
        return APPLICATION_PATH.'/../library/scheduler/events.php';
    }
}
?>
