<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Initial_schema extends CI_Migration
{
    public function up()
    {
        /***************************************************************/
        /* USER TABLE
         * 
         */
        $fields = array(
            'id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'user_fb_id varchar(255) COLLATE utf8_unicode_ci NOT NULL',
            'user_name varchar(255) COLLATE utf8_unicode_ci NOT NULL',
            'created_on timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP'
        );
        
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('user');
        
        /***************************************************************/
        /* TIMELINES TABLE
         * 
         */
        $fields = array(
          'id int(10) unsigned NOT NULL AUTO_INCREMENT',
          'hashvalue varchar(40) COLLATE utf8_unicode_ci NOT NULL',
          'user_id int(10) unsigned NOT NULL COMMENT "Not the FB ID"',
          'partner_fb_id varchar(255) COLLATE utf8_unicode_ci NOT NULL',
          'partner_name varchar(255) COLLATE utf8_unicode_ci NOT NULL',
          'photos mediumtext COLLATE utf8_unicode_ci',
          'videos mediumtext COLLATE utf8_unicode_ci',
          'posts mediumtext COLLATE utf8_unicode_ci',
          'music mediumtext COLLATE utf8_unicode_ci',
          'events mediumtext COLLATE utf8_unicode_ci',
          'likes mediumtext COLLATE utf8_unicode_ci',
          'checkins mediumtext COLLATE utf8_unicode_ci',
          'created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        );
        
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('hashvalue');
        $this->dbforge->add_key('user_id');
        $this->dbforge->create_table('timelines');
        
    }
    
    public function down()
    {
        $this->dbforge->drop_table('user');
        $this->dbforge->drop_table('timelines');
    }
}

/* End of file 001_initial_schema.php */
/* Location: ./application/migrations/001_initial_schema.php */