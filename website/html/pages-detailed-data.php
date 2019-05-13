<?php
  /*
   * Copyright (c) 2018 Ningbo Foreign Language School
   * This part of program should be delivered with the whole project.
   * Partly use is not allowed.
   * Licensed under GPL-v3 Agreement
   */
  require 'assets\\php\\shared_sql.php';
  require 'assets\\php\\shared_db.php';
  require 'assets\\php\\shared_html.php';
  require 'assets\\php\\shared_xml.php';
  require 'assets\\php\\shared_const.php';

  try {
    $db = DBConnectionSingleton::getInstance();

    $result = run_query_fetch($db, FETCH_LATEST_SQL);
    $air_temp = $result['air_temp'];
    $air_hum = $result['air_hum'];
    $air_light = $result['air_light'];
    $ground_hum = $result['ground_hum'];
  }
  catch (Exception $e) {
    $air_temp = 'None';
    $air_hum = 'None';
    $air_light = 'None';
    $ground_hum = 'None';
    $error_occur = true;
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
  <title>宁波外国语学校大棚管理系统 - 详细数据</title>
  <link rel="stylesheet" type="text/css" href="assets/lib/perfect-scrollbar/css/perfect-scrollbar.min.css" />
  <link rel="stylesheet" type="text/css" href="assets/lib/material-design-icons/css/material-design-iconic-font.min.css" />
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link rel="stylesheet" type="text/css" href="assets/lib/datatables/datatables.net-bs4/css/dataTables.bootstrap4.css" />
  <link rel="stylesheet" type="text/css" href="assets/lib/jquery.vectormap/jquery-jvectormap-1.2.2.css" />
  <link rel="stylesheet" type="text/css" href="assets/lib/jqvmap/jqvmap.min.css" />
  <link rel="stylesheet" type="text/css" href="assets/lib/datetimepicker/css/bootstrap-datetimepicker.min.css" />
  <link rel="stylesheet" type="text/css" href="assets/css/app.min.css" />
</head>

<body>
  <div class="be-wrapper be-fixed-sidebar">
    <nav class="navbar navbar-expand fixed-top be-top-header">
      <div class="container-fluid">
        <div class="be-navbar-header"><a href="index.php" class="navbar-brand"></a>
        </div>
        <div class="be-right-navbar">
          <ul class="nav navbar-nav float-right be-user-nav">
            <li class="nav-item dropdown"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
              <img src="assets/img/avatar.png" alt="Avatar"><span class="user-name"><?php print_user_name(); ?></span></a>
              <div role="menu" class="dropdown-menu">
                <!--online away busy-->
                <div class="user-info">
                  <?php print_user_info(); ?>
                </div>
                <?php print_user_buttons(); ?>
              </div>
            </li>
          </ul>
          <div class="page-title"><span>详细数据</span></div>
          <ul class="nav navbar-nav float-right be-icons-nav">
            <li class="nav-item dropdown"><a href="#" role="button" aria-expanded="false" class="nav-link be-toggle-right-sidebar"><span
                  class="icon mdi mdi-settings"></span></a></li>
            <li class="nav-item dropdown"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><span
                  class="icon mdi mdi-notifications"></span><span class="indicator"></span></a>
              <ul class="dropdown-menu be-notifications">
                <li>
                  <div class="title">通知<span class="badge badge-pill">3</span></div>
                  <div class="list">
                    <div class="be-scroller">
                      <div class="content">
                        <ul>
                          <li class="notification notification-unread"><a href="#">
                              <div class="image"><img src="assets/img/avatar2.png" alt="Avatar"></div>
                              <div class="notification-info">
                                <div class="text"><span class="user-name">Jessica Caruso</span> accepted your
                                  invitation to join the team.</div><span class="date">2 min ago</span>
                              </div>
                            </a></li>
                          <li class="notification"><a href="#">
                              <div class="image"><img src="assets/img/avatar3.png" alt="Avatar"></div>
                              <div class="notification-info">
                                <div class="text"><span class="user-name">Joel King</span> is now following you</div><span
                                  class="date">2 days ago</span>
                              </div>
                            </a></li>
                          <li class="notification"><a href="#">
                              <div class="image"><img src="assets/img/avatar4.png" alt="Avatar"></div>
                              <div class="notification-info">
                                <div class="text"><span class="user-name">John Doe</span> is watching your main
                                  repository</div><span class="date">2 days ago</span>
                              </div>
                            </a></li>
                          <li class="notification"><a href="#">
                              <div class="image"><img src="assets/img/avatar5.png" alt="Avatar"></div>
                              <div class="notification-info">
                                <span class="text"><span class="user-name">Emily Carter</span> is now following you</span><span
                                  class="date">5 days ago</span>
                              </div>
                            </a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="footer"> <a href="#">查看全部</a></div>
                </li>
              </ul>
            </li>
            <li class="nav-item dropdown"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><span
                  class="icon mdi mdi-apps"></span></a>
              <ul class="dropdown-menu be-connections">
                <li>
                  <div class="list">
                    <div class="content">
                      <div class="row">
                        <a href="#history-data" class="connection-item"><img src="assets/img/backup.png" alt="历史数据"><span>历史数据</span></a>
                        <a href="#latest-activities" class="connection-item"><img src="assets/img/backup.png" alt="最新行为"><span>最新行为</span></a>
                      </div>
                    </div>
                  </div>
                  <div class="footer"> <a href="#">更多...</a></div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="be-left-sidebar">
      <div class="left-sidebar-wrapper"><a href="#" class="left-sidebar-toggle">详细数据</a>
        <div class="left-sidebar-spacer">
          <div class="left-sidebar-scroll">
            <div class="left-sidebar-content">
              <ul class="sidebar-elements">
                <li class="divider">菜单</li>
                <li class=""><a href="index.php"><i class="icon mdi mdi-home"></i><span>仪表盘</span></a>
                </li>
                <li class="active"><a href="pages-detailed-data.php"><i class="icon mdi mdi-chart-donut"></i><span>详细数据</span></a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="progress-widget">
          <div class="progress-data"><?= '页面更新时间: ' . date('H:m:s'); ?></div>
          <div class="progress-data"><span class="progress-value">50%</span><span class="name">完善程度</span></div>
          <div class="progress">
            <div style="width: 50%;" class="progress-bar progress-bar-primary"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="be-content">
      <div class="main-content container-fluid">
        <noscript>
          <?php
            print_alert(AlertInfo::DANGER, '使用了不支持的设置！', 'JavaScript加载失败。大部分功能将不能正常工作。');
          ?>
        </noscript>
      <?php
        if (isset($error_occur)) {
          print_alert(AlertInfo::DANGER, '错误！', '加载页面时出错。页面不会正常工作。');
          http_response_code(503);
          exit();
        }
      ?>
      <div class="row">
        <div id="air-temp-card-holder" class="col-lg-3"></div>
        <div id="air-hum-card-holder" class="col-lg-3"></div>
        <div id="air-light-card-holder" class="col-lg-3"></div>
        <div id="ground-hum-card-holder" class="col-lg-3"></div>
      </div>
      <div class="row">
        <div class="col-lg-6">
            <div class="card">
              <div class="card-header card-header-divider">
                <div class="tools">
                  <span class="icon mdi mdi-chevron-down"></span>
                  <span class="icon mdi mdi-refresh-sync"></span>
                  <span class="icon mdi mdi-close" onclick="javascript:fade_out($(this));"></span>
                </div>
                <span class="title">空气温度</span>
              </div>
              <div class="card-body">
                <canvas id="air-temp-line-chart-canvas"></canvas>
              </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
              <div class="card-header card-header-divider">
                <div class="tools">
                  <span class="icon mdi mdi-chevron-down"></span>
                  <span class="icon mdi mdi-refresh-sync"></span>
                  <span class="icon mdi mdi-close" onclick="javascript:fade_out($(this));"></span>
                </div>
                <span class="title">空气湿度</span>
              </div>
              <div class="card-body">
                <canvas id="air-hum-line-chart-canvas"></canvas>
              </div>
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
            <div class="card">
              <div class="card-header card-header-divider">
                <div class="tools">
                  <span class="icon mdi mdi-chevron-down"></span>
                  <span class="icon mdi mdi-refresh-sync"></span>
                  <span class="icon mdi mdi-close" onclick="javascript:fade_out($(this));"></span>
                </div>
                <span class="title">土壤湿度</span>
              </div>
              <div class="card-body">
                <canvas id="ground-hum-line-chart-canvas"></canvas>
              </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
              <div class="card-header card-header-divider">
                <div class="tools">
                  <span class="icon mdi mdi-chevron-down"></span>
                  <span class="icon mdi mdi-refresh-sync"></span>
                  <span class="icon mdi mdi-close" onclick="javascript:fade_out($(this));"></span>
                </div>
                <span class="title">光强度</span>
              </div>
              <div class="card-body">
                <canvas id="air-light-line-chart-canvas"></canvas>
              </div>
            </div>
        </div>
      </div>
      </div>
    </div>
  </div>
  <nav class="be-right-sidebar">
    <div class="sb-content">
      <div class="tab-navigation">
        <ul role="tablist" class="nav nav-tabs nav-justified">
          <li role="presentation" class="nav-item"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab"
              class="nav-link active">聊天</a></li>
          <li role="presentation" class="nav-item"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab"
              class="nav-link">Todo</a></li>
          <li role="presentation" class="nav-item"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab"
              class="nav-link">设置</a></li>
        </ul>
      </div>
      <div class="tab-panel">
        <div class="tab-content">
          <div id="tab1" role="tabpanel" class="tab-pane tab-chat active">
            <div class="chat-contacts">
              <div class="chat-sections">
                <div class="be-scroller">
                  <div class="content">
                    <h2>Recent</h2>
                    <div class="contact-list contact-list-recent">
                      <div class="user"><a href="#"><img src="assets/img/avatar1.png" alt="Avatar">
                          <div class="user-data"><span class="status away"></span><span class="name">Claire Sassu</span><span
                              class="message">Can you share the...</span></div>
                        </a></div>
                      <div class="user"><a href="#"><img src="assets/img/avatar2.png" alt="Avatar">
                          <div class="user-data"><span class="status"></span><span class="name">Maggie jackson</span><span
                              class="message">I confirmed the info.</span></div>
                        </a></div>
                      <div class="user"><a href="#"><img src="assets/img/avatar3.png" alt="Avatar">
                          <div class="user-data"><span class="status offline"></span><span class="name">Joel King
                            </span><span class="message">Ready for the meeting...</span></div>
                        </a></div>
                    </div>
                    <h2>Contacts</h2>
                    <div class="contact-list">
                      <div class="user"><a href="#"><img src="assets/img/avatar4.png" alt="Avatar">
                          <div class="user-data2"><span class="status"></span><span class="name">Mike Bolthort</span></div>
                        </a></div>
                      <div class="user"><a href="#"><img src="assets/img/avatar5.png" alt="Avatar">
                          <div class="user-data2"><span class="status"></span><span class="name">Maggie jackson</span></div>
                        </a></div>
                      <div class="user"><a href="#"><img src="assets/img/avatar6.png" alt="Avatar">
                          <div class="user-data2"><span class="status offline"></span><span class="name">Jhon
                              Voltemar</span></div>
                        </a></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bottom-input">
                <input type="text" placeholder="Search..." name="q"><span class="mdi mdi-search"></span>
              </div>
            </div>
            <div class="chat-window">
              <div class="title">
                <div class="user"><img src="assets/img/avatar2.png" alt="Avatar">
                  <h2>Maggie jackson</h2><span>Active 1h ago</span>
                </div><span class="icon return mdi mdi-chevron-left"></span>
              </div>
              <div class="chat-messages">
                <div class="be-scroller">
                  <div class="content">
                    <ul>
                      <li class="friend">
                        <div class="msg">Hello</div>
                      </li>
                      <li class="self">
                        <div class="msg">Hi, how are you?</div>
                      </li>
                      <li class="friend">
                        <div class="msg">Good, I'll need support with my pc</div>
                      </li>
                      <li class="self">
                        <div class="msg">Sure, just tell me what is going on with your computer?</div>
                      </li>
                      <li class="friend">
                        <div class="msg">I don't know it just turns off suddenly</div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="chat-input">
                <div class="input-wrapper"><span class="photo mdi mdi-camera"></span>
                  <input type="text" placeholder="Message..." name="q" autocomplete="off"><span class="send-msg mdi mdi-mail-send"></span>
                </div>
              </div>
            </div>
          </div>
          <div id="tab2" role="tabpanel" class="tab-pane tab-todo">
            <div class="todo-container">
              <div class="todo-wrapper">
                <div class="be-scroller">
                  <div class="todo-content"><span class="category-title">Today</span>
                    <ul class="todo-list">
                      <li>
                        <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                          <input type="checkbox" checked="" class="custom-control-input"><span class="custom-control-label">Initialize
                            the project</span>
                        </label>
                      </li>
                      <li>
                        <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                          <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Create
                            the main structure </span>
                        </label>
                      </li>
                      <li>
                        <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                          <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Updates
                            changes to GitHub </span>
                        </label>
                      </li>
                    </ul><span class="category-title">Tomorrow</span>
                    <ul class="todo-list">
                      <li>
                        <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                          <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Initialize
                            the project </span>
                        </label>
                      </li>
                      <li>
                        <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                          <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Create
                            the main structure </span>
                        </label>
                      </li>
                      <li>
                        <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                          <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Updates
                            changes to GitHub </span>
                        </label>
                      </li>
                      <li>
                        <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                          <input type="checkbox" class="custom-control-input"><span title="This task is too long to be displayed in a normal space!"
                            class="custom-control-label">This task is too long to be displayed in a normal space!
                          </span>
                        </label>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="bottom-input">
                <input type="text" placeholder="Create new task..." name="q"><span class="mdi mdi-plus"></span>
              </div>
            </div>
          </div>
          <div id="tab3" role="tabpanel" class="tab-pane tab-settings">
            <div class="settings-wrapper">
              <div class="be-scroller"><span class="category-title">General</span>
                <ul class="settings-list">
                  <li>
                    <div class="switch-button switch-button-sm">
                      <input type="checkbox" checked="" name="st1" id="st1"><span>
                        <label for="st1"></label></span>
                    </div><span class="name">Available</span>
                  </li>
                  <li>
                    <div class="switch-button switch-button-sm">
                      <input type="checkbox" checked="" name="st2" id="st2"><span>
                        <label for="st2"></label></span>
                    </div><span class="name">Enable notifications</span>
                  </li>
                  <li>
                    <div class="switch-button switch-button-sm">
                      <input type="checkbox" checked="" name="st3" id="st3"><span>
                        <label for="st3"></label></span>
                    </div><span class="name">Login with Facebook</span>
                  </li>
                </ul><span class="category-title">Notifications</span>
                <ul class="settings-list">
                  <li>
                    <div class="switch-button switch-button-sm">
                      <input type="checkbox" name="st4" id="st4"><span>
                        <label for="st4"></label></span>
                    </div><span class="name">Email notifications</span>
                  </li>
                  <li>
                    <div class="switch-button switch-button-sm">
                      <input type="checkbox" checked="" name="st5" id="st5"><span>
                        <label for="st5"></label></span>
                    </div><span class="name">Project updates</span>
                  </li>
                  <li>
                    <div class="switch-button switch-button-sm">
                      <input type="checkbox" checked="" name="st6" id="st6"><span>
                        <label for="st6"></label></span>
                    </div><span class="name">New comments</span>
                  </li>
                  <li>
                    <div class="switch-button switch-button-sm">
                      <input type="checkbox" name="st7" id="st7"><span>
                        <label for="st7"></label></span>
                    </div><span class="name">Chat messages</span>
                  </li>
                </ul><span class="category-title">Workflow</span>
                <ul class="settings-list">
                  <li>
                    <div class="switch-button switch-button-sm">
                      <input type="checkbox" name="st8" id="st8"><span>
                        <label for="st8"></label></span>
                    </div><span class="name">Deploy on commit</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
  </div>
  <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
  <script src="assets/js/app.min.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/jquery.flot.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/jquery.flot.pie.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/jquery.flot.time.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/jquery.flot.resize.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/plugins/jquery.flot.orderBars.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/plugins/curvedLines.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/plugins/jquery.flot.tooltip.js" type="text/javascript"></script>
  <script src="assets/lib/jquery.sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
  <script src="assets/lib/countup/countUp.min.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
  <script src="assets/lib/jqvmap/jquery.vmap.min.js" type="text/javascript"></script>
  <script src="assets/lib/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script>
  <script src="assets/lib/chartjs/Chart.min.js" type="text/javascript"></script>
  <script src="assets/js/app-detailed-data.js.php" type="text/javascript"></script>
  <script src="assets/js/app-detailed-data-ajax.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(() => {
      //initialize the javascript
      App.init();
      App.ChartJs();
      App.firstTimeLoad();
      App.ajaxApply();
    });
  </script>
  <script type="text/javascript">
    function fade_out(obj) {
      obj.parent().parent().parent().parent().fadeOut();
    }
  </script>
</body>

</html>