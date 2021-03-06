<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

page_header('Home');

?>

<style>
    .mt-1_52rem{margin-top:1.52rem}
    .collection .collection-item.avatar{min-height:0;}
    .p-0{padding:0 !important;}
    .mb-0 {margin-bottom: 0 !important;}
</style>
<div class="row">
    <h4>Authorized Apps</h4>
    <?= applications_list($_SESSION['id']); ?>
</div>

<?php if ($_SESSION['developer']) { ?>
<div class="row">
    <div class="row mb-0">
        <div class="col s12 m9 p-0">
            <h4>Your clients</h4>
        </div>
        <div class="col s12 m3 p-0">
            <a href="/client/add" class="btn waves-effect blue accent-4 mt-1_52rem right">Create client</a>
        </div>
    </div>
    <?= clients_list($_SESSION['id']); ?>
</div>
<?php } ?>

<?php if (!$_SESSION['developer']) { ?>
<div class="row">
    <?php $user = sql_select('users', 'username,email', "id='{$_SESSION['id']}'", true); ?>
    <a href="mailto:ltcastelnuovo@gmail.com?subject=Request Developer Account || LTCAuth&body=Username: <?= $user['username'] ?>%0AEmail: <?= $user['email'] ?>%0A%0AWants developer account." class="btn waves-effect blue accent-4">Request developer account</a>
</div>
<?php } ?>

<?php if ($_SESSION['admin']) { ?>
<div class="row">
    <h4>Admin Panel</h4>
    <div class="row">
        <a href="/admin/users" class="col s12 m5 btn waves-effect blue accent-4">Users</a>
        <div class="col m2"></div>
        <a href="/admin/clients" class="col s12 m5 btn waves-effect blue accent-4">Clients</a>
    </div>
    <div class="row">
        <a href="/admin/logs" class="col s12 btn waves-effect blue accent-4">Logs</a>
    </div>
</div>
<?php } ?>

<?= page_footer(); ?>
