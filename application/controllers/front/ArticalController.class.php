<?php
    class ArticalController extends Controller  {
        public static function EditAction() {
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
            $articalInfo = get_object_vars(json_decode($_GET['content']));
            $articalId = 'null';
            $articalCode = $articalInfo['articalCode'];
            $articalTitle = $articalInfo['articalTitle'];
            $userCode = $articalInfo['userCode'];
            $categoryCode = $articalInfo['categoryCode'];
            $articalContent = htmlspecialchars($articalInfo['articalContent']);
            $articalPhoto = $articalInfo['articalPhoto'];
            $articalImages = $articalInfo['articalImages'] ? $articalInfo['articalImages'] : 'null';
            $articalCreateDate = $operate === 'submit' ? time() : 0;
            $articalType = $articalInfo['articalType'];
            $articalView = 0;
            $articalMark = 0;
            $articalCommentNum = 0;
            $articalStatus = $operate === 'submit' ? 1 : 0;
            $bookId = $articalInfo['bookId'] ? $articalInfo['bookId'] : 0;
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
                    $bookId
                )";
            } else if ($articalCode) {
                $articalCode = addslashes($articalCode);
                $time = $mysql -> getRow("select * from articals where articalCode='$articalCode'")['articalCreateDate'];
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
                    bookId=$bookId
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
        public static function ArticalQueryAction() {
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

        // 查询详情 或者 编辑 通过 articalCode
        public static function QueryAction() {
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
                    'photoImage' => FRONT_UPLOAD_PHOTO_PATH . $artical['photoImage'],
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
        public static function GetArticalByCodeAction() {
            $articalCode = $_GET['articalCode'];
            $sql = "select articals.*, users.* from articals left join users on articals.userCode = users.userCode where articalStatus != 0 and articalCode='$articalCode'";
            $mysql = new Mysql($GLOBALS['config']);
            $artical = $mysql -> getRow($sql);
            if ($artical) {
                $articalImages = array();
                if ($artical['articalImages']) {
                    foreach( explode(',', $artical['articalImages']) as $value) {
                        $articalImages[] = array(
                            'viewAdd' => FRONT_UPLOAD_CONTENT_PATH . $value,
                            'fileName' => $value,
                            'fileCode' => explode('.', $value)[0]
                        );
                    }
                }
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
                            'userCode' => $artical['userCode'],
                            'userName' => $artical['userName'],
                            'photoImage' => FRONT_UPLOAD_PHOTO_PATH . $artical['photoImage'],
                            'nickName' => $artical['nickName'],
                            'sax' => $artical['sax'],
                            'userLevel' => $artical['userLevel'],
                            'userJob' => $artical['userJob'],
                            'jobDesc' => $artical['jobDesc'],
                            'userJob' => $artical['userJob'],
                            'userJob' => $artical['userJob']
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
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '操作失败',
                    )
                );
            }
            self :: send();
        }
    }
?>