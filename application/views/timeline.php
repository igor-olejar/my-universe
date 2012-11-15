<h1>Welcome to Centre of My Universe</h1>
<p>
    <?php if ($user): ?>
        <?php include_once 'includes/timeline_details.php'; ?>
    <?php else: ?>
        <h3>Please login using Facebook</h3>
        <fb:login-button autologoutlink="true" size="large" ></fb:login-button>
        <div id="fb-root"></div>
    <?php endif; ?>
</p>