<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ROOT_PATH.DS.'includes'.DS.'Post.php';

class Post_Collection extends Post_Resource
{
    protected $_where;

    function __construct() {
        parent::__construct();
        $this->_where = array();
    }

    function addWhere($name,$op,$value,$isOr = false,$name_postfix = '') {
        $temp = new stdClass();
        $temp->isOr = $isOr;
        $temp->name = $name;
        $temp->name_postfix = $name_postfix;
        $temp->op = $op;
        $temp->value = $value;
        $this->_where[$name.$name_postfix] = $temp;
    }
    
    public function loadByQuery($offset = 0, $limit = 10)
    {
        
        $sql = "SELECT DISTINCT * FROM ".POSTS_TBL." ";

        $sql .= $this->_buildWhere();
        
        $sql .= "ORDER BY time DESC LIMIT :offset, :limit";       

        $sth = $this->db_res->prepare($sql);

        foreach($this->_where as $val) {
            $sth->bindValue(':'.$val->name.$val->name_postfix,$val->value);
        }

        $sth->bindValue(':offset',$offset);
        $sth->bindValue(':limit',$limit);

        $sth->execute();          
        
        $rows = $sth->fetchAll();
        
        return $this->_fetchRows($rows);                 
    }

    protected function _buildWhere() {
        $isFirst = true;
        $sql = '';
        foreach($this->_where as $val)
        {
            if($isFirst) {
                $sql .= "WHERE {$val->name} {$val->op} :{$val->name}{$val->name_postfix} ";
                $isFirst = false;
            }
            else
            {
                if($val->isOr)
                {
                    $sql .= "OR {$val->name} {$val->op} :{$val->name}{$val->name_postfix} ";
                }
                else
                {
                    $sql .= "AND {$val->name} {$val->op} :{$val->name}{$val->name_postfix} ";
                }

            }
        }
        return $sql;
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
            $post->hidden = $row['hidden'];
            $post->update_time = $row['update_time'];
            $rc[$post->id] = $post;
        }
        
        return $rc;
    }

    public function getSize() {

        $sql = "SELECT count(*) FROM ".POSTS_TBL.' ';
        $sql .= $this->_buildWhere();

        $sth = $this->db_res->prepare($sql);

        foreach($this->_where as $val) {
            $sth->bindValue(':'.$val->name.$val->name_postfix,$val->value);
        }

        $sth->execute();

        $result = $sth->fetchAll();

        return $result[0][0];
    }
}
