<?php
/** Class Show Model
 * Used by the Show controller to get the timelines for the given hashvalue
 * 
 * @package cmu
 * @see Show
 * @author Igor Olejar <igor.olejar@gmail.com>
 */
class Show_model extends CI_Model
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function getData($hashvalue)
    {
        $this->db->select('*');
        $this->db->from('timelines');
        $this->db->join('user', 'timelines.user_id = user.id');
        $this->db->where('hashvalue', $hashvalue);
        $this->db->limit(1);
        
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $row = $q->row();
            
            $out = array(
                'photos'        => unserialize($row->photos),
                'videos'        => unserialize($row->videos),
                'posts'         => unserialize($row->posts),
                'music'         => unserialize($row->music),
                'events'        => unserialize($row->events),
                'likes'         => unserialize($row->likes),
                'checkins'      => unserialize($row->checkins),
                'viewer'        => $row->partner_name,
                'originator'    => $row->user_name
            );
            
            return $out;
        } else {
            return FALSE;
        }
    }
    
    public function checkUserId($userID, $hashvalue)
    {
        $out = FALSE;
        
        $this->db->select('timelines.partner_fb_id, user.user_fb_id');
        $this->db->from('timelines');
        $this->db->join('user', 'timelines.user_id = user.id');
        $this->db->where('hashvalue', $hashvalue);
        $this->db->limit(1);
        
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $row = $q->row();
            
            if ($row->partner_fb_id == $userID || $row->user_fb_id == $userID) {
                $out = TRUE;
            }
        } 
        
        return $out;
    }
}

/* End of file show_model.php */
/* Location: ./application/models/show_model.php */