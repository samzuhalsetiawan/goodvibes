<?php 
    $page = "dashboard";
    require_once APPROOT.'/views/inc/header.php'; 
    $posts = $data['posts'];
?>
<div class="search-bar">
    <input data-uid="<?= $_SESSION['user_id'] ?>" type="text" name="search" id="search" placeholder='Cari Goodvibers lainnya ...' />
    <svg width="45" height="40" viewBox="0 0 45 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
        d="M31.0388 16.7427C31.0388 24.6068 24.779 30.911 17.1438 30.911C9.50856 30.911 3.24874 24.6068 3.24874 16.7427C3.24874 8.8787 9.50856 2.57446 17.1438 2.57446C24.779 2.57446 31.0388 8.8787 31.0388 16.7427Z"
        stroke="#BFC6D0" stroke-width="5" />
        <path d="M30.1822 27.642L41.9125 37.0072" stroke="#BFC6D0" stroke-width="5" stroke-linecap="round" />
    </svg>
</div>
<div class="search-result-container hidden"></div>
<div class="posts-container">
    <?php flash('posting_success') ?>
    <form action="<?= URLROOT . '/pages/posting' ?>" method="post" class="buat-post-container" enctype="multipart/form-data">
        <div class="profile-picture-container">
            <img src="<?= URLROOT . '/public/img/' . $data['profile_picture'] ?>" alt="profile-picture">
            <input name="uid" type="hidden" value="<?= $_SESSION['user_id'] ?>">
            <input type="text" value="@<?= $data['username'] ?>" disabled>
        </div>
        <div class="content-container">
            <textarea class="caption <?php echo (!empty($data['caption_err']) || !empty($data['image_err'])) ? 'is-invalid' : ''; ?>" rows="3" name="caption" placeholder="Ketik sesuatu..."><?= $data['caption'] ?? "" ?></textarea>
            <span class="invalid-feedback"><?php echo ($data['fullname_err'] ?? "") . " " . ($data['image_err'] ?? "") ; ?></span>
        </div>
        <div class="button-container">
            <label class="input-file">
                <input name="media" type="file" accept="image/*"/>
                Tambahkan Foto
            </label>
            <button type="submit" class="button-posting">Posting</button>
        </div>
    </form>
    <?php for ($i = 0; $i < count($posts); $i++) : ?>
        <div class="post-container">
            <div class="profile-picture-container">
                <img src="<?= URLROOT . '/public/img/' . $posts[$i]->profile_picture ?>" alt="profile-picture">
                <input name="uid" type="hidden" value="<?= $posts[$i]->owner ?>">
                <input type="text" value="@<?= $posts[$i]->username ?>" disabled>
            </div>
            <div class="content-container">
                <?php if (isset($posts[$i]->media) && $posts[$i]->media != "") : ?>
                    <img class="post-picture" src="<?= URLROOT . '/public/img/' . $posts[$i]->media ?>" alt="post-image">
                <?php endif ?>
                <?php if (isset($posts[$i]->caption) && $posts[$i]->caption != "") : ?>
                    <textarea class="caption" rows="3" name="caption"><?= $posts[$i]->caption ?></textarea>
                <?php endif ?>
            </div>
            <div class="button-container">
                <a href="<?= URLROOT . '/pages/comment/' . $posts[$i]->post_id ?>" class="button-komen">Komentar</a>
                <button class="button-suka">Suka</button>
            </div>
        </div>
    <?php endfor ?>
</div>

<?php require_once APPROOT.'/views/inc/footer.php'; ?>