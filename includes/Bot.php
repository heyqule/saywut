<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ROOT_PATH.DS.'includes'.DS.'Post_Collection.php';

abstract class Bot {
    protected $curl_settings;
    
    protected $interval; //In Minutes
    
    protected $hasChanged;
    
    protected $provider_id;
    
    function __construct() {
        $this->curl_settings = array();
        $this->curl_settings[CURLOPT_CONNECTTIMEOUT] = 5;
        $this->curl_settings[CURLOPT_RETURNTRANSFER] = 1;
        $this->interval = 24*60;
        $this->hasChanged = false;
    }    
    
    protected function _buildQueryString($arr) {
        $rc = '';
        foreach($arr as $key => $value) {
            $rc .= urlencode($key).'='.urlencode($value);
        }
        return $rc;
    }
            
    protected function _fetchRawData() {
        
        try
        {
            $curl_handle=curl_init();

            if(!isset($this->curl_settings[CURLOPT_URL]))
            {
                throw new Exception('URL is missing');
            }
            
            foreach($this->curl_settings as $key => $value)
            {
                curl_setopt($curl_handle,$key,$value);
            }

            $buffer = curl_exec($curl_handle);
            curl_close($curl_handle);
            return $buffer;
        }
        catch(Exception $e) 
        {
            throw new Exception($e->getMessage());
        }
    }
    
    abstract protected function fetch();
    abstract protected function store();
    
    public function run() {
        if($this->runnable())
        {
            $this->fetch();
            $this->store();
            Event::write($this->provider_id,Event::E_SUCCESS,'');
        }
    }
    
    public function runnable() {
        $time = strtotime(Event::getLastestSuccessTime($this->provider_id));        
        if( time() > ($time + $this->interval * 60 - 15) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
