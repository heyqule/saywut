<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once ROOT_PATH.DS.'includes'.DS.'Bot.php';
require_once ROOT_PATH.DS.'bots'.DS.'libs'.DS.'twitteroauth'.DS.'twitteroauth.php';

class Twitter_Bot extends Bot {
    
    protected $settings;

    protected $qurey_settings;
    
    protected $data;    
        
    protected $provider_name;

    protected $connection;
    
    public function __construct($id,$config) {
        parent::__construct($id);
        $this->qurey_settings['count'] = 60;
        $this->qurey_settings['include_rts'] = 1;
        $this->qurey_settings['include_entities'] = 1;
        $this->settings['account'] = $config['account'];
        $this->settings['overwrite'] = false;

        if(!empty($config['overwrite']))
        {
            $this->settings['overwrite'] = $config['overwrite'];
        }
        
        $this->interval = $config['interval'];

        $this->connection = new TwitterOAuth(
            $config['consumerKey'],
            $config['consumerSecret'],
            $config['oauthKey'],
            $config['oauthSecret']
        );

        $this->setUserTimeline($config['account']);
    }
    
    public function setUserTimeline($name = null) {
        $this->qurey_settings['screen_name'] = $name;
    }

    /*
     * Data fetching
     */
    protected function fetch() {

       $this->data = $this->connection->get('statuses/user_timeline',$this->qurey_settings);

       $this->checkError($this->data);
    }
    /*
     * Manipulating and storing data
     */
    protected function store() {
        if(is_array($this->data))
        {
            $this->numberChanged = 0;
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
                        
                        if(!empty($image_url) && strpos($image_url,'://') === false) {
                            $domain = parse_url($custom_data->extUrl);
                            $custom_data->card_photo_url = $domain['scheme'].'://'.$domain['host'].'/'.$image_url;
                        }
                        elseif(!empty($image_url))
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
                    $this->numberChanged++;
                }
            }            
        }
    }

    public function checkError($data) {
        if(!empty($data->errors)) {
            $msg = '';
            foreach($data->errors as $obj)
            {
                $msg .= $obj->code.' - '.$obj->message."\n";
            }
            $this->error = $msg;
        }

        if($this->error)
        {
            throw new Exception($this->error);
        }
    }

    public function convertJSON($buffer) {
        return json_decode(substr($buffer,strpos($buffer,'{"') ));
    }

    /*
    function __destruct() {

    }
    */
/*
 * Override this function if you need to do more than just fetch and store
 *
 * Example:
 *  - fetch multiple sources
 *
 */
    
     //public function run() {}
}
