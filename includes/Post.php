<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.DIRECTORY_SEPARATOR.'Post_Resource.php';

class Post 
{    
    
    protected $data;
    protected $resource;
    
    function __construct() {
        $this->resource = new Post_Resource();
    }
    
    public function load($id) {
        try {  
            $this->data = $this->resource->load($id);
        }
        catch(Exception $e) {
            throw new Exception($e->getMessage().' @ '.$e->getLine().' in '.$e->getFile());
        }            
    }
    
    public function save() {
        try {  
            $loadedData = null;
            if(isset($this->data['id']))
            {
                $loadedData = $this->resource->load($this->data['id']);
            }
            if(isset($this->data['provider_id']) && isset($this->data['provider_cid']) )
            {
                $loadedData = $this->resource->loadByProvider($this->data['provider_id'],$this->data['provider_cid']);
            }
            if($loadedData) {
                foreach($loadedData as $key => $value)
                {
                    if(empty($this->data[$key]))
                    {
                        $this->data[$key] = $value;
                    }
                }
            }
            $this->id = $this->resource->replace($this->data);
        }
        catch(Exception $e) {
            throw new Exception($e->getMessage().' @ '.$e->getLine().' in '.$e->getFile());
        }        
    }
    
    public function delete() {
        try {            
            if($this->id)
            {
                $this->resource->delete($this->id);
            }
        }
        catch(Exception $e) {
            throw new Exception($e->getMessage().' @ '.$e->getLine().' in '.$e->getFile());
        }           
    }
    
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }    
    
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**  As of PHP 5.1.0  */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }    
}
