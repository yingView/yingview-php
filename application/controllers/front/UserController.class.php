<?php
    class UserController extends Controller {
        // 注册用户名
        public static function registAction(){

            $userinfo = get_object_vars(json_decode($_GET['content']));
            $userinfo['userid'] = 'null';
            $userinfo['usercode'] = self::initCode();
            $userinfo['passcode'] = md5($userinfo['password']);
            $userinfo['tel'] = 'null';
            $userinfo['bithday'] = 'null';
            $userinfo['photoimage'] = $GLOBALS['localhost'] . '/public/uploads/default_photo.jpg';
            $userinfo['userlevel'] = 1;
            $userinfo['userpower'] = 1;
            $userinfo['userstatus'] = 0;
            $userinfo['jobdesc'] = 'null';
            $userinfo['activecode'] = self::initCode();
            $userinfo['usercreatetime'] = time();
            $sql = <<<heredoc
            insert into users values(
                $userinfo[userid],
                '$userinfo[usercode]',
                '$userinfo[username]',
                '$userinfo[password]',
                '$userinfo[passcode]',
                '$userinfo[nickname]',
                $userinfo[sax],
                '$userinfo[email]',
                $userinfo[tel],
                $userinfo[bithday],
                '$userinfo[photoimage]',
                $userinfo[userlevel],
                $userinfo[userpower],
                $userinfo[userstatus],
                '$userinfo[userjob]',
                '$userinfo[jobdesc]',
                '$userinfo[activecode]',
                $userinfo[usercreatetime]
            );
heredoc;

            if (!$userinfo['username'] || !$userinfo['password']) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '用户名或密码为空'
                    )
                );
            } else {
                $mysql = new Mysql($GLOBALS['config']);
                if ($mysql -> query($sql)) {
                    self :: setContent(
                        array('isSuccess' => true,
                            'message' => '注册成功',
                            'user' => array(
                                'nickname' => $userinfo['nickname'],
                                'username' => $userinfo['username'],
                                'passcode' => $userinfo['passcode'],
                                'photoimage' => $userinfo['photoimage'],
                                'usercode' => $userinfo['usercode'],
                                'userjob' => $userinfo['userjob']
                            )
                        )
                    );
                    // 设置session
                    $_SESSION['userinfo'] = $userinfo;
                    // 发送邮件
                    $to = $userinfo['email'];
                    $activeAddr = "{$GLOBALS[localhost]}?rpcname=user&method=active&username=$userinfo[username]&activecode=$userinfo[activecode]";
                    $body = <<<MailContent
                    <table height='100%' width='100%' cellpadding='0' cellspacing='0' border='0'>
                    <tbody>
                        <tr>
                            <td valign='top' align='center' class='devicewidth' style='background-color:#ffffff;'>
                                <table border='0' cellpadding='0' cellspacing='0' style='border:none;border-collapse:collapse;mso-table-lspace:0;mso-table-rspace:0;' width='600' class='devicewidth' align='center'>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <table border='0' cellpadding='0' cellspacing='0' style='border:none;border-collapse:collapse;mso-table-lspace:0;mso-table-rspace:0;' width='600' class='devicewidth' align='center'>
                                                    <tbody>
                                                        <tr>
                                                            <td align='left' style=' color:#2dbe60; font-family: helvetica, arial, sans-serif; font-size:30px; font-weight:100; padding:0 0 10px 0;' class='pl25 pr25'>
                                                                欢迎使用鹰视觉
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table border='0' cellpadding='0' cellspacing='0' style='border:none;border-collapse:collapse;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;overflow-wrap:break-word;word-wrap:break-word;' width='600' class='devicewidth'>
                                                    <tbody>
                                                        <tr>
                                                            <td style='color:#484848; font-family: helvetica, arial, sans-serif; font-size:18px; line-height:26px; font-weight:100; padding:0 30px 35px 0;' align='left' class='pr25 pl25 f18 l26'>
                                                                使用印象笔记，体验高效工作；在所有设备上安装印象笔记，享受完美生活。工作无拘无束，随时随地高效。在任意设备上打开这封邮件，点击下载印象笔记，即可安装。
                                                                <br>
                                                                <br>
                                                                小贴示：你知道吗？印象笔记还能帮你永久保存微信消息哦！关注「我的印象笔记」公众号，转发消息或文章给它，就可以永久保存，随时找到啦！
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table border='0' cellpadding='0' cellspacing='0' style='border:none;border-collapse:collapse;mso-table-lspace:0;mso-table-rspace:0;' width='600' class='devicewidth'>
                                                    <tbody>
                                                        <tr>
                                                            <td style='padding:0 0 30px 0;' class='pl25 pr25' align='left'>
                                                                <table border='0' cellpadding='0' cellspacing='0' style='border:none;border-collapse:collapse;mso-table-lspace:0;mso-table-rspace:0;' width='600' class='devicewidth'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td align='left' class='full'>
                                                                                <div>
                                                                                    <a href='$activeAddr' style='background-color:#2dbe60;border:1px solid #2dbe60;border-radius:4px;color:#ffffff;display:inline-block;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:bold;line-height:35px;text-align:center;text-decoration:none;-webkit-text-size-adjust:none;mso-hide:all;letter-spacing:.5px;min-width:150px;' class='button' target='_blank'>
                                                                                        点此激活
                                                                                    </a>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                </table>
MailContent;
                    $subjet = '鹰视觉激活邮件';
                    $mailer = new Mail($GLOBALS['mailconfig']);
                    $mailer -> sendMail($to,$subjet,$body);
                } else {
                    self :: setContent(
                        array('isSuccess' => false,
                            'message' => '用户名已经存在'
                        )
                    );
                };
            }
            self :: send();
        }

        // 激活用户
        public static function activeAction(){
            $username = $_GET['username'];
            $activecode = $_GET['activecode'];
            if (!$username || !$activecode) {
                echo '激活失败！';
                return;
            }
            $mysql = new Mysql($GLOBALS['config']);
            if ( $mysql -> getRow("select * from users where username='$username' and userstatus=0 and activecode='$activecode'")) {
                $sql = "update users set activecode=null,userstatus=1 where username='$username' and userstatus=0 and activecode='$activecode'";
                if ($mysql -> query($sql)) {
                    echo '激活成功';
                } else {
                    echo '激活失败！';
                }
            } else {
                echo '激活失败！';
            }    
        }
        // 用户登录
        public static function loginAction(){
            $username = $_GET['username'];
            $password = $_GET['password'];
            if (!$username || !$password) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '请填写用户名或密码'
                    )
                );
            } else {
                $mysql = new Mysql($GLOBALS['config']);
                $sql = "select * from users where username='$username' and password='$password'";
                $userinfo = $mysql -> getRow($sql);
                if ( $userinfo ) {
                    self :: setContent(
                        array('isSuccess' => true,
                            'message' => '登录成功',
                            'user' => array(
                                'nickname' => $userinfo['nickname'],
                                'username' => $userinfo['username'],
                                'passcode' => $userinfo['passcode'],
                                'photoimage' => $userinfo['photoimage'],
                                'usercode' => $userinfo['usercode'],
                                'userjob' => $userinfo['userjob']
                            )
                        )
                    );
                } else {
                    self :: setContent(
                        array('isSuccess' => false,
                            'message' => '用户名不存在或密码错误'
                        )
                    );
                }
            }
            self :: send();
        }
        // 用户退出
        public static function logoutAction(){
            if (isset($_SESSION['userinfo'])) {
                session_destroy();
            }
        }
    }
?>