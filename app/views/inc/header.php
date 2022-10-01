<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Global CSS -->
    <link rel="stylesheet" href="<?= URLROOT . '/public/css/global.css' ?>">

    <?php if ($page == "dashboard") : ?>
      <link rel="stylesheet" href="<?= URLROOT . '/public/css/dashboard.css' ?>">
    <?php elseif ($page == "login") : ?>
      <link rel="stylesheet" href="<?= URLROOT . '/public/css/login.css' ?>">
    <?php elseif ($page == "register") : ?>
      <link rel="stylesheet" href="<?= URLROOT . '/public/css/register.css' ?>">
    <?php elseif ($page == "account") : ?>
      <link rel="stylesheet" href="<?= URLROOT . '/public/css/account.css' ?>">
    <?php elseif ($page == "comment") : ?>
      <link rel="stylesheet" href="<?= URLROOT . '/public/css/comment.css' ?>">
    <?php elseif ($page == "edit_profile") : ?>
      <link rel="stylesheet" href="<?= URLROOT . '/public/css/edit_profile.css' ?>">
    <?php endif ?>
    <title><?php echo SITENAME; ?></title>
  </head>
  <body>
    <?php include "navbar.php";?>
    <div class="container">
        