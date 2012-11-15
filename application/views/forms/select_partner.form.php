<?php if ($rel_status && !in_array($rel_status, $singles_array)): ?>
    Your significant other is: <?php echo $partner_name; ?> (<?php echo $partner_id; ?>)<br />
    Run the application for this friend or select from the list below:<br />
    <?php $hidden = array('in_relationship' => 1); ?>
<?php else: ?>
    Please choose a friend from the list below:<br />
    <?php $hidden = array('in_relationship' => 0); ?>
<?php endif; ?>

<!-- Choose partner form -->
<?php echo validation_errors(); ?>
<?php if ($this->session->flashdata('no_friend')): ?>
    <div style="color:red">
        You must choose a friend from the list
    </div>
<?php endif; ?>

<?php $hidden['default_partner'] = $partner_id . "_" . $partner_name; ?>
<?php echo form_open('/animation', array('id' => 'partner-selection'), $hidden); ?>
<?php 
    if (!empty($friends)) {
        $options = array('0' => 'Please select');
        
        foreach ($friends as $friend) {
            $options[$friend['uid']."_".$friend['name']] = $friend['name'];
        }
        unset($friend);
        echo form_dropdown("available_friends", $options);
    }
?>
<br />
<?php echo form_submit('submit', 'Go'); ?>

