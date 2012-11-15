<?php
/** Class Timelines Model
 * Used by the Timelines controller to get list of user's friends from Facebook
 * 
 * @package cmu
 * @see Timelines
 * @author Igor Olejar <igor.olejar@gmail.com>
 */
class Timelines_model extends CI_Model
{
    private $photos = array();
    private $videos = array();
    private $user_wall = array();
    private $partner_wall = array();
    private $user_music = array();
    private $partner_music = array();
    private $user_events = array();
    private $partner_events = array();
    private $user_likes = array();
    private $partner_likes = array();
    private $checkins = array();
    private $br = NULL; //result from the Facebook query
    
    private $userID = NULL;
    private $partnerID = NULL;
    
    function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * Gets all the data needed
     * @param string $userID
     * @param string $partnerID
     * @return nothing
     */
    function getData($userID, $partnerID)
    {   
        //set user and partner id
        $this->setProperty('userID',$userID);
        $this->setProperty('partnerID',$partnerID);
        
        //Checkins query
        $cq = "SELECT place, tagged_ids, message_tags FROM stream WHERE source_id IN ($userID,$partnerID) AND place<>'' LIMIT ".$this->config->item('post_limit');

        //user music query
        $umq = "SELECT post_id, app_id, actor_id, message, app_data FROM stream WHERE filter_key='owner' AND source_id=me() AND app_id IN (".implode(",", $this->config->item('music_apps')).") LIMIT ".$this->config->item('post_limit');
        //$umq = "SELECT post_id, app_id, actor_id, message, app_data FROM stream WHERE filter_key='owner' AND source_id=me() LIMIT ".$this->config->item('post_limit');
        //partner music query
        $pmq = "SELECT post_id, app_id, actor_id, message, app_data FROM stream WHERE filter_key='owner' AND source_id=$partnerID and app_id IN (".implode(",", $this->config->item('music_apps')).") LIMIT ".$this->config->item('post_limit');

        $queries = array(
            //PHOTOS
            array('method' => 'GET', 'relative_url' => urlencode('/me/photos?fields=id,from,tags&limit='.$this->config->item('post_limit').'&until='.time())),
            //VIDEOS
            array('method' => 'GET', 'relative_url' => urlencode('/me/videos?fields=id,from,tags&limit='.$this->config->item('post_limit').'&until='.time())),
            //WALL POSTS USER
            array('method' => 'GET', 'relative_url' => urlencode('/me/posts?fields=id,comments,likes,story_tags,story,message,application&limit='.$this->config->item('post_limit').'&until='.time())),              
            //WALL POSTS PARTNER
            array('method' => 'GET', 'relative_url' => urlencode('/'.$partnerID.'/posts?fields=id,comments,likes,story_tags,story,message,application&limit='.$this->config->item('post_limit').'&until='.time())),                
            //MUSIC USER
            array('method' => 'GET', 'relative_url' => "method/fql.query?query=".str_replace(" ","+",$umq)),
            //MUSIC PARTNER
            array('method' => 'GET', 'relative_url' => "method/fql.query?query=".str_replace(" ","+",$pmq)),
            //EVENTS USER
            array('method' => 'GET', 'relative_url' => urlencode('/me/events?fields=id&limit='.$this->config->item('post_limit').'&until='.time())),
            //EVENTS PARTNER
            array('method' => 'GET', 'relative_url' => urlencode('/'.$partnerID.'/events?fields=id&limit='.$this->config->item('post_limit').'&until='.time())),
            //LIKES USER
            array('method' => 'GET', 'relative_url' => urlencode('/me/likes?limit='.$this->config->item('post_limit').'&until='.time())),
            //LIKES PARTNER
            array('method' => 'GET', 'relative_url' => urlencode('/'.$partnerID.'/likes?limit='.$this->config->item('post_limit').'&until='.time())),
            //CHECKINS
            array('method' => 'GET', 'relative_url' => "method/fql.query?query=".str_replace(" ","+",$cq)),
        );

        //encode
        $json = json_encode($queries);

        try {
            //end send request to FB
            $this->br = $this->facebook->api('?batch='.$json, 'POST');
            
        } catch (FacebookApiException $e) {
            show_error('Could not get the data from Facebook.<br />'.$e);
        }
        
        if ($this->br && $this->checkResultCodes($this->br)) {
            //disect the data
            $this->photos           = json_decode($this->br[0]['body'], true);
            $this->videos           = json_decode($this->br[1]['body'], true);
            $this->user_wall        = json_decode($this->br[2]['body'], true);
            $this->partner_wall     = json_decode($this->br[3]['body'], true);
            $this->user_music       = json_decode($this->br[4]['body'], true);
            $this->partner_music    = json_decode($this->br[5]['body'], true);
            $this->user_events      = json_decode($this->br[6]['body'], true);
            $this->partner_events   = json_decode($this->br[7]['body'], true);
            $this->user_likes       = json_decode($this->br[8]['body'], true);
            $this->partner_likes    = json_decode($this->br[9]['body'], true);
            $this->checkins         = json_decode($this->br[10]['body'], true);
        } else {
            redirect('/');
        }
    }
    
    /**
     * Return list of shared photos
     * @return array
     */
    function getPhotos()
    {
        if (!empty($this->photos)) {
            return $this->filterData($this->photos);
        } else {
            return array();
        }
    }
    
    /**
     * Gets the list of videos where both are tagged
     * @param string $userID
     * @param string $partnerID
     * @return array List of shared videos
     */
    function getVideos()
    {   
        if (!empty($this->videos)) {
            return $this->filterData($this->videos);
        } else {
            return array();
        }
    }
    
    /**
     * Gets the list of posts where
     * 1. User and partner posted on each other's walls
     * 2. User commented or liked Partner's post (and vice versa)
     * @return array List of shared videos
     */
    function getPosts()
    {   
        //sort user's posts
        $user_wall = $this->sortWall($this->user_wall, 'partnerID');
        
       //sort partner's posts
        $partner_wall = $this->sortWall($this->partner_wall, 'userID');
        
        return array_unique(array_merge($user_wall, $partner_wall));
    }
    
    /**
     * Gets the list of videos where both are tagged
     * @return array List of shared videos
     */
    function getCheckins()
    {    
        $out = array();
        foreach ($this->checkins as $checkin) {
            $out[] = element('place', $checkin);
        } unset($checkin);
        
        return array_unique($out);
    }
    
    /**
     * Gets the list likes that both partners like
     * @return array List of shared likes
     */
    function getLikes()
    {   
        return array_intersect($this->getLikesIds($this->user_likes), $this->getLikesIds($this->partner_likes));
    }
    
    /**
     * Gets the list of events that both attended
     * @return array List of event IDs
     */
    function getEvents()
    {   
        
        return array_intersect($this->getAttendedEvents($this->user_events), $this->getAttendedEvents($this->partner_events));
    }
    
    /**
     * Gets the list of music that both attended
     * @return array List of music post IDs
     */
    function getMusic()
    {   
        return array_unique(array_intersect($this->getMusicPosts($this->user_music), $this->getMusicPosts($this->partner_music)));
    }
    
    /**
     * Filters the given data array to include only the items where both partners are tagged and the items is posted by either partner
     * @param array $data
     * @return array
     */
    private function filterData($data)
    {
        $out = array();
        
        foreach ($data['data'] as $item) {
            //check if the item ID is either the user or the partner
            if (element('id', $item['from']) == $this->getParam('userID') || element('id', $item['from']) == $this->getParam('partnerID')) {
                $include_user = FALSE;
                $include_partner = FALSE;
                
                //if there are tags in the given item
                if (element('tags', $item)) {
                    foreach ($item['tags']['data'] as $tag) {
                        //include the item only if either partner is tagged
                        if (element('id', $tag) == $this->getParam('userID')) {
                            $include_user = TRUE;
                        } elseif (element('id', $tag) == $this->getParam('partnerID')) {
                            $include_partner = TRUE;
                        }
                    } unset($tag);

                    if ($include_user && $include_partner) {
                        $out[] = element('id', $item);
                    }
                }
            }
        } unset($item);
        
        return $out;
    }
    
    /**
     * Sets the class property with name $type to value $id
     * @param string $type
     * @param mixed $value
     */
    private function setProperty($type,$value)
    {
        $this->$type = $value;
    }
    
    /**
     * Returns the class property for the given pname
     * @param string $pname Parameter name
     * @return mixed
     */
    private function getParam($pname)
    {
        return $this->$pname;
    }
    
    /**
     * Removes the userID from the PostID, if the userID is a part of it (e.g. userID_postID)
     * @param string $postID
     * @return string Post ID
     */
    private function getPostId($postID)
    {
        $tmp = explode("_", $postID);
        return $tmp[1];
    }
    
    /**
     * Sorts out the $wall array (wall posts) for the given user
     * @param array $wall User's wall posts
     * @param string $user Two possible values: userID or partnerID
     * @return array Sorted wall
     */
    private function sortWall($wall, $user)
    {
        $out = array();
        
        //sort user's posts
        foreach ($wall['data'] as $post) {
            if (!in_array($this->getPostId($post['id']), $out)) {
                
                //comments
                if (element('data', $post['comments'])) { //check to see if there are any comments
                    //loop through each comment and see if the partner commented
                    foreach ($post['comments']['data'] as $comment) {
                        if (element('from', $comment)) {
                            if ($comment['from']['id'] == $this->getParam($user)) {
                                $out[] = $this->getPostId($post['id']);
                                break;
                            }
                        }
                    } unset($comment);
                }

                //likes
                if (element('likes', $post)) { // check to see if there are any likes
                    //loop through each like and see if the partner liked it
                    foreach ($post['likes']['data'] as $like) {
                        if (element('id', $like) == $this->getParam($user)) {
                            $out[] = $this->getPostId($post['id']);
                            break;
                        }
                    } unset($like);
                }


                //story tags
                if (element('story_tags', $post)) { //check to see if there are any story tags
                    foreach ($post['story_tags'] as $story_tag) {
                        if ($story_tag[0]['id'] == $this->getParam($user)) {
                            $out[] = $this->getPostId($post['id']);
                            break;
                        }
                    } unset($story_tag);
                }
            }
        } unset($post);
        
        return $out;
    }
    
    /**
     * Goes through all the posts for the user and extracts only the music related ones
     * @param array $posts
     * @return array List of music related posts
     */
    private function getMusicPosts($posts)
    {
        $out = array();
        
        foreach ($posts as $post) {
            if (element('attachment_data', $post['app_data'])) {
                $tmp = json_decode($post['app_data']['attachment_data']);

                if (!empty($tmp)) {
                    $out[] = array(
                        'post_id' => $this->getPostId($post['post_id']),
                        'href'    => $tmp->href
                    );
                }

                unset($tmp);
            }
        } unset($music);
        
        return $out;
    }
    
    /**
     * Looks at the list of events for the user and returns only the 'attended' ones
     * @param array $posts List of events
     * @return array List of attended event IDs
     */
    private function getAttendedEvents($posts)
    {
        $out = array();
        
        
        foreach ($posts['data'] as $post) {
            if (element('rsvp_status', $post) == "attending") {
                $out[] = element('id', $post);
            }
        } unset($post);
        
        return $out;
    }
    
    /**
     * 
     * @param array $posts
     * @return array
     */
    private function getLikesIds($posts)
    {
        $out = array();
        
        foreach (element('data', $posts) as $post) {
            $out[] = element('id', $post);
        } unset($post);
        
        return $out;
    }
    
    /**
     * Goes through the array of results from Facebook and checks the status codes.
     * They should all be 200
     * 
     * @param array $posts
     * @return boolean
     */
    private function checkResultCodes($posts)
    {
        $out = TRUE;
        
        foreach ($posts as $post) {
            if (element('code', $post) != '200') { //all of the status codes should be 200
                $out = FALSE;
                break;
            }
        } unset($post);
        
        return $out;
    }
    
    /**
     * Saves the timeline in the database
     * @param array $data All the data to be saved
     * @return string   Hashvalue of the new timeline
     */
    public function saveTimeline($data)
    {
        //create a hash value from user's and partner's IDs
        $hashvalue = hash('sha1', $this->getParam('userID').$this->getParam('partnerID').time());
        
        //check if user exists in the user table
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('user_fb_id', $this->getParam('userID'));
        $this->db->limit(1);
        
        $q = $this->db->get();
        
        $user_exists = ($q->num_rows() > 0)? TRUE : FALSE;
        
        //if multiples not allowed, check if the user exists in the user table
        if (!$this->config->item('multiple') && $user_exists) {
            return FALSE;
        }
        
        //create new user if user doesn't exist. Otherwise, just get user's ID
        if (!$user_exists) {
            //create new user
            $to_save = array(
                'user_fb_id'    =>  $this->getParam('userID'),
                'user_name'     =>  $this->session->userdata('user_name')
            );
            
            $this->db->insert('user', $to_save);
            unset($to_save);
            
            $db_user_id = $this->db->insert_id();
        } else {
            $row = $q->row();
            $db_user_id = $row->id;
        }
        
        //now that we have the user database ID, let's save stuff
        $to_save = array(
            'hashvalue'     =>  $hashvalue,
            'user_id'       =>  $db_user_id,
            'partner_fb_id' =>  $this->getParam('partnerID'),
            'partner_name'  =>  $this->session->userdata('chosen_partner_name'),
            'photos'        => serialize($data['photos']),
            'videos'        => serialize($data['videos']),
            'posts'         => serialize($data['posts']),
            'music'         => serialize($data['music']),
            'events'        => serialize($data['events']),
            'likes'         => serialize($data['likes']),
            'checkins'      => serialize($data['checkins'])
        );
        
        if ($this->db->insert('timelines', $to_save)) {
            return $hashvalue;
        }
    }
}

/* End of file timelines_model.php */
/* Location: ./application/models/timelines_model.php */