<?php 
    $page = "login";
    require_once APPROOT.'/views/inc/header.php';
?>
<div class="row mt-3 justify-content-center">
    <?php flash('register_success'); ?>
    <div class="col-md-5">
        <form action="<?php echo URLROOT; ?>/users/login" method="post" class="login-form">
            <img class="login-brand-logo" src="<?= URLROOT . '/public/img/goodvibes2_crop_logo-removebg.png' ?>" alt="Brand">
            <p class="login-text-desc">Silahkan Login dahulu</p>
            <div class="input-username-container">
                <input type="text" class="input-username <?php echo(!empty($data['username_err']))? 'is-invalid':''; ?>" name="username" placeholder="Username..." value="<?php echo $data['username'];?>" required autocomplete="off">
                <span class="invalid-feedback"><?php echo $data['username_err']; ?></span>
            </div>
            <div class="input-password-container">
                <input type="password" class="input-password <?php echo(!empty($data['password_err'])) ? 'is-invalid':''; ?>" name="password" placeholder="Password..." required autocomplete="off">
                <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
            </div>
            <p class="login-text-desc">Belum memiliki akun? daftar <a href="<?php echo URLROOT; ?>/users/register">disini</a></p>
            <button type="submit" class="button-login">Login</button>
        </form>
    </div>
</div>

<?php require_once APPROOT.'/views/inc/footer.php'; ?>