<?php 
    $page = "comment";
    require_once APPROOT.'/views/inc/header.php';
?>

<div class="comment-page-container">
    <div class="post-container">
        <div class="profile-picture-container">
            <img src="<?= URLROOT . '/public/img/' . $data['owner']->profile_picture ?>" alt="profile-picture">
            <input type="text" value="@<?= $data['owner']->username ?>" disabled>
        </div>
        <div class="content-container">
            <?php if (isset($data['owner']->media) && $data['owner']->media != "") : ?>
                <img class="post-picture" src="<?= URLROOT . '/public/img/' . $data['owner']->media ?>" alt="post-image">
            <?php endif ?>
            <?php if (isset($data['owner']->caption) && $data['owner']->caption != "") : ?>
                <textarea disabled class="caption" rows="3" name="caption"><?= $data['owner']->caption ?></textarea>
            <?php endif ?>
        </div>
        <div class="button-container">
            <button class="button-suka">Suka</button>
        </div>
    </div>
    <form class="comment-card" action="<?= URLROOT . '/users/comment' ?>" method="post" class="buat-comment-container">
        <img src="<?= URLROOT . '/public/img/' . $data['user']['profile_picture'] ?>" alt="profile-picture">
        <div class="username-dan-komen">
            <input type="hidden" name="post_id" value="<?= $data['owner']->post_id ?>">
            <h5 class="username">@<?= $data['user']['username'] ?></h5>
            <textarea class="comment" name="comment" rows="3" placeholder="Ketik komentar..."></textarea>
            <button type="submit" class="button-kirim">Kirim</button>
        </div>
    </form>
    <div class="comment-container">
        <?php if (empty($data["comments"])) : ?>
            <h3 class="belum-ada-komen">Belum ada komentar</h3>
        <?php else : ?>
            <?php for ($i = 0; $i < count($data['comments']); $i++) : ?>
                <div class="comment-card">
                    <img src="<?= URLROOT . '/public/img/' . $data['comments'][$i]->profile_picture ?>" alt="profile-picture">
                    <div class="username-dan-komen">
                        <h5 class="username">@<?= $data['comments'][$i]->username ?></h5>
                        <textarea disabled class="comment" rows="3"><?= $data['comments'][$i]->comment ?></textarea>
                    </div>
                </div>
            <?php endfor ?>
        <?php endif ?>
    </div>
</div>

<?php require_once APPROOT.'/views/inc/footer.php'; ?>