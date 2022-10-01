<?php 
    $page = "register";
    require_once APPROOT.'/views/inc/header.php';
?>
<div class="row mt-3 justify-content-center">
    <div class="col-md-5">
        <form action="<?php echo URLROOT; ?>/users/register" method="post" class="register-form">
            <img class="register-brand-logo" src="<?= URLROOT . '/public/img/goodvibes2_crop_logo-removebg.png' ?>" alt="Brand">
            <p class="register-text-desc">Silahkan buat akun anda</p>
            <div class="input-fullname-container">
                <input type="text" class="input-fullname <?php echo (!empty($data['fullname_err'])) ? 'is-invalid' : ''; ?>" name="fullname" placeholder="Nama Lengkap..." required autocomplete="off">
                <span class="invalid-feedback"><?php echo $data['fullname_err']; ?></span>
            </div>
            <div class="input-username-container">
                <input type="text" class="input-username <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" name="username" placeholder="Nama Akun Anda..." required autocomplete="off">
                <span class="invalid-feedback"><?php echo $data['username_err']; ?></span>
            </div>
            <div class="input-password-container">
                <input type="password" class="input-password <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" name="password" placeholder="Buat Password..." required autocomplete="off">
                <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
            </div>
            <div class="input-confirm-password-container">
                <input type="password" class="input-confirm-password <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" name="confirm_password" placeholder="Ketik kembali password..." required autocomplete="off">
                <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
            </div>
            <p class="register-text-desc">Sudah punya akun? login <a href="<?php echo URLROOT; ?>/users/login">disini</a></p>
            <button type="submit" class="button-register">Daftar</button>
        </form>
    </div>
</div>

<?php require_once APPROOT.'/views/inc/footer.php'; ?>