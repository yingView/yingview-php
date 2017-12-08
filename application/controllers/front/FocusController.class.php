<?php
    class FocusController extends Controller {
        // 添加关注
        public static function addFocusAction(){
            $byFocusUserCode = $_GET['byFocusUserCode'];
            $focusUserCode = $_GET['focusUserCode'];
            if ($byFocusUserCode === $focusUserCode) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '你不能关注自己'
                    )
                );
            } else {
                $mysql = new Mysql($GLOBALS['config']);
                $sql = "select * from userFocus where byFocusUserCode='$byFocusUserCode' and focusUserCode='$focusUserCode'";
                if (!$mysql -> getAll($sql)) {
                    $createDate = time();
                    $followId = 'null';
                    $followCode = self::initCode();
                    $sql = "insert into userFocus values (
                        $followId,
                        '$followCode',
                        '$byFocusUserCode',
                        '$focusUserCode',
                        $createDate
                    )";
                    if ($mysql -> query($sql)) {
                        self :: setContent(
                            array('isSuccess' => true,
                            'message' => '关注成功'
                            )
                        );
                    } else {
                        self :: setContent(
                            array('isSuccess' => true,
                            'message' => '关注失败'
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
        // 取消关注
        public static function deleteFocusAction(){
            $byFocusUserCode = $_GET['byFocusUserCode'];
            $focusUserCode = $_GET['focusUserCode'];
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "select * from userFocus where byFocusUserCode='$byFocusUserCode' and focusUserCode='$focusUserCode'";
            if ($mysql -> getAll($sql)) {
                $sql = "delete from userFocus where byFocusUserCode='$byFocusUserCode' and focusUserCode='$focusUserCode'";
                if ($mysql -> query($sql)) {
                    self :: setContent(
                        array('isSuccess' => true,
                        'message' => '取消成功'
                        )
                    );
                } else {
                    self :: setContent(
                        array('isSuccess' => true,
                        'message' => '取消失败'
                        )
                    );
                }
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                    'message' => '你没有关注该用户'
                    )
                );
            }
            self :: send();
        }

        // 查询 关注user的人 user为被关注人
        public static function queryFocusByUserCodeAction(){
            $userCode = $_GET['userCode'];
            $current = $_GET['current'];
            $size = $_GET['size'];
            $total = 0;

            if ($current === null) {
                $current = 1;
            }

            $current = ($current - 1) * $size;
            if ($size === null) {
                $size = 10;
            }

            if (!$userCode) {
                return;
            }

            $mysql = new Mysql($GLOBALS['config']);
            $sql ="select userFocus.*, users.* from userFocus left join users on userFocus.focusUserCode = users.userCode where userFocus.byFocusUserCode='$userCode'";
            $total = count($mysql -> getAll($sql));
            $sql.=" limit $current ,$size";
            $focusList = $mysql -> getAll($sql);
            if ($focusList) {
                $userList = array();
                foreach( $focusList as $value) {
                    $userList[] = array(
                        'userCode' => $value['userCode'],
                        'userName' => $value['userName'],
                        'nickName' => $value['nickName'],
                        'sax' => $value['sax'],
                        'email' => $value['email'],
                        'userPhoto' => FRONT_UPLOAD_PHOTO_PATH . $value['userPhoto'],
                        'userJob' => $value['userJob'],
                        'city' => $value['city']
                    );
                }
                self :: setContent(
                    array('isSuccess' => true,
                    'message' => '查询成功',
                    'focusList' => $userList,
                    'total' => $total
                    )
                );
            } else {
                self :: setContent(
                    array('isSuccess' => true,
                    'message' => '查询失败'
                    )
                );
            }
            self :: send();
        }

        // 查询 被user关注的人
        public static function queryByFocusByUserCodeAction(){
            $userCode = $_GET['userCode'];
            $current = $_GET['current'];
            $size = $_GET['size'];
            $total = 0;

            if ($current === null) {
                $current = 1;
            }

            $current = ($current - 1) * $size;
            if ($size === null) {
                $size = 10;
            }

            if (!$userCode) {
                return;
            }

            $mysql = new Mysql($GLOBALS['config']);
            $sql ="select userFocus.*, users.* from userFocus left join users on userFocus.byFocusUserCode = users.userCode where userFocus.focusUserCode='$userCode'";
            $total = count($mysql -> getAll($sql));
            $sql.=" limit $current ,$size";
            $focusList = $mysql -> getAll($sql);
            if ($focusList) {
                $userList = array();
                foreach( $focusList as $value) {
                    $userList[] = array(
                        'userCode' => $value['userCode'],
                        'userName' => $value['userName'],
                        'nickName' => $value['nickName'],
                        'sax' => $value['sax'],
                        'email' => $value['email'],
                        'userPhoto' => FRONT_UPLOAD_PHOTO_PATH . $value['userPhoto'],
                        'userJob' => $value['userJob'],
                        'city' => $value['city']
                    );
                }
                self :: setContent(
                    array('isSuccess' => true,
                    'message' => '查询成功',
                    'byFocusList' => $userList,
                    'total' => $total
                    )
                );
            } else {
                self :: setContent(
                    array('isSuccess' => true,
                    'message' => '查询失败'
                    )
                );
            }
            self :: send();
        }
    }
?>