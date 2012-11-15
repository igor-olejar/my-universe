<p>You: <?php echo $viewer; ?></p>
<p>
    Your partner: <?php echo $originator; ?>
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

<?php if (isset($logout_url)): ?>
<p>
    <?php echo anchor($logout_url, 'Log out'); ?>
</p>
<?php endif; ?>