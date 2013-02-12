<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ROOT_PATH.DS.'config.php';

final class Core {        
    
    protected static $db_res;
    
    public static function getDBHandle() {
        if(static::$db_res == null) {
            static::$db_res = new PDO('sqlite:'.DB_PATH,DB_USER,DB_PASS);
        }        
        return static::$db_res;
    }  
    
    public static function getMetaTags($url) {
        
        $contents = file_get_contents($url);
        
        libxml_use_internal_errors(true);
                
        $doc = new DomDocument();
        $doc->loadHTML($contents);
        $metas = $doc->getElementsByTagName('meta');
        
        $rc = array();
        
        foreach ($metas as $meta) {
            $name = $meta->getAttribute('name');
            if(empty($name))
            {
                $name = $meta->getAttribute('property');
            }
                        
            $content = $meta->getAttribute('content');
            if(empty($content)) {
                $content = $meta->getAttribute('value');
            }

            if(!empty($name) && !empty($content)) {
               $rc[$name] = $content;
            }
        }
        return $rc;
    }
}
