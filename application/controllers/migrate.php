<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Creates the 'universe' database tables
 *
 * @author Igor Olejar <igor.olejar@gmail.com>
 */
class Migrate extends CI_Controller 
{
    public function index()
    {
        $this->load->library('migration');
        
        if ( ! $this->migration->current()) {
          show_error($this->migration->error_string());
        } else {
            echo 'Done';
        }
    }
}

/* End of file timelines.php */
/* Location: ./application/controllers/timelines.php */
