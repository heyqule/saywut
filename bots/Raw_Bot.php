<?php
/**
 * Created by JetBrains PhpStorm.
 * User: heyqule
 * Date: 7/2/13
 * Time: 8:47 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Saywut;

require_once SAYWUT_ROOT_PATH.DS.'includes'.DS.'Bot.php';

class Raw_Bot extends Bot {

    function __construct($id,$data,$autoStore = false) {
        parent::__construct($id);

        $this->interval = self::NOT_A_CRON;
        $this->data = $data;

        if($autoStore && $data) {
            $this->store();
        }
    }

    public function setData($data,$autoStore = false) {
        $this->data = $data;

        if($autoStore && $data) {
            $this->store();
        }

        return $this;
    }

    public function fetch() {
    }

    public function store() {
        if(is_array($this->data))
        {
            $this->numberChanged = 0;
            foreach($this->data as $value) {

                if(empty($value) || empty($value->contents))
                {
                    $this->error[] = 'Data have missing required contents' ;
                }

                $post = new Post();
                $post->id = null;
                if(!empty($value->id))
                    $post->id = $value->id;

                if(!empty($value->title)) {
                    $post->title = $value->title;
                }

                $post->provider_id = $this->provider_id;

                $post->provider_cid = null;
                if(!empty($value->provider_cid))
                {
                    $post->provider_cid = $value->provider_cid;
                }
                else
                {
                    $post->provider_cid = uniqid();
                }

                $post->keywords = $value->keywords;

                $post->contents = $value->contents;

                $post->create_time = date(DT_FORMAT, time());



                if(!empty($value->meta) && isset($value->meta->hidden))
                {
                    $post->meta = new \stdClass();
                    $post->meta->hidden = $value->meta->hidden;
                }
                else
                {
                    $post->meta = new \stdClass();
                    $post->meta->hidden = 0;
                }

                $post->update_time = date(DT_FORMAT, time());


                if($post->save())
                {
                    $this->numberChanged++;
                }
                else
                {
                    $this->error[] = 'Unable save '.$value->title;
                }
            }
        }
        else
        {
            $this->error[] = 'Invalid Data Format';
        }
    }

}