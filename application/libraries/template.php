<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Simple templating class that automatically includes header and footer
 * 
 * @package cmu
 * @author Igor Olejar <igor.olejar@gmail.com>
 */

class Template extends CI_Loader
{
    function show($view, $data = array())
    {
   
        $this->view('includes/header');
        $this->view($view, $data);
        $this->view('includes/footer');
    }
}

/* End of file template.php */
/* Location: ./application/libraries/template.php */
