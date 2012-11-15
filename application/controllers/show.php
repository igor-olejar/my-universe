<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Show controller to show the timelines (as read from the database)
 * 
 * @package cmu
 * @author Igor Olejar <igor.olejar@gmail.com>
 */


class Show extends CI_Controller 
{
    private $data = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Show_model', 'sm');
    }
	
    function index() 
    {
        $this->addToData('user', NULL);
        $this->template->show('timeline', $this->getData());
    }
    
    /**
     *Check if the user is logged in, whether the hashvalue is valid and whether the user's Facebook ID is valid.
     *
     *@param string $hashvalue Unique identifier for the timeline
     *@return nothing
     */
    function timeline($hashvalue = 0) {
        
        //save the facebook object in data array for use in the footer
        $this->addToData('app_id', $this->facebook->getAppID());
        
        //logout URL
        $this->addToData('logout_url', $this->facebook->getLogoutUrl());
        
        //add the facebook permissions to the data array
        //$this->addToData('fb_perms', implode(", ", $this->config->item('fb_perms')));
        
        //hashvalue must be real and the user must be logged into facebook
        if ($hashvalue && strlen($hashvalue) == 40 && $this->userValid($this->facebook->getUser(), $hashvalue)) {
            $this->addToData('user', $this->facebook->getUser());
            
            if ($data = $this->sm->getData($hashvalue)) { //try to get the data from the database
                foreach ($data as $key=>$datum) {
                    $this->addToData($key, $datum);
                } unset($datum);
            }
        } else {
            $this->addToData('user', NULL);
        }
        
        $this->template->show('timeline', $this->getData());
    }
    
    private function getData()
    {
        return $this->data;
    }
    
    private function addToData($key, $val)
    {
        $this->data[$key] = $val;
    }
    
    /**
     * Checks whether the Facebook user ID matches either the partner or the user id the database.
     *
     * @param string $userID
     * @param string $hashvalue
     * @return bool
     */
    private function userValid($userID, $hashvalue)
    {
        try {
            $profile = $this->facebook->api('/me', 'GET');
        } catch (FacebookApiException $e) {
            $userID = NULL;
        }
        
        if ($this->sm->checkUserId($userID, $hashvalue)) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }
}

/* End of file show.php */
/* Location: ./application/controllers/show.php */