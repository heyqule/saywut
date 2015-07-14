<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ROOT_PATH.DS.'includes'.DS.'Post.php';

class Post_Collection extends Post_Resource
{
    protected $_where;
    protected $_orderBy;

    protected $_raw;
    protected $_fulltext;

    function __construct() {
        parent::__construct();
        $this->_where = array();
        $this->_orderBy = array();

        $this->_raw = 0;
    }

    function addWhere($name,$op,$value,$name_postfix = '') {
        $temp = new stdClass();
        $temp->name = 'p.'.$name;
        $temp->name_postfix = $name_postfix;
        $temp->op = $op;
        $temp->value = $value;
        $temp->prefix = '';
        $temp->postfix ='';
        $this->_where[$name.$name_postfix] = $temp;
        return $this;
    }

    function addOrderBy($column,$desc = true) {
        $ordStr = ($desc) ? 'DESC' : 'ASC';
        $this->_orderBy[$column] = 'p.'.$column.' '.$ordStr;
        return $this;
    }

    function removeOrderBy($column) {
        unset($this->_orderBy[$column]);
        return $this;
    }

    function addMetaWhere($name,$op,$value,$name_postfix = '') {
        //META NAME
        $metaname = new stdClass();
        $metaname->name = 'm.meta_name';
        $metaname->name_postfix = '';
        $metaname->op = $op;
        $metaname->value = $name.$name_postfix;
        $metaname->prefix = '(';
        $metaname->postfix ='';
        $this->_where[$metaname->name.$metaname->value] = $metaname;

        $this->addRaw('AND');

        //META_VALUE
        $metavalue = new stdClass();
        $metavalue->name = 'm.meta_value';
        $metavalue->name_postfix = '';
        $metavalue->op = $op;
        $metavalue->value = $value;
        $metavalue->prefix = '';
        $metavalue->postfix =')';
        $this->_where[$metavalue->name.$metaname->value] = $metavalue;
        return $this;
    }

    public function removeWhere($attr) {
        unset($this->_where[$attr]);
        return $this;
    }

    public function removeMetaWhere($attr) {
        unset($this->_where['m.meta_name'.$attr]);
        return $this;
    }

    /**
     * Add Raw Value to sql
     */
    public function addRaw($sqlSegment) {
        $temp = new stdClass();
        $temp->name = 'raw.'.$this->_raw;
        $temp->name_postfix = '';
        $temp->value = $sqlSegment;
        $this->_where['raw.'.$this->_raw] = $temp;

        $this->_raw++;

        return $this;
    }

    public function addFullText($query) {
        $fullTextQuery = 'MATCH (s.title,s.contents,s.keywords) AGAINST (:fulltext_'.$this->_raw.' IN NATURAL LANGUAGE MODE)';

        $temp = new stdClass();
        $temp->name = 'fulltext_'.$this->_raw;
        $temp->name_postfix = '';
        $temp->value = $query;
        $temp->query = $fullTextQuery;
        $this->_where['fulltext_'.$this->_raw] = $temp;

        $this->_raw++;
        return $this;
    }
    
    public function loadByQuery($offset = 0, $limit = 10)
    {
        
        $sql = "SELECT DISTINCT p.* FROM ".POSTS_TBL." as p LEFT JOIN ".META_TBL." as m on p.id = m.post_id LEFT JOIN ".SEARCH_TBL." as s on p.id = s.id ";

        $sql .= $this->_buildWhere();

        if(empty($this->_orderBy)) {
            $this->_orderBy[] = 'p.create_time DESC';
        }

        $sql .= "ORDER BY ".implode(',',$this->_orderBy)." LIMIT :offset, :limit";

        $sth = $this->db_res->prepare($sql);

        foreach($this->_where as $val) {
            $sth->bindValue(':'.str_replace('.','',$val->name).$val->name_postfix,$val->value,PDO::PARAM_STR);
        }

        $sth->bindParam(':offset',$offset,PDO::PARAM_INT);
        $sth->bindParam(':limit',$limit,PDO::PARAM_INT);

        $sth->execute();
        
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC);
        
        return $this->_fetchRows($rows);                 
    }

    protected function _buildWhere() {
        $isFirst = true;
        $sql = '';
        foreach($this->_where as $key => $val)
        {
            if($isFirst) {
                $sql .= " WHERE ";
                $isFirst = false;
            }

            if(strpos($key,'raw.') !== false)
            {
                $sql .= ' '.$val->value.' ';
                unset($this->_where[$key]);
                continue;
            }

            if(strpos($key,'fulltext_') !== false)
            {
                $sql .= ' '.$val->query.' ';
                continue;
            }


            $sql .= "{$val->prefix} {$val->name} {$val->op} :".str_replace('.','',$val->name)."{$val->name_postfix} {$val->postfix} ";
        }
        return $sql;
    }

    protected function _fetchRows($rows) {
        
        $rc = array();
        $ids = array();
        
        foreach($rows as $row) {
            $post = new Post();
            $post->setData($row);
            $rc[$post->id] = $post;
            $ids[] = $post->id;
        }

        if(!empty($ids))
        {
            $result = $this->db_res->query("SELECT * FROM ".META_TBL.' WHERE post_id IN ('.implode(',',$ids).')',PDO::FETCH_ASSOC);

            foreach($result as $row) {
                $post = $rc[$row['post_id']];
                if(empty($post->meta)) {
                    $post->meta = new stdClass();
                }
                $post->meta->$row['meta_name'] = $row['meta_value'];
            }
        }
        
        return $rc;
    }

    public function getSize() {

        $sql = "SELECT DISTINCT p.id FROM ".POSTS_TBL.' as p LEFT JOIN '.META_TBL.' as m on p.id = m.post_id LEFT JOIN '.SEARCH_TBL.' as s on p.id = s.id ';
        $sql .= $this->_buildWhere();

        $sth = $this->db_res->prepare($sql);

        foreach($this->_where as $val) {
            $sth->bindValue(':'.str_replace('.','',$val->name).$val->name_postfix,$val->value);
        }

        $sth->execute();

        $result = $sth->fetchAll();

        return count($result);
    }
}
