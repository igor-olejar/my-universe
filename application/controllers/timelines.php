<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Timelines controller. Analyzes the user's and partner's data
 * 
 * @package cmu
 * @author Igor Olejar <igor.olejar@gmail.com>
 */


class Timelines extends CI_Controller 
{
    private $user;
    
    function __construct()
    {
        parent::__construct();
        
        //check if user logged into facebook
        if (!$this->facebook->getUser()) {
            //redirect to start
            redirect('/');
        } else {
            $this->setUser($this->facebook->getUser());
        }
    }
	
    /**
     * Index model. Collects data from the model and shows the 'timelines' view
     */
    function index() 
    {   
        if ($this->getUser() && $this->session->userdata('chosen_partner_id')) {
            
            //load the timelines model
            $this->load->model('Timelines_model', 'tm');
            
            //get the data
            $this->tm->getData($this->getUser(), $this->session->userdata('chosen_partner_id'));
            
            //photos
            $data = array(
                'photos'    =>  $this->tm->getPhotos(),
                'videos'    =>  $this->tm->getVideos(),
                'posts'     =>  $this->tm->getPosts(),
                'music'     =>  $this->tm->getMusic(),
                'events'    =>  $this->tm->getEvents(),
                'likes'     =>  $this->tm->getLikes(),
                'checkins'  =>  $this->tm->getCheckins()
            );  
            
            $data['user'] = $this->getUser();
            $data['partner_id'] = $this->session->userdata('chosen_partner_id');
            $data['partner_name'] = $this->session->userdata('chosen_partner_name');
            
            //save in the database
            if ($hashvalue = $this->tm->saveTimeline($data)) {
                $data['share_url'] = base_url() . 'timelines/share/' . $hashvalue;
            }
            
            $data['logout_url'] = $this->facebook->getLogoutUrl();
            
            $this->template->show('timelines', $data);

        } else {
            show_error('Ooops! An error occured');
        }
    }
    
    /**
     * Share method. Posts the link on partner's wall
     *
     * @param string $hashvalue The unique hashvalue for the new shared timeline
     * @return nothing
     */
    public function share($hashvalue)
    {
        $post_array = array(
            'message'   => $this->config->item('post_message'),
            'link'      => base_url() . 'show/timeline/' . $hashvalue,
        );

        
        try {
            $res = $this->facebook->api('/'.$this->session->userdata('chosen_partner_id').'/feed', 'POST', $post_array);
            echo "Posted";
        } catch (FacebookApiException $e) {
            show_error("Posting on your friend's wall resulted in error<br />".$e);
        }
    }
    
    /**
     * User ID setter
     *
     *@param string $fbID Facebook user ID
     *@return nothing
     */
    private function setUser($fbID)
    {
        $this->user = $fbID;
    }
    
    /**
     * User ID getter
     *
     * @return string Facebook ID
     */
    private function getUser() 
    {
        return $this->user;
    }
}

/* End of file timelines.php */
/* Location: ./application/controllers/timelines.php */