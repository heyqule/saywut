<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once ROOT_PATH.DS.'includes'.DS.'Core.php';
require_once ROOT_PATH.DS.'includes'.DS.'Event.php';

class Post_Resource {
    protected $db_res;
    
    protected $upsert_stm;   
    
    function __construct() {
        
        $this->db_res = Core::getDBHandle();
        
        $insert = "REPLACE INTO ".POSTS_TBL." (id, title, provider_id, provider_cid , contents, tags, custom_data, time, hidden, update_time)
                    VALUES (:id, :title, :provider_id, :provider_cid, :contents, :tags, :custom_data, :time, :hidden, :update_time)";
        
        $this->upsert_stm = $this->db_res->prepare($insert);            
    }
    
    public function replace($data) {
        // Execute statement
        if(empty($data['id']))
        {
           $data['id'] = null; 
        }

        if(empty($data['hidden']))
        {
            $data['hidden'] = 0;
        }

        if(empty($data['provider_cid']))
        {
            $data['provider_cid'] = null;
        }

        if(empty($data['custom_data']))
        {
            $data['custom_data'] = null;
        }

        if(empty($data['tags']))
        {
            $data['tags'] = null;
        }

        $this->upsert_stm->execute(
            array(
                ':id' => $data['id'], 
                ':title' => $data['title'], 
                ':provider_id' => $data['provider_id'],
                ':provider_cid' => $data['provider_cid'],
                ':contents' => $data['contents'],
                ':tags' => $data['tags'],
                ':custom_data' => $data['custom_data'],
                ':time'  => $data['time'],
                ':update_time'  => date(DT_FORMAT, time()),
                ':hidden'       => $data['hidden']
            )                
        );
        
        return $this->db_res->lastInsertId();
    }    
    
    public function delete($id) {
        $this->db_res->exec('DELETE FROM '.POSTS_TBL.' WHERE id = '.$id);
    }
    
    public function load($id) {
        $result = $this->db_res->query('SELECT * FROM '.POSTS_TBL.' WHERE id = '.$id);           
        
        $rc_row = null;
        if(!empty($result))
        {
            foreach($result as $row) {
                $rc_row = $row;
            }       
        }
        
        return $rc_row;
    } 
    
    public function loadByProvider($pid,$pcid) {
        $result = $this->db_res->query('SELECT * FROM '.POSTS_TBL.' WHERE 
            provider_id = '.$pid.' AND provider_cid = '.$pcid);           
        
        $rc_row = null;
        if(!empty($result))
        {
            foreach($result as $row) {
                $rc_row = $row;
            }       
        }
        
        return $rc_row;        
    }
}
