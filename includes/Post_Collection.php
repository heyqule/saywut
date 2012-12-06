<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ROOT_PATH.DS.'includes'.DS.'Post.php';

class Post_Collection extends Post_Resource
{    
    function __construct() {
        parent::__construct();
    }
    
    public function loadDefault($offset = 0, $limit = 10) {
        $sql = "SELECT * FROM ".POSTS_TBL." ORDER BY time DESC LIMIT :offset, :limit";
        $sth = $this->db_res->prepare($sql);
        $sth->execute(
                array(
                    ':offset' => $offset*$limit,
                    ':limit'  => $limit
                )); 
        $rows = $sth->fetchAll();
        
        return $this->_fetchRows($rows);        
    }
    
    public function loadByTime($from,$to,$offset = 0,$limit = 10) {
        $sql = "SELECT * FROM ".POSTS_TBL." WHERE time >= :from AND time <= :to ORDER BY time DESC LIMIT :offset, :limit";
        $sth = $this->db_res->prepare($sql);
        $sth->execute(
                array(
                    ':from' => $from, 
                    ':to' => $to,
                    ':offset' => $offset*$limit,
                    ':limit'  => $limit
                ));

        $rows = $sth->fetchAll();
        
        return $this->_fetchRows($rows);
    }
    
    public function loadByProvider($id, $offset = 0,$limit = 10) {
        $sql = "SELECT * FROM ".POSTS_TBL." WHERE provider_id = :provider_id ORDER BY time DESC LIMIT :offset, :limit";
        $sth = $this->db_res->prepare($sql);
        $sth->execute(
                array(
                    ':provider_id' => $provider_id,
                    ':offset' => $offset*$limit,
                    ':limit'  => $limit
                )); 
        $rows = $sth->fetchAll();
        
        return $this->_fetchRows($rows);         
    } 
    
    protected function _fetchRows($rows) {
        
        $rc = array();
        
        foreach($rows as $row) {
            $post = new Post();
            $post->id = $row['id'];
            $post->title = $row['title'];
            $post->contents = $row['contents'];
            $post->provider_id = $row['provider_id'];
            $post->provider_cid = $row['provider_cid'];
            $post->tags = $row['tags'];
            $post->custom_data = $row['custom_data'];
            $post->time = $row['time'];
            $rc[$post->id] = $post;
        }
        
        return $rc;
    }
}
