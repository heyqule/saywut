<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once __DIR__.DIRECTORY_SEPARATOR.'Post_Collection.php';
abstract class Bot {
    protected $curl_settings;
    
    function __construct() {
        $this->curl_settings = array();
        $this->curl_settings[CURLOPT_CONNECTTIMEOUT] = 5;
        $this->curl_settings[CURLOPT_RETURNTRANSFER] = 1;
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
        $this->fetch();
        $this->store();
    }
}
