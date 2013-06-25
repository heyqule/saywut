<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ROOT_PATH.DS.'includes'.DS.'Post_Collection.php';

abstract class Bot {

    protected $interval; //In Minutes
    
    protected $numberChanged;   
    
    protected $provider_id;

    protected $error;
    
    function __construct() {

        $this->interval = 24*60;
        $this->numberChanged = 0;
        $this->error = false;
    }    
    
    protected function _buildQueryString($arr) {
        $rc = '';
        foreach($arr as $key => $value) {
            $rc .= urlencode($key).'='.urlencode($value).'&';
        }
        return $rc;
    }
            
    protected function _fetchRawData() {
        return Core::runCurl($this->curl_settings);
    }
    
    abstract protected function fetch();
    abstract protected function store();
    
    public function run() {
        if($this->runnable() && !$this->error)
        {
            $this->fetch();
            if(!$this->error)
            {
                $this->store();
                Event::write($this->provider_id,Event::E_SUCCESS,'Number of Change - '.$this->numberChanged);
            }
        }
    }
    
    public function runnable() {
        $time = strtotime(Event::getLatestSuccessTime($this->provider_id));        
        if( time() > ($time + $this->interval * 60 - 60) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
