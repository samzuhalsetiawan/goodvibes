<?php 
    $page = "account";
    require_once APPROOT.'/views/inc/header.php';
    $posts = $data['posts'];
?>
<div class="account-container">
    <div class="account-header">
        <div class="account-profile-picture">
            <img src="<?= URLROOT . '/public/img/' . $data['profile_picture'] ?>" alt="profile-picture">
        </div>
        <div class="desc-container">
            <h3 class="nama-akun">@<?= $data['username'] ?></h3>
            <div class="follower-counter">
                <div class="follower-container">
                    <h5>Pengikut</h5>
                    <h5><?= $data['followers'] ?></h5>
                </div>
                <div class="following-container">
                    <h5>Mengikuti</h5>
                    <h5><?= $data['following'] ?></h5>
                </div>
            </div>
            <?php if ($data['uid'] != $_SESSION['user_id']) : ?>
                <?php if (isset($data['is_following']) && $data['is_following'] == false) : ?>
                    <a href="<?= URLROOT . '/' . 'users/follow/' . $data['uid'] ?>" class="button-follow">+ Ikuti</a>
                <?php else : ?>
                    <a href="<?= URLROOT . '/' . 'users/disfollow/' . $data['uid'] ?>" class="button-follow">Batal Mengikuti</a>
                <?php endif ?>
            <?php endif ?>
        </div>
        <?php if ($data['uid'] == $_SESSION['user_id']) : ?>
            <a class="button-edit-profile" href="<?= URLROOT . '/pages/edit_profile' ?>">Edit Profile</a>
        <?php endif ?>
    </div>
    <div class="account-posts">
        <?php for ($i = 0; $i < count($posts); $i++) : ?>
            <div class="post-container">
                <div class="profile-picture-container">
                    <img src="<?= URLROOT . '/public/img/' . $data['profile_picture'] ?>" alt="profile-picture">
                    <input name="uid" type="hidden" value="<?= $posts[$i]->owner ?>">
                    <input type="text" value="@<?= $data['username'] ?>" disabled>
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
</div>

<?php require_once APPROOT.'/views/inc/footer.php'; ?>