<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once ROOT_PATH.DS.'includes'.DS.'Core.php';
require_once ROOT_PATH.DS.'includes'.DS.'Event.php';

class Post_Resource {
    protected $db_res;

    protected $insert_post_stm;
    protected $insert_post_meta_stm;
    protected $insert_post_search_stm;
    
    function __construct() {
        $this->db_res = Core::getDBHandle();
    }

    public function getDBHandle() {
        return $this->db_res;
    }
    
    public function save($data) {
        // Execute statement
        if(empty($data['id']))
        {
           $data['id'] = null;
        }

        if(empty($data['provider_cid']))
        {
            $data['provider_cid'] = null;
        }

        if(empty($data['title']))
        {
            $data['title'] = null;
        }

        if(empty($data['keywords']))
        {
            $data['keywords'] = null;
        }

        if(empty($insert_post_stm))
        {

            $insert_post = "INSERT INTO ".POSTS_TBL." (id, title, provider_id, provider_cid , contents, create_time, update_time, keywords)
                        VALUES (:id, :title, :provider_id, :provider_cid, :contents, :create_time, :update_time, :keywords)
                        ON DUPLICATE KEY UPDATE title = :title, contents = :contents, create_time = :create_time, update_time = :update_time,
                        provider_cid = :provider_cid, keywords = :keywords;";

            $this->insert_post_stm = $this->db_res->prepare($insert_post);

            $insert_post_meta = "INSERT INTO ".META_TBL." (id, post_id, meta_name, meta_value)
                VALUES (:id, :post_id, :meta_name, :meta_value) ON DUPLICATE KEY UPDATE meta_value = :meta_value;";

            $this->insert_post_meta_stm = $this->db_res->prepare($insert_post_meta);

            $insert_post_search = "INSERT INTO ".SEARCH_TBL." (id, title, contents, keywords) VALUES (:id, :title, :contents, :keywords);";

            $this->insert_post_search_stm = $this->db_res->prepare($insert_post_search);
        }

        $this->insert_post_stm->execute(
            array(
                ':id' => $data['id'],
                ':title' => $data['title'],
                ':provider_id' => $data['provider_id'],
                ':provider_cid' => $data['provider_cid'],
                ':contents' => $data['contents'],
                ':create_time'  => $data['create_time'],
                ':update_time'  => date(DT_FORMAT, time()),
                ':keywords' => $data['keywords']
            )
        );

        $post_id = $this->db_res->lastInsertId();

        if(!empty($data['meta'])) {
            foreach($data['meta'] as $key => $value) {
                $this->insert_post_meta_stm->execute(
                    array(
                        ':id'=>null,
                        ':post_id'=>$post_id,
                        ':meta_name'=>$key,
                        ':meta_value'=>$value
                    )
                );
            }
        }

        $this->insert_post_search_stm->execute(
            array(
                ':id' => $post_id,
                ':title' => $data['title'],
                ':contents' => $data['contents'],
                ':keywords' => $data['keywords']
            )
        );

        return $post_id;
    }    
    
    public function delete($id) {
        $stm = $this->db_res->prepare('DELETE FROM '.POSTS_TBL.' WHERE id = :id');
        $stm->execute(array(':id'=>$id));
        $this->db_res->exec(''.$id);

        $stm2 = $this->db_res->prepare('DELETE FROM '.SEARCH_TBL.' WHERE id = :id');
        $stm2->execute(array(':id'=>$id));
        $this->db_res->exec(''.$id);
    }
    
    public function load($id) {
        $stm = $this->db_res->prepare('SELECT * FROM '.POSTS_TBL.' WHERE id = :id');
        $stm->execute(array(':id'=>$id));
        
        $rc_row = $this->_fetchResult($stm->fetch(PDO::FETCH_ASSOC));
        
        return $rc_row;
    } 
    
    public function loadByProvider($pid,$pcid) {
        $stm = $this->db_res->prepare('SELECT * FROM '.POSTS_TBL.' WHERE
            provider_id = :provider_id AND provider_cid = :provider_cid');

        $stm->execute(array(':provider_id'=>$pid,':provider_cid'=>$pcid));

        $rc_row = $this->_fetchResult($stm->fetch(PDO::FETCH_ASSOC));
        
        return $rc_row;        
    }

    public function reindexAll() {
        $stm = $this->db_res->prepare(
            'INSERT INTO '.SEARCH_TBL.' SELECT t.id, t.title, t.contents, t.keywords FROM '.POSTS_TBL.' t LEFT JOIN '.SEARCH_TBL.' s on s.id=t.id WHERE s.id is null');

        $stm->execute();

        $rowCount = $stm->rowCount();

        $stm = $this->db_res->prepare(
            'DELETE FROM '.SEARCH_TBL.' WHERE id = (SELECT s.id FROM '.SEARCH_TBL.' s LEFT JOIN '.POSTS_TBL.' t on t.id=s.id WHERE t.id is null)');

        $rowCount += $stm->rowCount();

        $stm->execute();

        return $stm->rowCount();
    }

    protected function _fetchResult($result) {
        $rc_row = null;

        if(!empty($result))
        {
            $rc_row = $result;
            $meta_result = $this->db_res->query('SELECT * FROM '.META_TBL.' WHERE post_id = '.$rc_row['id'],PDO::FETCH_ASSOC);

            foreach($meta_result as $row) {
                if(empty($rc_row['meta'])) {
                    $rc_row['meta'] = new stdClass();
                }
                $rc_row['meta']->$row['meta_name'] = $row['meta_value'];
            }

        }

        return $rc_row;
    }
}
