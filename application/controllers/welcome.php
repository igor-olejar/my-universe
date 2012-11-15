<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Default controller. Shows the landing page with the facebook login button or 
 * the "go" form if the user is logged in.
 * 
 * @package cmu
 * @author Igor Olejar <igor.olejar@gmail.com>
 */


class Welcome extends CI_Controller 
{
    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Index method.
     * Checks whether the user is logged in. If yes, collect data
     */
    function index() 
    {
        //load welcome model
        $this->load->model('Welcome_model', 'wm');
            
        //save the facebook object in data array for use in the footer
        $data['app_id'] = $this->facebook->getAppID();
        
        $user = $this->facebook->getUser();
        
        //Try getting the user profile from Facebook. If fail, set user to NULL
        if ($user) {
            
            //first check if the user has created a timeline previously
            if ($timeline = $this->wm->previousTimelineExists($user) && !$this->config->item('multiple')) {
                redirect('/show/'.$timeline);
            }
            
            try {
                $data['user_profile'] = $this->facebook->api('/me', 'GET');
            } catch (FacebookApiException $e) {
                $user = null;
            }
        }

        //login and logout urls
        if ($user) {
            $data['logout_url'] = $this->facebook->getLogoutUrl();
        } else {
            $data['login_url'] = $this->facebook->getLoginUrl();
        }
        
        //if user exists, add the name to session for later use
        if ($user) {
            $sess_data = array(
                'user_name'          =>  $data['user_profile']['name'],
                'user_first_name'    =>  $data['user_profile']['first_name'],
                'user_last_name'     =>  $data['user_profile']['last_name']
            );
            $this->session->set_userdata($sess_data);
            unset($sess_data);
        }
        
        
        //various relevant data
        if ($user) {
            //singles array
            $data['singles_array'] = $this->config->item('singles_array');
            
            //list of friends
            $data['friends'] = $this->wm->getFbFriends();
            
            //relationship data
            $data['rel_status'] = (array_key_exists('relationship_status', $data['user_profile']))? $data['user_profile']['relationship_status'] : NULL;
                
            //significant other
            if (array_key_exists('significant_other', $data['user_profile'])) {
                $data['partner_name'] = $data['user_profile']['significant_other']['name'];
                $data['partner_id'] = $data['user_profile']['significant_other']['id'];
            } else {
                $data['partner_name'] = '';
                $data['partner_id'] = 0;
            }
        }
        
        //add the user login status to data array
        $data['user'] = $user;
        
        //add the facebook permissions to the data array
        $data['fb_perms'] = implode(", ", $this->config->item('fb_perms'));
        
        //show the landing page
        $this->template->show('landing', $data);
        
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */