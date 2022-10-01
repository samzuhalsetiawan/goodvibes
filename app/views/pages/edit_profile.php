<?php 
    $page = "edit_profile";
    require_once APPROOT.'/views/inc/header.php';
?>

<div class="edit-profile-page-container">
    <h3 class="edit-profile-title">Edit Profile</h3>

    <div class="preview-foto-profile-container">
        <img class="profile-picture" src="<?= URLROOT . '/public/img/' . $data['profile_picture'] ?>" alt="foto-profile">
    </div>

    <form action="<?= URLROOT . '/users/edit_profile' ?>" method="post" enctype="multipart/form-data">
        <div class="input-group mb-3">
            <label class="input-group-text" for="inputGroupFile01">Foto Profile</label>
            <input name="profile_picture" type="file" class="form-control pilih-profile-picture" id="inputGroupFile02">
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">@</span>
            <input name="username" value="<?= $data["username"] ?>" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1" autocomplete="off">
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">Panggilan</span>
            <input name="nickname" value="<?= $data["nickname"] ?>" type="text" class="form-control" placeholder="Nama Panggilan" aria-label="Username" aria-describedby="basic-addon1" autocomplete="off">
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">Nama Lengkap</span>
            <input name="fullname" value="<?= $data["fullname"] ?>" type="text" class="form-control" placeholder="Nama Lengkap" aria-label="Username" aria-describedby="basic-addon1" autocomplete="off">
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">Email</span>
            <input name="email" value="<?= $data["email"] ?>" type="text" class="form-control" placeholder="Email" aria-label="Username" aria-describedby="basic-addon1" autocomplete="off">
        </div>
        <div class="button-control-container">
            <button>Kembali</button>
            <button>Ubah data akun saya</button>
        </div>
    </form>
</div>

<?php require_once APPROOT.'/views/inc/footer.php'; ?>