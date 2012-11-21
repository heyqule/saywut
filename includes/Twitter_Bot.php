<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(__DIR__.DIRECTORY_SEPARATOR.'Bot.php');
class Twitter_Bot extends Bot {
    
    protected $settings;
    
    protected $data;    
    
    protected $provider_id;
    protected $provider_name;
    
    public function __construct($id,$account) {
        parent::__construct();
        $this->settings['count'] = 60;
        $this->settings['include_rts'] = 1;
        $this->settings['account'] = $account;
        $this->provider_id = $id; 
        
        $this->setUserTimeline($account);
    }
    
    public function setUserTimeline($name = null) {
        if(!$name) {
            $name = $this->settings['account'];
        }
        $url = 'http://api.twitter.com/1/statuses/user_timeline/'.$name.'.json?'.$this->_buildQueryString($this->settings);               
        
        $this->curl_settings[CURLOPT_URL] = $url;
    }
    
    protected function fetch() {
       $rawData = $this->_fetchRawData();
       $this->data = json_decode($rawData);
    }
    
    protected function store() {    
        if(is_array($this->data))
        {
            foreach($this->data as $key => $value) {            
                $post = new Post();
                $post->id = null;            
                $post->title = null;
                $post->provider_id = $this->provider_id;
                $post->provider_cid = $value->id_str;
                $post->contents = $value->text;
                $post->tags = null;
                $post->time = date(DT_FORMAT, strtotime($value->created_at));                
                $post->save();
            }
        }
    }    
}
