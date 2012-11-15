<h1>Welcome to Centre of My Universe</h1>
<p>
    <?php if ($user): ?>
        <?php include_once 'forms/select_partner.form.php'; ?>
        <br />
        <a href="<?php echo $logout_url; ?>">Log out</a>
    <?php else: ?>
        <h3>Please login using Facebook</h3>
        <fb:login-button autologoutlink="true" size="large" scope="<?php echo $fb_perms; ?>"></fb:login-button>
        <div id="fb-root"></div>
    <?php endif; ?>
</p>