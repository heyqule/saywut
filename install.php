<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_error',1); 

require_once 'config.php';

try
{

    
    echo 'Initializing DB... <br />';
    $file_db = new PDO('sqlite:'.DB_PATH,DB_USER,DB_PASS);
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);

    if(!defined('INSTALLED')) {
        echo '[V0.1] Creating Posts Tables... <br />';
        $file_db->exec("CREATE TABLE IF NOT EXISTS posts (
                        id INTEGER PRIMARY KEY, 
                        title TEXT,
                        provider_id INTEGER,
                        provider_cid TEXT,                    
                        contents TEXT, 
                        tags TEXT,
                        custom_data TEXT,
                        time INTEGER,
                        UNIQUE(provider_id, provider_cid) ON CONFLICT REPLACE)");

        echo 'Writing System Config... <br />';
        $handle = fopen("./config.php", "a");
        $contents = "\ndefine('INSTALLED',true);";
        fwrite($handle, $contents);
        fclose($handle);
    }
    else
    {
        echo 'Already Installed  <br />';
    }
    
    if(!defined('UPGRADE_0.2')) {
        echo '[V0.2] Creating Event Tables... <br />';
        $file_db->exec("CREATE TABLE IF NOT EXISTS events (
                        id INTEGER PRIMARY KEY, 
                        bot_id INTEGER,
                        event_type INTEGER,
                        message TEXT, 
                        time INTEGER)");

        echo 'Writing System Config... <br />';
        $handle = fopen("./config.php", "a");
        $contents = "\ndefine('UPGRADE_0.2',true);";
        fwrite($handle, $contents);
        fclose($handle);        
    }
    else
    {
        echo 'Already Upgraded to 0.2 <br />';
    }
    
    echo 'Done...';
    
} catch(Exception $e) {
    echo '<br />ERROR:<br />';
    echo nl2br(print_r($e,true));
}