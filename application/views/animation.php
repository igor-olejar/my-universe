<h1>Animation page</h1>
<p>
    Data will be analyzed for your partner:<br />
    <?php echo $chosen_partner_name; ?> (<?php echo $chosen_partner_id; ?>)
</p>
<p>
    Animation... will redirect to /timelines controller<br />
    <?php echo anchor('/timelines', 'Timelines'); ?>
</p>
<p>
    <?php echo anchor('/', 'Go back', array('title' => 'Bo Back')); ?>
</p>