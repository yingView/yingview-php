<?php
    class EmailController extends Controller {
        // 新建邮件
        public static function addEmailAction() {
            $emailId = 'null';
            $emailCode = self::initCode();
            $sendUserCode = $_GET['sendUserCode'];
            $ReceiveUserCode = $_GET['ReceiveUserCode'];
            $eamilTitle = $_GET['eamilTitle'];
            $eamilContent = $_GET['eamilContent'];
            $emailStatus = $_GET['emailStatus'];
            if($emailStatus === null) {
                $emailStatus = 0;
            }
            $emailCreateDate = time();
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "insert into emails values (
                    $emailId,
                    '$emailCode',
                    '$sendUserCode',
                    '$ReceiveUserCode',
                    '$eamilTitle',
                    '$eamilContent',
                    $emailStatus,
                    '$emailCreateDate'
                )";
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
            $emailCode = $_GET['emailCode'];
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "delete from emails where emailCode='$emailCode'";
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
            self :: send();
        }

        // 撤回邮件
        public static function withdrawEmailAction() {
            $emailCode = $_GET['emailCode'];
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "update emails set emailStatus=2 where emailCode='$emailCode' and emailStatus=0";
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
            self :: send();
        }

        // 查询所有邮件
        public static function queryAllEmailAction() {
            $userCode = $_GET['userCode'];
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
            $sql = "select * from emails where sendUserCode='$userCode' or ReceiveUserCode='$userCode'";
            $total = count($mysql -> getAll($sql));
            $current = ($current - 1) * $size;
            $sql.=" limit $current ,$size";
            $emailList = $mysql -> getAll($sql);

            if ($emailList) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'emailList' => $emailList,
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
        // 查询已发送
        public static function queryAllSendAction() {
            $userCode = $_GET['userCode'];
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
            $sql = "select * from emails where sendUserCode='$userCode' and emailStatus!=2";
            $total = count($mysql -> getAll($sql));
            $current = ($current - 1) * $size;
            $sql.=" limit $current ,$size";
            $emailList = $mysql -> getAll($sql);

            if ($emailList) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'emailList' => $emailList,
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

        // 查询接收的邮件
        public static function queryAllReceiveAction() {
            $userCode = $_GET['userCode'];
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
            $sql = "select * from emails where ReceiveUserCode='$userCode' and emailStatus!=2";
            $total = count($mysql -> getAll($sql));
            $current = ($current - 1) * $size;
            $sql.=" limit $current ,$size";
            $emailList = $mysql -> getAll($sql);
            if ($emailList) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'emailList' => $emailList,
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

        // 查询草稿邮件
        public static function queryAllDraftAction() {
            $userCode = $_GET['userCode'];
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
            $sql = "select * from emails where sendUserCode='$userCode' and emailStatus=2";
            $total = count($mysql -> getAll($sql));
            $current = ($current - 1) * $size;
            $sql.=" limit $current ,$size";
            $emailList = $mysql -> getAll($sql);
            if ($emailList) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'emailList' => $emailList,
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