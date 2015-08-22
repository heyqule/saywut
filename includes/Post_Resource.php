<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Saywut;

require_once SAYWUT_ROOT_PATH.DS.'includes'.DS.'Core.php';
require_once SAYWUT_ROOT_PATH.DS.'includes'.DS.'Event.php';

class Post_Resource {
    protected $db_res;

    protected $insert_post_stm;
    protected $insert_post_meta_stm;
    protected $insert_post_search_stm;
    protected $delete_post_meta_stm;
    
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

            $insert_post = "INSERT INTO ".POSTS_TBL." (id, title, provider_id, provider_cid , contents, keywords, create_time, update_time)
                        VALUES (:id, :title, :provider_id, :provider_cid, :contents, :keywords, :create_time, :update_time)
                        ON DUPLICATE KEY UPDATE title = :title, contents = :contents, create_time = :create_time, update_time = :update_time,
                        provider_cid = :provider_cid, keywords = :keywords;";

            $this->insert_post_stm = $this->db_res->prepare($insert_post);

            $insert_post_meta = "INSERT INTO ".META_TBL." (id, post_id, meta_name, meta_value)
                VALUES (:id, :post_id, :meta_name, :meta_value) ON DUPLICATE KEY UPDATE meta_value = :meta_value;";

            $this->insert_post_meta_stm = $this->db_res->prepare($insert_post_meta);

            $insert_post_search = "INSERT INTO ".SEARCH_TBL." (id, title, contents, keywords) VALUES (:id, :title, :contents, :keywords)
                      ON DUPLICATE KEY UPDATE title = :title, contents = :contents, keywords = :keywords;";

            $this->insert_post_search_stm = $this->db_res->prepare($insert_post_search);

            $delete_post_meta = "DELETE FROM  ".META_TBL." WHERE post_id = :post_id and meta_name = :meta_name;";

            $this->delete_post_meta_stm = $this->db_res->prepare($delete_post_meta);
        }

        try
        {
            $this->db_res->beginTransaction();

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

            if($this->insert_post_stm->errorCode() != '00000')
            {
                throw new \Exception('Main SQL Upsert Failed');
            };

            $post_id = $this->db_res->lastInsertId();

            if(empty($post_id) && $data['id'])
            {
                $post_id = $data['id'];
            }

            if(!empty($data['meta'])) {
                foreach($data['meta'] as $key => $value) {
                    if(strlen((string) $value))
                    {
                        $this->insert_post_meta_stm->execute(
                            array(
                                ':id' => null,
                                ':post_id' => $post_id,
                                ':meta_name' => $key,
                                ':meta_value' => $value
                            )
                        );

                        if ($this->insert_post_meta_stm->errorCode() != '00000')
                        {
                            throw new \Exception('Meta SQL Upsert Failed @ ' . $key);
                        };
                    }
                    else
                    {
                        $this->delete_post_meta_stm->execute(
                            array(
                                ':post_id' => $post_id,
                                ':meta_name' => $key,
                            )
                        );

                        if ($this->delete_post_meta_stm->errorCode() != '00000')
                        {
                            throw new \Exception('Meta SQL DELETE Failed @ ' . $key);
                        };
                    }
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

            if($this->insert_post_search_stm->errorCode() != '00000')
            {
                throw new \Exception('Index SQL Failed @');
            };

            $this->db_res->commit();
            return $post_id;
        }
        catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
            $this->db_res->rollBack();
        }
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
        
        $rc_row = $this->_fetchResult($stm->fetch(\PDO::FETCH_ASSOC));
        
        return $rc_row;
    } 
    
    public function loadByProvider($pid,$pcid) {
        $stm = $this->db_res->prepare('SELECT * FROM '.POSTS_TBL.' WHERE
            provider_id = :provider_id AND provider_cid = :provider_cid');

        $stm->execute(array(':provider_id'=>$pid,':provider_cid'=>$pcid));

        $rc_row = $this->_fetchResult($stm->fetch(\PDO::FETCH_ASSOC));
        
        return $rc_row;        
    }

    public function reindexAll() {
        $stm = $this->db_res->prepare(
            'DELETE FROM '.SEARCH_TBL);

        $stm->execute();

        $stm = $this->db_res->prepare(
            'INSERT INTO '.SEARCH_TBL.' SELECT t.id, t.title, t.contents, t.keywords FROM '.POSTS_TBL.' t LEFT JOIN '.SEARCH_TBL.' s on s.id=t.id');

        $stm->execute();

        return $stm->rowCount();
    }

    protected function _fetchResult($result) {
        $rc_row = null;

        if(!empty($result))
        {
            $rc_row = $result;
            $meta_result = $this->db_res->query('SELECT * FROM '.META_TBL.' WHERE post_id = '.$rc_row['id'],\PDO::FETCH_ASSOC);

            foreach($meta_result as $row) {
                if(empty($rc_row['meta'])) {
                    $rc_row['meta'] = new \stdClass();
                }
                $rc_row['meta']->$row['meta_name'] = $row['meta_value'];
            }

        }

        return $rc_row;
    }
}
