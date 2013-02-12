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
        $this->settings['overwrite'] = false;
        
        if(!empty($config['overwrite']))
        {
            $this->settings['overwrite'] = $config['overwrite'];
        }
        
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
                
                $targetPost = new Post();
                $targetPost->loadByProdviderId($post->provider_id, $post->provider_cid);
                if(!empty($targetPost->id) && !$this->settings['overwrite']) {
                   continue; 
                }
                
                if(!empty($value->entities->media[0]->media_url))
                {                
                    $custom_data = new stdClass();

                    $custom_data->imageUrl = $value->entities->media[0]->media_url;
                
                    $post->custom_data = json_encode($custom_data);
                }
                else if(!empty($value->entities->urls[0]->expanded_url))
                {
                    $metas = Core::getMetaTags($value->entities->urls[0]->expanded_url);                    
                    
                    $custom_data = new stdClass();

                    $custom_data->extUrl = $value->entities->urls[0]->expanded_url;                                        
                    
                    if(!empty($metas['twitter:player']))
                    { 
                        $custom_data->card_video_url = $metas['twitter:player'];  
                    }
                    elseif(!empty($metas['og:video']))
                    {
                        $custom_data->card_video_url = $metas['og:video'];  
                    }
                    
                    
                    if(empty($custom_data->card_video_url))
                    {                           
                        $image_url = '';
                        if(!empty($metas['twitter:image']))
                        {
                             $image_url = $metas['twitter:image'];
                        }        
                        elseif(!empty($metas['og:image']))
                        {
                             $image_url = $metas['og:image'];
                        }
                        
                        if(strpos($image_url,'://') === false) {
                            $domain = parse_url($custom_data->extUrl);
                            $custom_data->card_photo_url = $domain['scheme'].'://'.$domain['host'].'/'.$image_url;
                        }
                        else
                        {
                            $custom_data->card_photo_url = $image_url;
                        }
                    }
                    
                    
                    if(!empty($metas['twitter:site']))
                    {
                        $custom_data->card_holder = $metas['twitter:site'];
                    } 
                    elseif(!empty($metas['og:site_name']))
                    {
                        $custom_data->card_holder = $metas['og:site_name'];
                    }
                    
                    
                    if(!empty($metas['twitter:title']))
                    {                    
                        $custom_data->card_title = $metas['twitter:title'];
                    }
                    elseif(!empty($metas['og:title'])) 
                    {
                        $custom_data->card_title = $metas['og:title'];
                    }
                    elseif(!empty($metas['title']))
                    {
                        $custom_data->card_title = $metas['title'];
                    }

                    
                    if(!empty($metas['twitter:description']))
                    {                    
                        $custom_data->card_description = $metas['twitter:description'];                    
                    }
                    elseif(!empty($metas['og:description'])) 
                    {
                        $custom_data->card_description = $metas['og:description'];                    
                    }
                    elseif(!empty($metas['description'])) 
                    {
                        $custom_data->card_description = $metas['description'];                    
                    }                    
                                    
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
