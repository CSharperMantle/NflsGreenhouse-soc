<?php
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="宁波外国语学校大棚管理系统 - 设置编辑器">
    <meta name="author" content="Mantle Jonse(CSharperMantle) & Jones Ma(iRed_K)">
    <link rel="shortcut icon" href="assets/img/logo-fav.png">
    <title>宁波外国语学校大棚管理系统 - 设置编辑器</title>
    <link rel="stylesheet" type="text/css" href="assets/lib/perfect-scrollbar/css/perfect-scrollbar.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/material-design-icons/css/material-design-iconic-font.min.css"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="assets/lib/jquery.codemirror/lib/codemirror.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/jquery.codemirror/theme/monokai.css"/>
    <link rel="stylesheet" href="assets/css/app.css" type="text/css"/>
  </head>
  <body>
    <div class="be-wrapper be-aside be-fixed-sidebar no-border">
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
                  <div class="user-info"> <?php print_user_info(); ?> </div>
                  <?php print_user_buttons(); ?>
                </div>
              </li>
            </ul>
            <div class="page-title"><span>Code Editor</span></div>
            <ul class="nav navbar-nav float-right be-icons-nav">
              <li class="nav-item dropdown"><a href="#" role="button" aria-expanded="false" class="nav-link be-toggle-right-sidebar"><span class="icon mdi mdi-settings"></span></a></li>
              <li class="nav-item dropdown"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><span class="icon mdi mdi-notifications"></span><span class="indicator"></span></a>
                <ul class="dropdown-menu be-notifications">
                  <li>
                    <div class="title">Notifications<span class="badge badge-pill">3</span></div>
                    <div class="list">
                      <div class="be-scroller">
                        <div class="content">
                          <ul>
                            <li class="notification notification-unread"><a href="#">
                                <div class="image"><img src="assets/img/avatar2.png" alt="Avatar"></div>
                                <div class="notification-info">
                                  <div class="text"><span class="user-name">Jessica Caruso</span> accepted your invitation to join the team.</div><span class="date">2 min ago</span>
                                </div></a></li>
                            <li class="notification"><a href="#">
                                <div class="image"><img src="assets/img/avatar3.png" alt="Avatar"></div>
                                <div class="notification-info">
                                  <div class="text"><span class="user-name">Joel King</span> is now following you</div><span class="date">2 days ago</span>
                                </div></a></li>
                            <li class="notification"><a href="#">
                                <div class="image"><img src="assets/img/avatar4.png" alt="Avatar"></div>
                                <div class="notification-info">
                                  <div class="text"><span class="user-name">John Doe</span> is watching your main repository</div><span class="date">2 days ago</span>
                                </div></a></li>
                            <li class="notification"><a href="#">
                                <div class="image"><img src="assets/img/avatar5.png" alt="Avatar"></div>
                                <div class="notification-info"><span class="text"><span class="user-name">Emily Carter</span> is now following you</span><span class="date">5 days ago</span></div></a></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <div class="footer"> <a href="#">View all notifications</a></div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <div class="be-left-sidebar">
        <div class="left-sidebar-wrapper"><a href="#" class="left-sidebar-toggle">设置编辑器</a>
          <div class="left-sidebar-spacer">
            <div class="left-sidebar-scroll">
              <div class="left-sidebar-content">
                <ul class="sidebar-elements">
                  <li class="divider">菜单</li>
                  <li class=""><a href="index.php"><i class="icon mdi mdi-home"></i><span>仪表盘</span></a>
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
      <div class="be-content be-no-padding">
        <aside class="page-aside codeditor">
          <div class="be-scroller">
            <div class="aside-content">
              <div class="content">
                <div class="aside-header">
                  <button data-target=".aside-nav" data-toggle="collapse" type="button" class="navbar-toggle"><span class="icon mdi mdi-caret-down"></span></button><span class="title">Code Editor</span>
                  <p class="description">Dream code editor</p>
                </div>
              </div>
              <div class="aside-nav collapse">
                <ul class="nav">
                  <li class="active"><a href="#"><span class="unsaved"></span><i class="mdi mdi-file"></i> index.html</a></li>
                  <li><a href="#"><i class="mdi mdi-file"></i> style.css</a></li>
                  <li><a href="#"><i class="mdi mdi-file"></i> script.js</a></li>
                </ul><span class="title">Actions</span>
                <ul class="nav nav-pills nav-stacked">
                  <li><a href="#">Open File</a></li>
                  <li><a href="#">Save File</a></li>
                  <li><a href="#">Save As...</a></li>
                </ul>
                <div class="aside-compose"><a href="#" class="btn btn-lg btn-primary btn-block">New File</a></div>
              </div>
            </div>
          </div>
        </aside>
        <div class="main-content container-fluid">
          <div class="content">
            <div class="code-editor">
              <div id="console"></div>
            </div>
          </div>
        </div>
      </div>
      <nav class="be-right-sidebar">
        <div class="sb-content">
          <div class="tab-navigation">
            <ul role="tablist" class="nav nav-tabs nav-justified">
              <li role="presentation" class="nav-item"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab" class="nav-link active">Chat</a></li>
              <li role="presentation" class="nav-item"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab" class="nav-link">Todo</a></li>
              <li role="presentation" class="nav-item"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab" class="nav-link">Settings</a></li>
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
                              <div class="user-data"><span class="status away"></span><span class="name">Claire Sassu</span><span class="message">Can you share the...</span></div></a></div>
                          <div class="user"><a href="#"><img src="assets/img/avatar2.png" alt="Avatar">
                              <div class="user-data"><span class="status"></span><span class="name">Maggie jackson</span><span class="message">I confirmed the info.</span></div></a></div>
                          <div class="user"><a href="#"><img src="assets/img/avatar3.png" alt="Avatar">
                              <div class="user-data"><span class="status offline"></span><span class="name">Joel King		</span><span class="message">Ready for the meeti...</span></div></a></div>
                        </div>
                        <h2>Contacts</h2>
                        <div class="contact-list">
                          <div class="user"><a href="#"><img src="assets/img/avatar4.png" alt="Avatar">
                              <div class="user-data2"><span class="status"></span><span class="name">Mike Bolthort</span></div></a></div>
                          <div class="user"><a href="#"><img src="assets/img/avatar5.png" alt="Avatar">
                              <div class="user-data2"><span class="status"></span><span class="name">Maggie jackson</span></div></a></div>
                          <div class="user"><a href="#"><img src="assets/img/avatar6.png" alt="Avatar">
                              <div class="user-data2"><span class="status offline"></span><span class="name">Jhon Voltemar</span></div></a></div>
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
                              <input type="checkbox" checked="" class="custom-control-input"><span class="custom-control-label">Initialize the project</span>
                            </label>
                          </li>
                          <li>
                            <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                              <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Create the main structure							</span>
                            </label>
                          </li>
                          <li>
                            <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                              <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Updates changes to GitHub							</span>
                            </label>
                          </li>
                        </ul><span class="category-title">Tomorrow</span>
                        <ul class="todo-list">
                          <li>
                            <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                              <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Initialize the project							</span>
                            </label>
                          </li>
                          <li>
                            <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                              <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Create the main structure							</span>
                            </label>
                          </li>
                          <li>
                            <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                              <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Updates changes to GitHub							</span>
                            </label>
                          </li>
                          <li>
                            <label class="custom-checkbox custom-control custom-control-sm"><span class="delete mdi mdi-delete"></span>
                              <input type="checkbox" class="custom-control-input"><span title="This task is too long to be displayed in a normal space!" class="custom-control-label">This task is too long to be displayed in a normal space!							</span>
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
    <script src="assets/lib/jquery.codemirror/lib/codemirror.js" type="text/javascript"></script>
    <script src="assets/lib/jquery.codemirror/mode/xml/xml.js" type="text/javascript"></script>
    <script src="assets/lib/jquery.codemirror/mode/css/css.js" type="text/javascript"></script>
    <script src="assets/lib/jquery.codemirror/mode/htmlmixed/htmlmixed.js" type="text/javascript"></script>
    <script src="assets/lib/jquery.codemirror/addon/edit/matchbrackets.js" type="text/javascript"></script>
    <script src="assets/js/app-code-editor.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        //initialize the javascript
        App.init();
      
        App.codeEditor();
        
      });
      
    </script>
    <div id="code1" style="display: none;">
      &#x3C;!DOCTYPE html&#x3E;
      &#x3C;html lang=&#x22;en&#x22;&#x3E;
      &#x3C;head&#x3E;
      &#x3C;meta charset=&#x22;utf-8&#x22;&#x3E;
      &#x3C;meta name=&#x22;viewport&#x22; content=&#x22;width=device-width, initial-scale=1, shrink-to-fit=no&#x22;&#x3E;
      &#x3C;link rel=&#x22;icon&#x22; href=&#x22;../../favicon.ico&#x22;&#x3E;
      &#x3C;!-- Bootstrap core CSS --&#x3E;
      &#x3C;link href=&#x22;../../dist/css/bootstrap.min.css&#x22; rel=&#x22;stylesheet&#x22;&#x3E;
      &#x3C;!-- Custom styles for this template --&#x3E;
      &#x3C;link href=&#x22;starter-template.css&#x22; rel=&#x22;stylesheet&#x22;&#x3E;
      &#x3C;/head&#x3E;
      &#x3C;body&#x3E;
      &#x3C;nav class=&#x22;navbar navbar-toggleable-md navbar-inverse bg-inverse fixed-top&#x22;&#x3E;
      &#x3C;a class=&#x22;navbar-brand&#x22; href=&#x22;#&#x22;&#x3E;Navbar&#x3C;/a&#x3E;
      &#x3C;/nav&#x3E;
      &#x3C;div class=&#x22;container&#x22;&#x3E;
      &#x9;...Example content...
      &#x3C;/div&#x3E;&#x3C;!-- /.container --&#x3E;
      &#x3C;!-- Bootstrap core JavaScript================================================== --&#x3E;
      &#x3C;!-- Placed at the end of the document so the pages load faster --&#x3E;
      &#x3C;script&#x3E;window.jQuery || document.write(&#x27;&#x3C;script src=&#x22;../../assets/js/vendor/jquery.min.js&#x22;&#x3E;&#x3C;\/script&#x3E;&#x27;)&#x3C;/script&#x3E;
      &#x3C;script src=&#x22;../../dist/js/bootstrap.min.js&#x22;&#x3E;&#x3C;/script&#x3E;
      &#x3C;/body&#x3E;
      &#x3C;/html&#x3E;
    </div>
  </body>
</html>