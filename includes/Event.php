<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ROOT_PATH.DS.'includes'.DS.'Core.php';

final Class Event {
    const E_EMERG = 0;
    const E_ALERT = 1;
    const E_CRIT = 2;
    const E_ERROR = 3;
    const E_WARNING = 4;
    const E_NOTICE = 5;
    const E_INFO = 6;
    const E_DEBUG = 7;
        
    const E_SUCCESS = 1000;   
    
    private static $event_types;


    public static function getEventType($code) {
        if(static::$event_types == null) {
            static::$event_types = array(
                1 => 'Emergency',
                2 => 'Alert',
                3 => 'Critical',
                4 => 'Error',
                5 => 'Warning',
                6 => 'Notice',
                7 => 'Info',
                8 => 'Debug',
                1000 => 'Update Success',
            );
        }
        
        return static::$event_types[$code];
    }
    
    public static function getBotName($botConfig,$code) {
        if(!empty($botConfig[$code]['name']))
        {
            return $botConfig[$code]['name'];
        }
        else
        {
            return 'N/A';
        }
    }
        
    public static function read($bot_id = null,$event_type = array(),$page = 0, $limit = 50) {        
        $dbHandler = Core::getDBHandle();

        $selectSQL = "SELECT * FROM ".EVENTS_TBL;
        $exeOptions = array();
        if($bot_id && sizeof($event_type)) 
        {
            $selectSQL .= " WHERE bot_id = :bot_id AND event_type IN (:event_type) ";
            $exeOptions = array(':bot_id' => $bot_id, ':event_type' => $event_type); 
        }
        else if($bot_id)
        {
            $selectSQL .= " WHERE bot_id = :bot_id ";
            $exeOptions = array(':bot_id' => $bot_id); 
        }
        else if($event_type)
        {
            $selectSQL .= " WHERE event_type IN (:event_type) ";
            $exeOptions = array(':event_type' => $event_type); 
        }                   
        
        $selectSQL .= '  ORDER BY TIME DESC LIMIT '.$page.','.$limit;

        $downstream = $dbHandler->prepare($selectSQL);  

        $downstream->execute();    
                
        $arr = array();
        while ($row = $downstream->fetch(PDO::FETCH_ASSOC)) {
            $arr[] = $row;
        }               
        
        return $arr;
    }
    
    public static function getLatestSuccessTime($bot_id) {
        $dbHandler = Core::getDBHandle();
        
        $rows = $dbHandler->query('SELECT time FROM '.EVENTS_TBL.' WHERE bot_id = '.$bot_id.' ORDER BY TIME DESC LIMIT 0,1');
        
        $time = null;
        
        if(!empty($rows))
        {
            foreach($rows as $row)
            {
                $time = $row['time'];
            }
        }
        
        return $time;
    }
    
    public static function write($botId,$eventId,$eventMsg) {
        $dbHandler = Core::getDBHandle();

        $insertSQL = "REPLACE INTO ".EVENTS_TBL." (id, bot_id, event_type, message , time) 
                    VALUES (:id, :bot_id, :event_type, :message, :time)";

        $upstream = $dbHandler->prepare($insertSQL);  

        $upstream->execute(
            array(
                ':id' => null, 
                ':bot_id' => $botId, 
                ':event_type' => $eventId,
                ':message' => $eventMsg,
                ':time'  => date(DT_FORMAT)
            )                
        );    
    } 
    
    public static function cleanup() {
        $cleanupInterval = EVENTS_CLEANUP*24*3600;
        
        $oldTime = self::getLatestSuccessTime(0);
        if(!empty($oldTime) && $oldTime + $cleanupInterval < $time())
        {
            $startTime = time() - $cleanupInterval;
            $dbHandler = Core::getDBHandle();
            $count = $dbHandler->exec('DELETE FROM '.EVENTS_TBL.' WHERE time < '.$startTime);
            self::write(0,self::E_INFO,'Deleted '.$count.' logs');
        }
    }
}
