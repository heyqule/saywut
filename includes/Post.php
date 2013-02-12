<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once ROOT_PATH.DS.'includes'.DS.'Post_Resource.php';

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
    
    public function loadByProdviderId($pid,$pcid) {
        try {
            $this->data = $this->resource->loadByProvider($pid, $pcid);
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage().' @ '.$e->getLine().' in '.$e->getFile());
        }                
    }
    
    public function save() {
        try { 
            $this->id = $this->resource->replace($this->data);
            return $this->id;
        }
        catch(Exception $e) {
            Event::write(
                    $this->provider_id,
                    Event::E_ERROR,
                    $e->getMessage().' @ '.$e->getLine().' in '.$e->getFile()
                 );
            return false;
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
        if (is_array($this->data) && array_key_exists($name, $this->data)) {
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
