<?php
    class EmailController extends Controller {
        // 新建邮件
        public static function addEmailAction() {
            $emailId = 'null';
            $emailCode = $_GET['emailCode'];
            $sendUserCode = $_GET['sendUserCode'];
            $receiveUserCode = $_GET['receiveUserCode'];
            $eamilTitle = $_GET['eamilTitle'];
            $eamilContent = $_GET['eamilContent'];
            $emailStatus = $_GET['emailStatus'];
            
            if($emailStatus === null) {
                $emailStatus = 0;
            }
            $emailCreateDate = time();
            $mysql = new Mysql($GLOBALS['config']);
            $sql = null;
            if ($emailCode === null) {
                $emailCode = self::initCode();
                $sql = "insert into emails values (
                    $emailId,
                    '$emailCode',
                    '$sendUserCode',
                    '$receiveUserCode',
                    '$eamilTitle',
                    '$eamilContent',
                    $emailStatus,
                    '$emailCreateDate'
                )";
            } else {
                $sql = "update emails set 
                sendUserCode = '$sendUserCode',
                receiveUserCode = '$receiveUserCode',
                eamilTitle = '$eamilTitle',
                eamilContent = '$eamilContent',
                emailStatus = '$emailStatus',
                emailCreateDate = '$emailCreateDate'
                where emailCode='$emailCode'";
            }

            if ($mysql -> query($sql)) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '发送成功'
                    )
                );
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '发送失败'
                    )
                );
            }
            self :: send();
        }

        // 删除邮件
        public static function deleteEmailAction() {
            $emailCodes = $_GET['emailCodes'];
            if ($emailCodes) {
                $emailCodes = str_replace("\\","",$emailCodes);
                $emailCodes = json_decode($emailCodes);
                $mysql = new Mysql($GLOBALS['config']);
                $sql = "delete from emails where emailCode in (";
                foreach( $emailCodes as $emailCode) {
                    $sql .= "'$emailCode', ";
                }
                $sql .= "'')";
                if ($mysql -> query($sql)) {
                    self :: setContent(
                        array('isSuccess' => true,
                            'message' => '删除成功'
                        )
                    );
                } else {
                    self :: setContent(
                        array('isSuccess' => false,
                            'message' => '删除失败'
                        )
                    );
                }
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '参数为空'
                    )
                );
            }
            
            self :: send();
        }

        // 变为已读
        public static function readEmailAction() {
            $emailCode = $_GET['emailCode'];
            $emailStatus = $_GET['emailStatus'];
            if($emailCode === null) {
                return;
            }
            if ($emailStatus === null) {
                $emailStatus = 1;
            }
            $sql = "update emails set emailStatus = '$emailStatus' where emailCode='$emailCode'";
            $mysql = new Mysql($GLOBALS['config']);
            $mysql -> query($sql);
        }

        // 撤回邮件
        public static function withdrawEmailAction() {
            $emailCode = $_GET['emailCode'];
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "update emails set emailStatus=2 where emailCode='$emailCode' and emailStatus=0";
            if(count($mysql -> getAll("select * from  emails where emailCode='$emailCode' and emailStatus=0"))) {
                if ($mysql -> query($sql)) {
                    self :: setContent(
                        array('isSuccess' => true,
                            'message' => '撤回成功'
                        )
                    );
                } else {
                    self :: setContent(
                        array('isSuccess' => false,
                            'message' => '撤回失败'
                        )
                    );
                }
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '该邮件不能撤回'
                    )
                );
            }
            
            self :: send();
        }

        // 查询所有邮件
        public static function queryEmailAction() {
            $userCode = $_GET['userCode'];
            $type = $_GET['type'];
            $current = $_GET['current'];
            $size = $_GET['size'];
            $total = 0;
            if ($current === null) {
                $current = 1;
            }
            if ($size === null) {
                $size = 10;
            }
            $mysql = new Mysql($GLOBALS['config']);

            $sql = "select emails.*, users.* from emails left join users on emails.sendUserCode = users.userCode or emails.receiveUserCode = users.userCode where receiveUserCode='$userCode' or sendUserCode='$userCode'";
            
            if ($type === 'send') { // 查询我发送的
                $sql = "select emails.*, users.*, users.nickName from emails left join users on emails.receiveUserCode = users.userCode where sendUserCode='$userCode' and emailStatus!=2";
            }

            if ($type === 'receive') { // 查询我收到的
                $sql = "select emails.*, users.*, users.nickName from emails left join users on emails.sendUserCode = users.userCode where receiveUserCode='$userCode' and emailStatus!=2";
            }

            if ($type === 'draft') { // 查询我的草稿 我发送的
                $sql = "select emails.*, users.*, users.nickName from emails left join users on emails.receiveUserCode = users.userCode where sendUserCode='$userCode' and emailStatus=2";
            }
            
            $total = count($mysql -> getAll($sql));
            $current = ($current - 1) * $size;
            $sql.=" limit $current ,$size";
            $emailList = $mysql -> getAll($sql);

            if ($emailList) {
                $copy = array();
                foreach( $emailList as $emails ) {
                    $copy[] = array(
                        'userCode' => $emails['userCode'],
                        'city' => $emails['city'],
                        'nickName' => $emails['nickName'],
                        'sax' => $emails['sax'],
                        'userJob' => $emails['userJob'],
                        'userPhoto' => FRONT_UPLOAD_PHOTO_PATH . $emails['userPhoto'],
                        'userLevel' => $emails['userLevel'],
                        'eamilContent' => $emails['eamilContent'],
                        'eamilTitle' => $emails['eamilTitle'],
                        'emailCode' => $emails['emailCode'],
                        'emailStatus' => $emails['emailStatus'],
                        'emailCreateDate' => $emails['emailCreateDate']
                    );
                }
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'emailList' => $copy,
                        'total' => $total
                    )
                );
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '撤回失败'
                    )
                );
            }
            self :: send();
        }


    }
?>