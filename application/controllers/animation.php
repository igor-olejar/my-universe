<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Animation controller. Verifies the form submission from the Welcome controller and shows the animation
 * 
 * @package cmu
 * @author Igor Olejar <igor.olejar@gmail.com>
 */


class Animation extends CI_Controller 
{
    function __construct()
    {
        parent::__construct();
        
        //check if user logged into facebook
        if (!$this->facebook->getUser()) {
            //redirect to start
            redirect('/');
        }
    }
	
    /**
     * Index method. Validates the form and displays the animation view
     * @return nothing
     */
    function index() 
    {
        //validation rules
        $rules = array(
            array(
                'field'     =>  'default_partner',
                'label'     =>  'Default Partner',
                'rules'     =>  'required'
            ),
            array(
                'field'     =>  'available_friends',
                'label'     =>  'Friends List',
                'rules'     =>  'required'
            )
        );
        
        $this->form_validation->set_rules($rules);
        
        if ($this->form_validation->run() !== FALSE) {
            /*
             * For people in a relationship: default_partner is not zero and available_friends can be zero or an ID. 
             * If it is an ID, it takes priority over the default_partner
             * 
             * For single people: default_partner is always zero and available_friends cannot be zero
             */
            
            //collect the data
            $in_relationship = $this->input->post('in_relationship');
            $default_partner = $this->input->post('default_partner');
            $selected_friend = $this->input->post('available_friends');
            
            if ($in_relationship) {
                if ($selected_friend != '0') {
                    $default_partner = $selected_friend; //person chose someone else other than the significan other
                }
            } else {
                if ($selected_friend != '0') {
                    $default_partner = $selected_friend;
                } else {
                    $this->session->set_flashdata('no_friend', TRUE);
                    redirect('/');
                }
            }            
            
            //disect the default partner
            $tmp = explode("_", $default_partner);
            $data = array(
                'chosen_partner_id'     =>  $tmp[0],
                'chosen_partner_name'   =>  $tmp[1]
            );
            unset($tmp);
            
            //also save in session
            $new_sess_data = array(
                'chosen_partner_id'     =>  $data['chosen_partner_id'],
                'chosen_partner_name'   =>  $data['chosen_partner_name']
            );
            $this->session->set_userdata($new_sess_data);
            unset($new_sess_data);
            
            $this->template->show('animation', $data);
        } else {
            $this->template->show('landing');
        }
    }
}

/* End of file animation.php */
/* Location: ./application/controllers/animation.php */