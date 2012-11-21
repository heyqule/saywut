<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_error',1); 
require_once './includes/config.php';


try
{
    if(defined('INSTALLED')) {
        die('System is installed');
    }    
    
    echo 'Initializing DB... <br />';
    $file_db = new PDO('sqlite:'.DB_PATH,DB_USER,DB_PASS);
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);

    echo 'Creating Tables... <br />';
    $file_db->exec("CREATE TABLE IF NOT EXISTS posts (
                    id INTEGER PRIMARY KEY, 
                    title TEXT,
                    provider_id INTEGER,
                    provider_cid TEXT,                    
                    contents TEXT, 
                    tags TEXT,
                    time INTEGER)");
    
    echo 'Writing System Config... <br />';
    $handle = fopen("./includes/config.php", "a");
    $contents = "\ndefine('INSTALLED',true);\n";
    fwrite($handle, $contents);
    fclose($handle);
    
    echo 'Done...';
    
} catch(Exception $e) {
    echo '<br />ERROR:<br />';
    echo nl2br(print_r($e,true));
}