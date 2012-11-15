<?php echo heading('Timelines page', 1); ?>
<p>You: <?php echo $user; ?></p>
<p>
    Your partner: <?php echo $partner_name; ?> (<?php echo $partner_id; ?>)
</p>
<p>
    List of shared photos:<br />
    <?php echo ol($photos); ?>
</p>

<p>
    List of shared videos:<br />
    <?php echo ol($videos); ?> 
</p>

<p>
    List of posts:<br />
    <?php echo ol($posts); ?> 
</p>

<p>
    List of shared music:<br />
    <?php //echo ol($music); ?> 
    <pre>
        <?php print_r($music); ?>
    </pre>
</p>


<p>
    List of shared events:<br />
    <?php echo ol($events); ?> 
</p>

<p>
    List of shared likes:<br />
    <?php echo ol($likes); ?> 
</p>

<p>
    List of shared checkins (places):<br />
    <?php echo ol($checkins); ?> 
</p>

<p>
    <?php echo anchor('/', 'Go back', array('title' => 'Bo Back')); ?>
</p>

<p>
    <?php echo anchor($logout_url, 'Log out'); ?>
</p>

<?php if (isset($share_url)): ?>
<p>
    <?php echo anchor($share_url, 'Share URL'); ?>
</p>
<?php endif; ?>
