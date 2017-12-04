<?php
    class UserController extends Controller {
        // 注册用户名
        public static function registAction(){

            $userInfo = get_object_vars(json_decode($_GET['content']));
            $userInfo['userId'] = 'null';
            $userInfo['userCode'] = self::initCode();
            $userInfo['passCode'] = md5($userInfo['password']);
            $userInfo['tel'] = 'null';
            $userInfo['bithday'] = 'null';
            $userInfo['photoImage'] = 'default_photo.jpg';
            $userInfo['userLevel'] = 1;
            $userInfo['userPower'] = 1;
            $userInfo['userStatus'] = 0;
            $userInfo['jobDesc'] = 'null';
            $userInfo['activeCode'] = self::initCode();
            $userInfo['userCreateTime'] = time();
            $sql = <<<heredoc
            insert into users values(
                $userInfo[userId],
                '$userInfo[userCode]',
                '$userInfo[userName]',
                '$userInfo[password]',
                '$userInfo[passCode]',
                '$userInfo[nickName]',
                $userInfo[sax],
                '$userInfo[email]',
                $userInfo[tel],
                $userInfo[bithday],
                '$userInfo[photoImage]',
                $userInfo[userLevel],
                $userInfo[userPower],
                $userInfo[userStatus],
                '$userInfo[userJob]',
                '$userInfo[jobDesc]',
                '$userInfo[activeCode]',
                $userInfo[userCreateTime]
            );
heredoc;

            if (!$userInfo['userName'] || !$userInfo['password']) {
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
                                'nickName' => $userInfo['nickName'],
                                'userName' => $userInfo['userName'],
                                'passCode' => $userInfo['passCode'],
                                'photoImage' => FRONT_UPLOAD_PHOTO_PATH . $userInfo['photoImage'],
                                'userCode' => $userInfo['userCode'],
                                'userJob' => $userInfo['userJob']
                            )
                        )
                    );
                    // 设置session
                    $_SESSION['userInfo'] = $userInfo;
                    // 发送邮件
                    $to = $userInfo['email'];
                    $activeAddr = "{$GLOBALS[localhost]}?rpcname=user&method=active&userName=$userInfo[userName]&activeCode=$userInfo[activeCode]";
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
            $userName = $_GET['userName'];
            $activeCode = $_GET['activeCode'];
            if (!$userName || !$activeCode) {
                echo '激活失败！';
                return;
            }
            $mysql = new Mysql($GLOBALS['config']);
            if ( $mysql -> getRow("select * from users where userName='$userName' and userStatus=0 and activeCode='$activeCode'")) {
                $sql = "update users set activeCode=null,userStatus=1 where userName='$userName' and userStatus=0 and activeCode='$activeCode'";
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
            $userName = $_GET['userName'];
            $password = $_GET['password'];
            $captcha = $_GET['captcha'];
            if (strtolower($_SESSION['captcha']) != strtolower($captcha) && $_SESSION['captchaIdx'] >= 3) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '验证码不正确'
                    )
                );
            } else if (!$userName || !$password) {
                $captchaIdx = 1;
                if ($_SESSION['captchaIdx']) {
                    $captchaIdx = $_SESSION['captchaIdx'] + 1;
                }
                $_SESSION['captchaIdx'] = $captchaIdx;
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '请填写用户名或密码'
                    )
                );
            } else {
                $mysql = new Mysql($GLOBALS['config']);
                $sql = "select * from users where userName='$userName' and password='$password'";
                $userInfo = $mysql -> getRow($sql);
                if ( $userInfo ) {
                    $_SESSION['userInfo'] = $userInfo;
                    self :: setContent(
                        array('isSuccess' => true,
                            'message' => '登录成功',
                            'user' => array(
                                'nickName' => $userInfo['nickName'],
                                'userName' => $userInfo['userName'],
                                'passCode' => $userInfo['passCode'],
                                'photoImage' => FRONT_UPLOAD_PHOTO_PATH . $userInfo['photoImage'],
                                'userCode' => $userInfo['userCode'],
                                'userJob' => $userInfo['userJob']
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
            if (isset($_SESSION['userInfo'])) {
                unset($_SESSION['userInfo']);
                unset($_SESSION['captcha']);
            }
        }

        // 关注
        public static function followAction(){
            $followUserCode = $_GET['followUserCode'];
            $visitorCode = $_GET['visitorCode'];
            if ($followUserCode === $visitorCode) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '你不能关注自己'
                    )
                );
            } else {
                $mysql = new Mysql($GLOBALS['config']);
                $sql = "select * from userFollow where followUserCode='$followUserCode' and visitorCode='$visitorCode'";
                if (!$mysql -> getAll($sql)) {
                    $createDate = time();
                    $followId = 'null';
                    $followCode = self::initCode();
                    $sql = "insert into userFollow values (
                        $followId,
                        '$followCode',
                        '$followUserCode',
                        '$visitorCode',
                        $createDate
                    )";
                    if ($mysql -> query($sql)) {
                        self :: setContent(
                            array('isSuccess' => true,
                            'message' => '操作成功'
                            )
                        );
                    } else {
                        self :: setContent(
                            array('isSuccess' => true,
                            'message' => '操作失败'
                            )
                        );
                    }
                } else {
                    self :: setContent(
                        array('isSuccess' => false,
                        'message' => '你已经关注过该用户'
                        )
                    );
                }
            }
            self :: send();
        }
    }
?>