<?php
/** Class Welcome Model
 * Used by the Welcome controller to get list of user's friends from Facebook
 * 
 * @package cmu
 * @see Welcome
 * @author Igor Olejar <igor.olejar@gmail.com>
 */
class Welcome_model extends CI_Model
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    
    /**
     * Gets the list of friends for the user. The list is ordered by friend's last name
     * @param mixed $facebook
     * @return array
     */
    public function getFbFriends()
    {
        $out = array();
        
        $fql_query = array(
            'method'    =>  'fql.query',
            'query'     =>  'SELECT uid, name, first_name, last_name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) ORDER BY last_name'
        );
        
        try {
            $out = $this->facebook->api($fql_query);
        } catch (FacebookApiException $e) {
            show_error('Could not get the list of your friends from Facebook<br />'.$e);
        }
        
        return $out;
    }
    
    public function previousTimelineExists($user)
    {
        $this->db->select('timelines.hashvalue');
        $this->db->from('user');
        $this->db->join('timelines', 'timelines.user_id = user.id');
        $this->db->where('user.id', $user);
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() === 0) {
            return FALSE;
        } else {
            $row = $query->row();
            return $row->hashvalue;
        }
    }
}

/* End of file welcome_model.php */
/* Location: ./application/models/welcome_model.php */