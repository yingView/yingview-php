<?php
    class ArticalController extends Controller  {

        public static function editAction() {
            if (!$_GET['content']) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '数据为空'
                    )
                );
                self :: send();
                return;
            }
            $operate = $_GET['operate'];
            $content = $_GET['content'];
            $content = str_replace("\\","",$content);
            $articalInfo = get_object_vars(json_decode($content));
            $articalId = 'null';
            $articalCode = $articalInfo['articalCode'];
            $articalTitle = $articalInfo['articalTitle'];
            $userCode = $articalInfo['userCode'];
            $categoryCode = $articalInfo['categoryCode'] ? $articalInfo['categoryCode'] : 'null';
            $articalContent = htmlspecialchars($articalInfo['articalContent']);
            $articalPhoto = $articalInfo['articalPhoto'];
            $articalImages = $articalInfo['articalImages'] ? $articalInfo['articalImages'] : 'null';
            $articalCreateDate = $operate === 'submit' ? time() : 0;
            $articalType = $articalInfo['articalType'];
            $articalView = 0;
            $articalMark = 0;
            $articalCommentNum = 0;
            $articalStatus = $operate === 'submit' ? 1 : 0;
            $bookCode = $articalInfo['bookCode'] ? $articalInfo['bookCode'] : 0;
            $sql = "";
            $mysql = new Mysql($GLOBALS['config']);
            if (!$articalCode) {
                $articalCode = self::initCode();
                $sql = "insert into articals values (
                    $articalId,
                    '$articalCode',
                    '$articalTitle',
                    '$userCode',
                    $categoryCode,
                    '$articalContent',
                    '$articalPhoto',
                    '$articalImages',
                    $articalCreateDate,
                    $articalType,
                    $articalView,
                    $articalMark,
                    $articalCommentNum,
                    $articalStatus,
                    '$bookCode'
                )";
            } else if ($articalCode) {
                $articalCode = addslashes($articalCode);
                $time = $mysql -> getRow("select * from articals where articalCode='$articalCode'");
                $time = $time['articalCreateDate'];
                $articalCreateDate = $time ? $time : time();
                $sql = "update articals set 
                    articalTitle='$articalTitle',
                    categoryCode=$categoryCode,
                    articalContent='$articalContent',
                    articalPhoto='$articalPhoto',
                    articalImages='$articalImages',
                    articalCreateDate=$articalCreateDate,
                    articalType=$articalType,
                    articalStatus=$articalStatus,
                    bookCode='$bookCode'
                where 
                articalCode='$articalCode'";
            }
            if ($mysql -> query($sql)) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                        'articalCode' => $articalCode
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
        
        // 查询所有 通过 type new hot
        public static function articalQueryAction() {
            if ($_GET['size'] === 'null') {
                $_GET['size'] = 40;
            };
            if ($_GET['current'] === 'null') {
                $_GET['current'] = 1;
            };
            if ($_GET['needType'] !== 'null') {
                self :: setContent(
                    array(
                        'isSuccess' => true,
                        'message' => '操作成功',
                        'retValue' => self :: QueryAction()
                    )
                );
            } else {
                $_GET['size'] = 8;
                $articalList = array();
                $_GET['needType'] = 'new';
                $articalList['new'] = self :: QueryAction();
                $_GET['needType'] = 'hot';
                $articalList['hot'] = self :: QueryAction();
                $_GET['needType'] = 'great';
                $articalList['great'] = self :: QueryAction();
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                        'retValue' => $articalList
                    )
                );
            }
            self :: send();
        }

        // 查询list 通过 needType
        public static function queryAction() {
            $needType = $_GET['needType'];
            $current = $_GET['current'];
            $size = $_GET['size'];
            $time = time() - 10368000;
            $total = 0;
            $sql = "select articals.*, users.* from articals left join users on articals.userCode = users.userCode 
            where ";
            $mysql = new Mysql($GLOBALS['config']);
            if ($needType === 'new') {
                $sql.="articalStatus = 1 order by articalCreateDate desc ";
                $total = count($mysql -> getAll($sql));
            } else if ($needType === 'all') {
                $sql.="articalStatus = 1 and articalCreateDate > $time order by (articalView + articalMark + articalCommentNum) desc ";
                $total = count($mysql -> getAll($sql));
            } else if ($needType === 'great') {
                $sql.="articalStatus = 2 and articalCreateDate > $time order by articalCreateDate desc ";
                $total = count($mysql -> getAll($sql));
            } else if ($needType === 'hot') {
                $sql.="articalStatus = 1 and articalCreateDate > $time order by articalView desc ";
                $total = count($mysql -> getAll($sql));
            }

            $current = ($current - 1) * $size;
            $sql.="limit $current ,$size";
            $articalArray = $mysql -> getAll($sql);

            $articalList = array();
            foreach($articalArray as $artical) {
                $articalList[] = array(
                    'articalCode' => $artical['articalCode'],
                    'articalTitle' => $artical['articalTitle'],
                    'categoryCode' => $artical['categoryCode'],
                    // 'articalContent' => $artical['articalContent'],
                    'articalPhoto' => FRONT_UPLOAD_COVER_PATH . $artical['articalPhoto'],
                    // 'articalImages' => $artical['articalImages'],
                    'articalCreateDate' => $artical['articalCreateDate'],
                    'articalType' => $artical['articalType'],
                    'articalView' => $artical['articalView'],
                    'articalMark' => $artical['articalMark'],
                    'articalCommentNum' => $artical['articalCommentNum'],
                    'articalStatus' => $artical['articalStatus'],
                    'userCode' => $artical['userCode'],
                    'userName' => $artical['userName'],
                    'userPhoto' => FRONT_UPLOAD_PHOTO_PATH . $artical['userPhoto'],
                    'nickName' => $artical['nickName'],
                    'sax' => $artical['sax'],
                    'userLevel' => $artical['userLevel'],
                    'userJob' => $artical['userJob'],
                    'jobDesc' => $artical['jobDesc'],
                    'userJob' => $artical['userJob'],
                    'userJob' => $artical['userJob']
                );
            };
            return array('articalList' => $articalList, 'total' => $total, 'current' => $current + 1, 'size' => $size);
        }
        public static function getArticalByCodeAction() {
            $articalCode = $_GET['articalCode'];
            $sql = "select articals.*, users.* from articals left join users on articals.userCode = users.userCode where articalStatus != 0 and articalCode='$articalCode'";
            $mysql = new Mysql($GLOBALS['config']);
            $artical = $mysql -> getRow($sql);
            if ($artical) {
                $articalImages = array();
                if ($artical['articalImages']) {
                    foreach( explode(',', $artical['articalImages']) as $value) {
                        $fileCode = explode('.', $value);
                        $articalImages[] = array(
                            'viewAdd' => FRONT_UPLOAD_CONTENT_PATH . $value,
                            'fileName' => $value,
                            'fileCode' => $fileCode[0]
                        );
                    }
                }
                $bookName = $mysql -> getRow("select * from books where bookCode='$artical[bookCode]'");
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                        'articalInfo' => array(
                            'articalCode' => $artical['articalCode'],
                            'articalTitle' => $artical['articalTitle'],
                            'categoryCode' => $artical['categoryCode'],
                            'articalContent' => $artical['articalContent'],
                            'articalPhoto' => array(
                                'url' => FRONT_UPLOAD_COVER_PATH . $artical['articalPhoto'],
                                'fileName' => $artical['articalPhoto']
                            ),
                            'articalImages' => $articalImages,
                            'articalCreateDate' => $artical['articalCreateDate'],
                            'articalType' => $artical['articalType'],
                            'articalView' => $artical['articalView'],
                            'articalMark' => $artical['articalMark'],
                            'articalCommentNum' => $artical['articalCommentNum'],
                            'articalStatus' => $artical['articalStatus'],
                            'bookCode' => $artical['bookCode'],
                            'userCode' => $artical['userCode'],
                            'userName' => $artical['userName'],
                            'userPhoto' => FRONT_UPLOAD_PHOTO_PATH . $artical['userPhoto'],
                            'nickName' => $artical['nickName'],
                            'sax' => $artical['sax'],
                            'userLevel' => $artical['userLevel'],
                            'userJob' => $artical['userJob'],
                            'jobDesc' => $artical['jobDesc'],
                            'userJob' => $artical['userJob'],
                            'userJob' => $artical['userJob'],
                            'bookName' => $bookName['bookName']
                        )
                    )
                );
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '操作失败',
                    )
                );
            }
            self :: send();
        }

        public static function ArticalViewAction() {
            $articalCode = $_GET['articalCode'];
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $y=date("Y"); 
            $m=date("m"); 
            $d=date("d");
            $createDate = strtotime( $y . '-' . $m . '-' . $d);
            $sql = "select * from articalViews where articalCode='$articalCode' and visitorIp='$user_ip' and createDate=$createDate";
            $mysql = new Mysql($GLOBALS['config']);
            if (!$mysql -> getAll($sql)) {
                $viewId = 'null';
                $viewCode = self::initCode();
                $sql = "insert into articalViews values (
                    $viewId,
                    '$viewCode',
                    '$articalCode',
                    '$user_ip',
                    $createDate
                )";
                $mysql -> query($sql);
                $sql = "update articals set 
                articalView=(articalView + 1)
                where 
                articalCode='$articalCode'";
                $mysql -> query($sql);
            } else {
                echo 123;
            }
        }

        public static function ArticalMarkAction() {
            $articalCode = $_GET['articalCode'];
            $userCode = $_GET['userCode'];
            $y=date("Y"); 
            $m=date("m"); 
            $d=date("d");
            $createDate = strtotime( $y . '-' . $m . '-' . $d);
            $sql = "select * from articalMarks where articalCode='$articalCode' and userCode='$userCode'";
            $mysql = new Mysql($GLOBALS['config']);
            if (!$mysql -> getAll($sql)) {
                $markId = 'null';
                $markCode = self::initCode();
                $sql = "insert into articalMarks values (
                    $markId,
                    '$markCode',
                    '$articalCode',
                    '$userCode',
                    $createDate
                )";
                if ($mysql -> query($sql)) {
                    $sql = "update articals set 
                    articalMark=(articalMark + 1)
                    where 
                    articalCode='$articalCode'";
                    $mysql -> query($sql);
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
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '操作失败'
                    )
                );
            }
            self :: send();
        }

        public static function getArticalListByUserCodeAction() {
            $userCode = $_GET['userCode'];
            $self = $_GET['self'];
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
            $sql = "select * from articals where userCode='$userCode' and articalType != 2 ";
            if ($self != 'true') {
                $sql .= "and articalStatus != 0";
            }
            $total = count($mysql -> getAll($sql));
            $current = ($current - 1) * $size;
            $sql.=" limit $current ,$size";
            $articalList = $mysql -> getAll($sql);
            if ($articalList) {
                $copy = array();
                foreach($articalList as $key => $value) {
                    $value['articalPhoto'] = array(
                        'url' => FRONT_UPLOAD_COVER_PATH . $value['articalPhoto'],
                        'fileName' => $value['articalPhoto']
                    );
                    $copy[] = $value;
                }
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                        'articalList' => $copy,
                        'total' => $total
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

        public static function getArticalListByBookCodeAction() {
            $bookCode = $_GET['bookCode'];
            $self = $_GET['self'];
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "select * from articals where bookCode='$bookCode' and articalType=2 ";
            if ($self !== 'true') {
                $sql .= "and articalStatus!=0";
            }
            $articalList = $mysql -> getAll($sql);
            if ($articalList) {
                $copy = array();
                foreach($articalList as $key => $value) {
                    $value['articalPhoto'] = array(
                        'url' => FRONT_UPLOAD_COVER_PATH . $value['articalPhoto'],
                        'fileName' => $value['articalPhoto']
                    );
                    $copy[] = $value;
                }
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                        'articalList' => $copy,
                        'total' => $total
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

        public static function deleteArticalByCodeAction() {
            $articalCode = $_GET['articalCode'];
            // 从session中取出userCode userCode = $userCode;
            if ($articalCode === null) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '操作失败'
                    )
                );
            } else {
                $mysql = new Mysql($GLOBALS['config']);
                $articalInfo = $mysql -> getRow("select * from articals where articalCode='$articalCode'");
                $sql = "delete from articals where articalCode='$articalCode'";
                if ($mysql -> query($sql)) {
                    $fileNames = array();
                    if ($articalInfo['articalType'] === '1') {
                        $fileNames['content'] = explode(',', $articalInfo['articalImages']);
                    }
                    $fileNames['photo'] = $articalInfo['articalPhoto'];
                    self :: deleteFilesByFileNameAction($fileNames);
                    self :: setContent(
                        array('isSuccess' => true,
                            'message' => '操作成功',
                        )
                    );
                } else {
                    self :: setContent(
                        array('isSuccess' => false,
                            'message' => '操作失败',
                        )
                    );
                }
            }
            self :: send();
        }

        // 根据fileName 删除附件
        public static function deleteFilesByFileNameAction($fileNames){
            if (!$fileNames) {
                return false;
            }
            $mysql = new Mysql($GLOBALS['config']);
            foreach($fileNames as $key => $value) {
                if ($key === 'content') {
                    foreach($value as $fileName) {
                        if ($mysql -> query("delete from files where fileName='$fileName'")) {
                            $fileName = UPLOAD_CONTENT_PATH . $fileName;
                            unlink($fileName);
                        }
                    }
                } else if ($key === 'photo') {
                    if ($mysql -> query("delete from files where fileName='$value'")) {
                        $value = UPLOAD_PHOTO_PATH . $value;
                        unlink($value);
                    }  
                }
            }
        }
    }
?>