<?php
  /*
   * Copyright (c) 2018 Ningbo Foreign Language School
   * This part of program should be delivered with the whole project.
   * Partly use is not allowed.
   * Licensed under GPL-v3 Agreement
   */
  require 'assets\\php\\shared_db.php';
  require 'assets\\php\\shared_sql.php';
  require 'assets\\php\\shared_html.php';
  require 'assets\\php\\shared_util.php';

  $user_not_registered = false;
  $user_password_wrong = false;
  if (array_key_exists('username', $_POST) && array_key_exists('password', $_POST)) {
    if ($_POST['username'] != NULL && $_POST['password'] != NULL) {
      $_username = purify_data($_POST['username']);
      $_passwd = purify_data($_POST['password']);

      $db = new PDO(dbdsn, username, passwd);

      $result = run_query_fetch($db, FETCH_USER_EXIST_SQL, array($_username));
      if ($result == NULL) {
        $user_not_registered = true;
      }
      if ($result['password'] == $_passwd) {
        set_session_logged_in($result['username']);
        redirect_to('index.php');
      } else {
        $user_password_wrong = true;
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="zh-cmn-Hans">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="宁波外国语学校大棚管理系统">
    <meta name="author" content="Mantle Jonse(CSharperMantle) & Jones Ma(iRed_K)">
    <link rel="shortcut icon" href="assets/img/logo-fav.png">
    <title>宁波外国语学校大棚管理系统 - 登录</title>
    <link rel="stylesheet" type="text/css" href="assets/lib/perfect-scrollbar/css/perfect-scrollbar.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/material-design-icons/css/material-design-iconic-font.min.css"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="assets/css/app.css" type="text/css"/>
  </head>
  <body class="be-splash-screen">
    <div class="be-wrapper be-login">
      <div class="be-content">
        <div class="main-content container-fluid">
          <div class="splash-container">
            <div class="card card-border-color card-border-color-primary">
              <div class="card-header"><img src="assets/img/logo.png" alt="logo" class="logo-img"><span class="splash-description">
                <?php 
                  if($user_not_registered) print USER_NOT_REGISTERED_MESSAGE; 
                  elseif($user_password_wrong) print USER_PASSWORD_WRONG_MESSAGE; 
                  else print "<div>请输入登录信息</div>"; 
                ?></span></div>
              <div class="card-body">
                <form action="pages-login.php" method="POST">
                  <div class="form-group">
                    <input id="username" type="text" required="" placeholder="用户名" autocomplete="off" class="form-control" name="username" />
                  </div>
                  <div class="form-group">
                    <input id="password" type="password" required="" placeholder="密码" class="form-control" name="password" />
                  </div>
                  <div class="form-group login-submit">
                    <button data-dismiss="modal" type="submit" class="btn btn-primary btn-xl">登录</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
    <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="assets/js/app.js" type="text/javascript"></script>
    <script src="assets/js/app-pages-login.js" type="text/javascript"></script>
  </body>
</html>