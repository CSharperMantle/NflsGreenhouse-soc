<?php
  require 'assets\\php\\shared.php';

  try {
    $db = new PDO($dbdsn, $username, $passwd);

    $result = run_query($db, $FETCH_LATEST_SQL);
    $air_temp = $result['air_temp'];
    $air_hum = $result['air_hum'];
    $air_light = $result['air_light'];
    $ground_hum = $result['ground_hum'];

    $result = run_query($db, $FETCH_TOTAL_COMMITS_SQL);
    $total_commits = $result['count'];
    $result = run_query($db, $FETCH_TOTAL_ALERTS_SQL);
    $total_alerts = $result['count'];
    $result = run_query($db, $FETCH_TODAY_COMMITS_COUNT_SQL);
    $today_uploads = $result['count'];
    $result = run_query($db, $FETCH_TODAY_ALERTS_COUNT_SQL);
    $today_alerts = $result['count'];
    $result = run_query($db, $FETCH_LATEST_AIR_TEMP_ALERT_SQL);
    $air_temp_is_ok = $result['is_ok'];
    $result = run_query($db, $FETCH_LATEST_AIR_HUM_ALERT_SQL);
    $air_hum_is_ok = $result['is_ok'];
    $result = run_query($db, $FETCH_LATEST_AIR_LIGHT_ALERT_SQL);
    $air_light_is_ok = $result['is_ok'];
    $result = run_query($db, $FETCH_LATEST_GROUND_HUM_ALERT_SQL);
    $ground_hum_is_ok = $result['is_ok'];

    $all_commits = run_query_fetch_all($db, $FETCH_ALL_SQL);
    
    $now = new DateTime();

    $exists = isset($_SESSION['is_logged_in']);

  }
  catch (PDOException $e) {
    $air_temp = 'None';
    $air_hum = 'None';
    $air_light = 'None';
    $ground_hum = 'None';
    $total_commits = 0;
    $total_alerts = 0;
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
  <title>宁波外国语学校大棚管理系统 - 仪表盘</title>
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
  <link rel="stylesheet" href="assets/css/app.css" type="text/css" />
</head>

<body>
  <div class="be-wrapper be-fixed-sidebar">
    <nav class="navbar navbar-expand fixed-top be-top-header">
      <div class="container-fluid">
        <div class="be-navbar-header"><a href="index.php" class="navbar-brand"></a>
        </div>
        <div class="be-right-navbar">
          <ul class="nav navbar-nav float-right be-user-nav">
            <li class="nav-item dropdown"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><img
                  src="assets/img/avatar.png" alt="Avatar"><span class="user-name"><?= $exists ? $_SESSION['username'] : 'John Doe'; ?></span></a>
              <div role="menu" class="dropdown-menu">
                <!--online away busy-->
                <div class="user-info">
                  <?php
                      if ($exists) {
                        print_user_status($_SESSION['username'], 'online');
                      } else {
                        print_user_status('John Doe', 'offline');
                      }
                    ?>
                </div>
                <!-- <a href="pages-profile.html" class="dropdown-item">
                    <span class="icon mdi mdi-face"></span> Account
                  </a> -->
                <?php
                    if($exists && $_SESSION['is_logged_in'] == true) {
                      print_settings_button();
                      print_logoff_button();
                    } else {
                      print_login_button();
                    }
                ?>
              </div>
            </li>
          </ul>
          <div class="page-title"><span>仪表盘</span></div>
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
                        <a href="#history_data" class="connection-item"><img src="assets/img/backup.png" alt="Github"><span>历史数据</span></a>
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
      <div class="left-sidebar-wrapper"><a href="#" class="left-sidebar-toggle">仪表盘</a>
        <div class="left-sidebar-spacer">
          <div class="left-sidebar-scroll">
            <div class="left-sidebar-content">
              <ul class="sidebar-elements">
                <li class="divider">菜单</li>
                <li class="active"><a href="index.php"><i class="icon mdi mdi-home"></i><span>仪表盘</span></a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="progress-widget">
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
            print_alert(AlertInfo::DANGER, '使用了不支持的设置！', '您的浏览器不支持JavaScript。大部分功能将不能正常工作。');
          ?>
        </noscript>
        <?php
            if (isset($error_occur)) {
              print_alert(AlertInfo::DANGER, '错误！', '加载页面时出错。页面不会正常工作。');
              http_response_code(503);
              exit();
            }
            if (date('H') >= 18 && date('H') < 24) {
              print_alert(AlertInfo::INFO, '晚上好！', '目前是UTC时间晚上。');
            } 
            elseif (date('H') >= 0 && date('H') < 12) {
              print_alert(AlertInfo::INFO, '早上好！', '目前是UTC时间早晨。');
            }
            elseif (date('H') >= 12 && date('H') < 18) {
              print_alert(AlertInfo::INFO, '下午好！', '目前是UTC时间下午。');
            }
            switch ($air_temp_is_ok) {
              case AlertType::HIGH:
                print_alert(AlertInfo::WARNING, '警告!', '空气温度过高。');
                $is_all_fine = false;
                break;
              case AlertType::LOW:
                print_alert(AlertInfo::WARNING, '警告!', '空气温度过低。');
                $is_all_fine = false;
                break;
              default:
                break;
            }
            switch ($air_hum_is_ok) {
              case AlertType::HIGH:
                print_alert(AlertInfo::WARNING, '警告!', '空气湿度过高。');
                $is_all_fine = false;
                break;
              case AlertType::LOW:
                print_alert(AlertInfo::WARNING, '警告!', '空气湿度过低。');
                $is_all_fine = false;
                break;
              default:
                break;
            }
            switch ($air_light_is_ok) {
              case AlertType::HIGH:
                print_alert(AlertInfo::WARNING, '警告!', '光线过强。');
                $is_all_fine = false;
                break;
              case AlertType::LOW:
                print_alert(AlertInfo::WARNING, '警告!', '光线过弱。');
                $is_all_fine = false;
                break;
              default:
                break;
            }
            switch ($ground_hum_is_ok) {
              case AlertType::HIGH:
                print_alert(AlertInfo::WARNING, '警告!', ' 土壤过湿。');
                $is_all_fine = false;
                break;
              case AlertType::LOW:
                print_alert(AlertInfo::WARNING, '警告!', '土壤过干。');
                $is_all_fine = false;
                break;
              default:
                break;
            }
            if (!isset($is_all_fine)) {
              print_alert(AlertInfo::GOOD, '放心！', '一切正常。');
            }
          ?>
      </div>

      <div class="row">
        <div class="col-12 col-lg-6 col-xl-3">
          <div class="widget widget-tile">
            <div id="spark1" class="chart sparkline"></div>
            <div class="data-info">
              <div class="desc">上传总数</div>
              <div class="value"><span class="indicator indicator-equal mdi mdi-chevron-right"></span><span data-toggle="counter"
                  data-end="<?= $total_commits; ?>" class="number"><?= $total_commits; ?></span>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-6 col-xl-3">
          <div class="widget widget-tile">
            <div id="spark2" class="chart sparkline"></div>
            <div class="data-info">
              <div class="desc">报警总数</div>
              <div class="value"><span class="indicator indicator-equal mdi mdi-chevron-right"></span><span data-toggle="counter"
                  data-end="<?= $total_alerts; ?>" class="number"><?= $total_alerts; ?></span>
              </div>
            </div>
          </div>
        </div>
        <!--mdi-chevron-up/down/left/right indicator-negative/positive/equal -->
        <div class="col-12 col-lg-6 col-xl-3">
          <div class="widget widget-tile">
            <div id="spark3" class="chart sparkline"></div>
            <div class="data-info">
              <div class="desc">今日上传数</div>
              <div class="value"><span class="indicator indicator-equal mdi mdi-chevron-right"></span><span data-toggle="counter"
                  data-end="<?= $today_uploads; ?>" class="number"><?= $today_uploads; ?></span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-3">
          <div class="widget widget-tile">
            <div id="spark4" class="chart sparkline"></div>
            <div class="data-info">
              <div class="desc">今日报警数</div>
              <div class="value"><span class="indicator indicator-negative mdi mdi-chevron-right"></span><span
                  data-toggle="counter" data-end="<?= $today_alerts; ?>" class="number">0</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="widget widget-fullwidth be-loading">
            <div class="widget-head">

              <div class="tools">
                <div class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle"><span class="icon mdi mdi-more-vert d-inline-block d-md-none"></span></a>
                  <div role="menu" class="dropdown-menu"><a href="#" class="dropdown-item">周</a><a href="#" class="dropdown-item">月</a><a
                      href="#" class="dropdown-item">年</a>
                    <div class="dropdown-divider"></div><a href="#" class="dropdown-item">今天</a>
                  </div>
                </div><span class="icon mdi mdi-chevron-down"></span><span class="icon toggle-loading mdi mdi-refresh-sync"></span><span
                  class="icon mdi mdi-close"></span>
              </div>

              <div class="button-toolbar d-none d-md-block">
                <div class="btn-group">
                  <button type="button" class="btn btn-secondary">一周</button>
                  <button type="button" class="btn btn-secondary active">一月</button>
                  <button type="button" class="btn btn-secondary">一年</button>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn btn-secondary">今天</button>
                </div>
              </div>

              <span name="history_data" class="title">历史数据</span>
            </div>
            <div class="widget-chart-container">
              <div class="widget-chart-info">
                <ul class="chart-legend-horizontal">
                  <li><span data-color="main-chart-color1"></span> 空气湿度</li>
                  <li><span data-color="main-chart-color2"></span> 空气温度</li>
                  <!--<li><span data-color="main-chart-color3"></span> 空气亮度</li>
					            <li><span data-color="main-chart-color4"></span> 地面湿度</li>-->
                </ul>
              </div>
              <div class="widget-counter-group widget-counter-group-right">
                <div class="counter counter-big">
                  <div class="value">
                    <?= $air_temp . '°C'; ?>
                  </div>
                  <div class="desc">空气温度</div>
                </div>
                <div class="counter counter-big">
                  <div class="value">
                    <?= $air_hum . '%'; ?>
                  </div>
                  <div class="desc">空气湿度</div>
                </div>
                <div class="counter counter-big">
                  <div class="value">
                    <?= $air_light; ?>
                  </div>
                  <div class="desc">光强度</div>
                </div>
                <div class="counter counter-big">
                  <div class="value">
                    <?= $ground_hum; ?>
                  </div>
                  <div class="desc">地面湿度</div>
                </div>
              </div>
              <div id="main-chart" style="height: 260px;"></div>
            </div>
            <div class="be-spinner">
              <svg width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                <circle fill="none" stroke-width="4" stroke-linecap="round" cx="33" cy="33" r="30" class="circle"></circle>
              </svg>
            </div>
          </div>
        </div>
      </div>
      <div class="row">

        <div class="col-sm-12">
          <div class="card card-table">
            <div class="card-header">活动记录
              <div class="tools dropdown"><span class="icon mdi mdi-download"></span><a href="#" role="button"
                  data-toggle="dropdown" class="dropdown-toggle"><span class="icon mdi mdi-more-vert"></span></a>
                <div role="menu" class="dropdown-menu"><a href="#" class="dropdown-item">Action</a><a href="#" class="dropdown-item">Another
                    action</a><a href="#" class="dropdown-item">Something else here</a>
                  <div class="dropdown-divider"></div><a href="#" class="dropdown-item">Separated link</a>
                </div>
              </div><span class="card-subtitle">系统历史活动</span>
            </div>
            <div class="card-body">
              <table id="table2" class="table table-striped table-hover table-fw-widget">
                <thead>
                  <tr>
                    <th>时间</th>
                    <th>类型</th>
                    <th>空气温度 (°C)</th>
                    <th>空气湿度 (%)</th>
                    <th>地面湿度</th>
                    <th>光强度</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($all_commits as $key => $value) {
                      $_air_temp = $value['air_temp'];
                      $_air_light = $value['air_light'];
                      $_air_hum = $value['air_hum'];
                      $_ground_hum = $value['ground_hum'];
                      $_timestamp = $value['timestamp'];
                      $_oddity = (($key + 1) % 2) == 0 ? 'odd' : 'even';
                      print_all_commits_each_item($_timestamp, $_oddity, '收到数据', $_air_temp, $_air_hum, $_ground_hum, $_air_light);
                    } 
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card">
            <div class="card-header card-header-divider pb-3">种植时长</div>
            <div class="card-body pt-5">
              <div class="row user-progress user-progress-small">
                <div class="col-lg-5"><span class="title">牵牛花</span></div>
                <div class="col-lg-7">
                  <div class="progress">
                    <div style="width: 40%" class="progress-bar bg-success"></div>
                  </div>
                </div>
              </div>
              <div class="row user-progress user-progress-small">
                <div class="col-lg-5"><span class="title">玫瑰花</span></div>
                <div class="col-lg-7">
                  <div class="progress">
                    <div style="width: 65%" class="progress-bar bg-success"></div>
                  </div>
                </div>
              </div>
              <div class="row user-progress user-progress-small">
                <div class="col-lg-5"><span class="title">多肉植物</span></div>
                <div class="col-lg-7">
                  <div class="progress">
                    <div style="width: 30%" class="progress-bar bg-success"></div>
                  </div>
                </div>
              </div>
              <div class="row user-progress user-progress-small">
                <div class="col-lg-5"><span class="title">菊花</span></div>
                <div class="col-lg-7">
                  <div class="progress">
                    <div style="width: 80%" class="progress-bar bg-success"></div>
                  </div>
                </div>
              </div>
              <div class="row user-progress user-progress-small">
                <div class="col-lg-5"><span class="title">薄荷</span></div>
                <div class="col-lg-7">
                  <div class="progress">
                    <div style="width: 45%" class="progress-bar bg-success"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="row">
        <div class="col-12 col-lg-6">
          <div class="card">
            <div class="card-header">最新行为</div>
            <div class="card-body">
              <ul class="user-timeline user-timeline-compact">
                <li class="latest">
                  <div class="user-timeline-date">刚刚</div>
                  <div class="user-timeline-title">浇灌植物</div>
                  <div class="user-timeline-description">为大棚内 <b>玫瑰花#P02</b> 浇了 1.00 吨水</div>
                </li>
                <li>
                  <div class="user-timeline-date">今天 - 15:35</div>
                  <div class="user-timeline-title">打开大棚大门</div>
                  <div class="user-timeline-description">打开 <b>大门#D01</b> </div>
                </li>
                <li>
                  <div class="user-timeline-date">昨天 - 23:41</div>
                  <div class="user-timeline-title">打开大棚遮光罩</div>
                  <div class="user-timeline-description">打开 <b>遮光罩#S01</b> </div>
                </li>
                <li>
                  <div class="user-timeline-date">昨天 - 3:02</div>
                  <div class="user-timeline-title">浇灌植物</div>
                  <div class="user-timeline-description">为大棚内 <b>牵牛花#P01</b> 浇了 0.48 吨水</div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-6">
          <div class="widget be-loading">
            <div class="widget-head">
              <div class="tools"><span class="icon mdi mdi-chevron-down"></span><span class="icon mdi mdi-refresh-sync toggle-loading"></span><span
                  class="icon mdi mdi-close"></span></div>
              <div class="title">植物分布图</div>
            </div>
            <div class="widget-chart-container">
              <div class="widget-chart-info mb-4">
                <div class="indicator indicator-positive float-right"><span class="icon mdi mdi-chevron-up"></span><span
                    class="number">100%</span></div>
                <div class="counter counter-inline">
                  <div class="value">1872</div>
                  <div class="desc">植物数</div>
                </div>
              </div>
              <div id="map-widget" style="height: 265px;"></div>
            </div>
            <div class="be-spinner">
              <svg width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                <circle fill="none" stroke-width="4" stroke-linecap="round" cx="33" cy="33" r="30" class="circle"></circle>
              </svg>
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
                            </span><span class="message">Ready for the meeti...</span></div>
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
  <script src="assets/js/app.js" type="text/javascript"></script>
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
  <script src="assets/js/app-dashboard.js.php" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net/js/jquery.dataTables.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-bs4/js/dataTables.bootstrap4.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons/js/dataTables.buttons.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.html5.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.flash.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.print.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.colVis.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js" type="text/javascript"></script>
  <script src="assets/js/app-tables-datatables.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function () {
      //initialize the javascript
      App.init();
      App.dashboard();
      App.dataTables();
    });
  </script>
</body>

</html>