<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Saywut;

require_once SAYWUT_ROOT_PATH.DS.'config.php';

final class Core {        
    
    protected static $db_res;
    protected static $bot_types = array();
    
    public static function getDBHandle() {
        if(static::$db_res == null) {
            static::$db_res = new \PDO('mysql:host='.MYSQL_DB_HOST.';port='.MYSQL_DB_PORT.';dbname='.MYSQL_DB_NAME.';charset=utf8',
                MYSQL_DB_USER,MYSQL_DB_PASS);
        }        
        return static::$db_res;
    }


    public static function getBotKey($botInfo) {
        $id = md5($botInfo['class'].$botInfo['name']);

        self::getBots();

        if(empty(static::$bot_types[$id])) {
            $stm = static::$db_res->prepare('INSERT INTO '.BOTS_TBL.' (class, name) VALUES (:class, :name);');
            if($stm->execute(
                array(':class'=>$botInfo['class'],':name'=>$botInfo['name'])
            ))
            {
                return static::$db_res->lastInsertId();
            }
        }

        return static::$bot_types[$id]['id'];
    }

    public static function getBots() {
        self::getDBHandle();
        if(empty(static::$bot_types))
        {
            $stm = static::$db_res->prepare('SELECT * FROM '.BOTS_TBL);
            $stm->execute();
            $result = $stm->fetchAll(\PDO::FETCH_ASSOC);
            foreach($result as $value) {
                static::$bot_types[md5($value['class'].$value['name'])] = $value;
            }
        }

        return static::$bot_types;
    }

    public static function getBotName($id) {
        self::getBots();
        foreach(static::$bot_types as $value) {
            if($value['id'] == $id) {
                return $value['class'].' - '.$value['name'];
            }
        }
        return null;
    }



    
    public static function getMetaTags($url) {
        $rc = null;
        try
        {
            $settings[CURLOPT_URL] = $url;
            $contents = self::runCurl($settings);

            if(!empty($contents))
            {
                libxml_use_internal_errors(true);

                $doc = new \DomDocument();
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
            }
            return $rc;
        }
        catch (Exception $e)
        {
            return $rc;
        }
    }

    public static function runCurl($settings) {
        try
        {

            if(is_array($settings)) {
                $settings = $settings + $GLOBALS['CURL_SETTINGS'];
            }
            else
            {
                throw new \Exception('Settings is not an array');
            }

            $curl_handle=curl_init();

            if(!isset($settings[CURLOPT_URL]))
            {
                throw new \Exception('URL is missing');
            }


            curl_setopt_array($curl_handle,$settings);

            $buffer = curl_exec($curl_handle);

            curl_close($curl_handle);
            return $buffer;
        }
        catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }
}
