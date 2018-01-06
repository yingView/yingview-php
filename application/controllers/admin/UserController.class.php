<?php
    class UserController extends Controller {

        // 查询 所有用户
        public static function queryUserByKeyWordAction(){
            $keyword = $_GET['keyword'];
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

            $mysql = new Mysql($GLOBALS['config']);
            $sql ="select * from users";
            $alltotal = count($mysql -> getAll($sql));
            if ($keyword === 'limit') {
                $sql .= " where userStatus = 0 or userStatus = 2 or userStatus = 3 ";
            } else if ($keyword === 'normal') {
                $sql .= " where userStatus = 1 and (userPower = 1 or userPower = 2 or userPower = 3) ";
            } else if ($keyword === 'vip') {
                $sql .= " where userStatus = 1 and (userPower = 4 or userPower = 5) ";
            } else if ($keyword === 'admin') {
                $sql .= " where userPower = 6 or userPower = 7 or userPower = 8 or userPower = 9 ";
            }
            $total = count($mysql -> getAll($sql));
            $sql.=" limit $current ,$size";
            $userList = $mysql -> getAll($sql);
            if ($userList) {
                $copy = [];
                foreach( $userList as $value) {
                    $value['userPhoto'] = FRONT_UPLOAD_PHOTO_PATH . $value['userPhoto'];
                    $copy[] = $value;
                }
                self :: setContent(
                    array('isSuccess' => true,
                    'message' => '查询成功',
                    'userList' => $copy,
                    'total' => $total,
                    'alltotal' => $alltotal
                    )
                );
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                    'message' => '查询失败'
                    )
                );
            }
            self :: send();
        }

        // 删除用户
        public static function deleteByUserCodeAction(){
            $userCode = $_GET['userCode'];
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "delete from users where userCode='$userCode'";
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
        }

        // 更新用户
        public static function updateUserInfoAction(){

            $userInfo = $_GET['userInfo'];
            $userInfo = str_replace("\\","",$userInfo);
            $userInfo = get_object_vars(json_decode($userInfo));
            $userCode = $userInfo['userCode'];

            $userName = $userInfo['userName'];
            $password = $userInfo['password'];
            $passCode = md5($password);
            $nickName = $userInfo['nickName'];
            $sax = $userInfo['sax'];
            $email = $userInfo['email'];
            $tel = $userInfo['tel'] ? $userInfo['tel'] : 'null';
            $bithday = $userInfo['bithday'] ? $userInfo['bithday'] : 'null';
            $userPhoto = $userInfo['userPhoto'];
            $userBanner = $userInfo['userBanner'];
            $userLevel = $userInfo['userLevel'];
            $userPower = $userInfo['userPower'];
            $userStatus = $userInfo['userStatus'];
            $userJob = $userInfo['userJob'];
            $sign = $userInfo['sign'];
            $description = $userInfo['description'];
            $experience = $userInfo['experience'];
            $city = $userInfo['city'];
            $activeCode = $userInfo['activeCode'];

            $mysql = new Mysql($GLOBALS['config']);
            $sql = "update users set ";
            $sql .= "userName='$userName', ";
            $sql .= "password='$password', ";
            $sql .= "passCode='$passCode', ";
            $sql .= "nickName='$nickName', ";
            $sql .= "sax=$sax, ";
            $sql .= "email='$email', ";
            $sql .= "tel=$tel, ";
            $sql .= "bithday=$bithday, ";
            $sql .= "userPhoto='$userPhoto', ";
            $sql .= "userBanner='$userBanner', ";
            $sql .= "userLevel=$userLevel, ";
            $sql .= "userPower=$userPower, ";
            $sql .= "userStatus=$userStatus, ";
            $sql .= "userJob='$userJob', ";
            $sql .= "sign='$sign', ";
            $sql .= "description='$description', ";
            $sql .= "experience='$experience', ";
            $sql .= "city='$city', ";
            $sql .= "activeCode='$activeCode' ";
            $sql .= "where userCode='$userCode'";
            if ($mysql -> query($sql)) {
                self :: setContent(
                    array('isSuccess' => true,
                    'message' => '操作成功'
                    )
                );
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                    'message' => '操作失败'
                    )
                );
            }
            self :: send(); 
        }
    }
?>