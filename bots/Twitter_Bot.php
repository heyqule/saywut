<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once ROOT_PATH.DS.'includes'.DS.'Bot.php';

class Twitter_Bot extends Bot {
    
    protected $settings;
    
    protected $data;    
        
    protected $provider_name;
    
    public function __construct($id,$config) {
        parent::__construct();
        $this->settings['count'] = 60;
        $this->settings['include_rts'] = 1;
        $this->settings['include_entities'] = 1;
        $this->settings['account'] = $config['account'];
        
        $this->interval = $config['interval'];
        $this->provider_id = $id; 
        
        $this->setUserTimeline($config['account']);       
    }
    
    public function setUserTimeline($name = null) {
        if(!$name) {
            $name = $this->settings['account'];
        }
        $url = 'http://api.twitter.com/1/statuses/user_timeline/'.$name.'.json?'.$this->_buildQueryString($this->settings);               
        
        $this->curl_settings[CURLOPT_URL] = $url;
    }

    /*
     * Data fetching
     */
    protected function fetch() {
       $rawData = $this->_fetchRawData();
       $this->data = json_decode($rawData);
    }
    /*
     * Manipulating and storing data
     */
    protected function store() {    
        if(is_array($this->data))
        {
            $numberOfChanges = 0;
            foreach($this->data as $key => $value) {            
                $post = new Post();
                $post->id = null;            
                $post->title = null;
                $post->provider_id = $this->provider_id;
                $post->provider_cid = $value->id_str;
                $post->contents = $value->text;
                $post->tags = null;               
                
                if(!empty($value->entities->media[0]->media_url))
                {                
                    $custom_data = new stdClass();

                    $custom_data->imageUrl = $value->entities->media[0]->media_url;
                
                    $post->custom_data = json_encode($custom_data);
                }
                else if(!empty($value->entities->urls[0]->expanded_url))
                {
                    $custom_data = new stdClass();

                    $custom_data->extUrl = $value->entities->urls[0]->expanded_url;
                
                    $post->custom_data = json_encode($custom_data);                    
                }
                else {
                   $post->custom_data = null;
                }
                $post->time = date(DT_FORMAT, strtotime($value->created_at));                
                if($post->save())
                {
                    $numberOfChanges++;
                }
            }
            
            if($numberOfChanges) {
                $this->hasChanged = true;
            }
        }
    }
    
    /*
     * Override this function if you need to do more than just fetch and store
     * 
     * Example: 
     *  - fetch multiple sources
     *
     */
    
     //public function run() {}
}
