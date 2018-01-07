<?php
    class CommentController extends Controller  {
        public static function addAction() {
            $commentId = 'null';
            $commentCode = self::initCode();
            $articalCode = $_GET['articalCode'];
            $userCode = $_GET['userCode'];
            $bookCode = $_GET['bookCode'];
            $comContent = $_GET['comContent'];
            $comCreateDate = time();
            $comParentType = $_GET['comParentType'];
            $comParentCode = $_GET['comParentCode'];
            $comMark = 0;
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "select * from comments where articalCode='$articalCode' and userCode='$userCode' and comCreateDate > ($comCreateDate - 30)";
            if ($mysql -> getAll($sql)) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '您评论的太快了',
                    )
                );
            } else {
                $sql = "insert into comments values (
                    $commentId,
                    '$commentCode',
                    '$articalCode',
                    '$userCode',
                    '$bookCode',
                    '$comContent',
                    $comCreateDate,
                    $comParentType,
                    '$comParentCode',
                    $comMark
                )";
                if ($mysql -> query($sql)) {
                    self :: setContent(
                        array('isSuccess' => true,
                            'message' => '操作成功',
                        )
                    );
                    $sql = "update articals set 
                    articalCommentNum=(articalCommentNum + 1)
                    where 
                    articalCode='$articalCode'";
                    $mysql -> query($sql);
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

        public static function markAction() {
            $commentCode = $_GET['commentCode'];
            $userCode = $_GET['userCode'];
            $y=date("Y"); 
            $m=date("m"); 
            $d=date("d");
            $createDate = strtotime( $y . '-' . $m . '-' . $d);
            $sql = "select * from commentMarks where commentCode='$commentCode' and userCode='$userCode'";
            $mysql = new Mysql($GLOBALS['config']);
            if (!$mysql -> getAll($sql)) {
                $commentMarkId = 'null';
                $commentMarkCode = self::initCode();
                $sql = "insert into commentMarks values (
                    $commentMarkId,
                    '$commentMarkCode',
                    '$commentCode',
                    '$userCode',
                    $createDate
                )";
                if ($mysql -> query($sql)) {
                    $sql = "update comments set 
                    comMark=(comMark + 1)
                    where 
                    commentCode='$commentCode'";
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
                        'message' => '你已经赞过了',
                    )
                );
            }
            self :: send();
        }

        public static function deleteAction() {
            $userCode = $_GET['userCode'];
            $articalCode = $_GET['articalCode'];
            $comParentType = $_GET['comParentType'];
            $commentCode = $_GET['commentCode'];
            $comParentCode = $_GET['comParentCode'];
            $sql = "delete from comments where (commentCode='$commentCode' and userCode='$userCode') ";
            
            
            $mysql = new Mysql($GLOBALS['config']);
            $total = 0;
            if ($comParentType === '0') {
                $total = count($mysql -> getAll("select * from comments where comParentCode='$comParentCode'"));
            }
            if ($mysql -> query($sql)) {
                $total += 1;
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                    )
                );
                $sql = "update articals set 
                articalCommentNum=(articalCommentNum - $total)
                where 
                articalCode='$articalCode'";
                $mysql -> query($sql);
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '操作失败',
                    )
                );
            }
            self :: send();
        }

        public static function queryByArticalCodeAction() {
            $articalCode = $_GET['articalCode'];
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
            $current = ($current - 1) * $size;
            $sql = "select comments.*, users.* from comments left join users on comments.userCode = users.userCode where articalCode='$articalCode' and comParentType = 0";
            $total = count($mysql -> getAll($sql));
            $sql . " limit $current ,$size";
            $commentsByArtical = $mysql -> getAll($sql);
            $sql = "select comments.*, users.* from comments left join users on comments.userCode = users.userCode where articalCode='$articalCode' and comParentType = 1";
            $commentsByComment = $mysql -> getAll($sql);
            if ($commentsByArtical) {
                $commentlist = array();
                foreach( $commentsByArtical as $value) {
                    $commentlist[$value['commentCode']] = array(
                        'commentCode' => $value['commentCode'],
                        'articalCode' => $value['articalCode'],
                        'comContent' => $value['comContent'],
                        'bookCode' => $value['bookCode'],
                        'comCreateDate' => $value['comCreateDate'],
                        'comParentType' => $value['comParentType'],
                        'comParentCode' => $value['comParentCode'],
                        'comMark' => $value['comMark'],
                        'userCode' => $value['userCode'],
                        'userName' => $value['userName'],
                        'userPhoto' => $value['userPhoto'],
                        'nickName' => $value['nickName'],
                        'sax' => $value['sax'],
                        'userLevel' => $value['userLevel'],
                        'userJob' => $value['userJob'],
                        'jobDesc' => $value['jobDesc'],
                        'userJob' => $value['userJob'],
                        'userJob' => $value['userJob'],
                        'children' => array()
                    );
                }
                foreach( $commentsByComment as $value) {
                    if ($commentlist[$value['comParentCode']]) {
                        $commentlist[$value['comParentCode']]['children'][] = array(
                            'commentCode' => $value['commentCode'],
                            'articalCode' => $value['articalCode'],
                            'comContent' => $value['comContent'],
                            'bookCode' => $value['bookCode'],
                            'comCreateDate' => $value['comCreateDate'],
                            'comParentType' => $value['comParentType'],
                            'comParentCode' => $value['comParentCode'],
                            'comMark' => $value['comMark'],
                            'userCode' => $value['userCode'],
                            'userName' => $value['userName'],
                            'userPhoto' => $value['userPhoto'],
                            'nickName' => $value['nickName'],
                            'sax' => $value['sax'],
                            'userLevel' => $value['userLevel'],
                            'userJob' => $value['userJob'],
                            'jobDesc' => $value['jobDesc']
                        );
                    }
                }
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                        'commentList' => $commentlist,
                        'total' => $total
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

        public static function queryByUserCodeAction() {
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
            $current = ($current - 1) * $size;
            $sql = "select comments.*, articals.* from comments left join articals on comments.articalCode = articals.articalCode where comments.userCode='$userCode'";
            $total = count($mysql -> getAll($sql));
            $sql . " limit $current ,$size";
            $commentList = $mysql -> getAll($sql);
            
            if ($commentList) {
                $copy = array();
                foreach($commentList as $value) {
                    $value['articalPhoto'] = array(
                        'url' => FRONT_UPLOAD_COVER_PATH . $value['articalPhoto'],
                        'fileName' => $value['articalPhoto']
                    );
                    if ($value['articalType'] === '1') {
                        $value['commentNum'] = count($mysql -> getAll("select * from comments where comParentCode = '$value[commentCode]'"));
                    } else {
                        $value['commentNum'] = 0;
                    }
                    $copy[] = $value;
                }
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                        'commentList' => $copy,
                        'total' => $total
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

        public static function queryByBookCodeAction() {
            $bookCode = $_GET['bookCode'];
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
            $current = ($current - 1) * $size;
            $sql = "select comments.*, users.* from comments left join users on comments.userCode = users.userCode where comments.bookCode='$bookCode' and comParentType=2";
            $total = count($mysql -> getAll($sql));
            $sql . " limit $current ,$size";
            $commentList = $mysql -> getAll($sql);
            if ($commentList) {
                $copy = array();
                foreach( $commentList as $value) {
                    $copy[] = array(
                        'commentCode' => $value['commentCode'],
                        'comContent' => $value['comContent'],
                        'bookCode' => $value['bookCode'],
                        'comCreateDate' => $value['comCreateDate'],
                        'comParentType' => $value['comParentType'],
                        'comParentCode' => $value['comParentCode'],
                        'comMark' => $value['comMark'],
                        'userCode' => $value['userCode'],
                        'userName' => $value['userName'],
                        'userPhoto' => $value['userPhoto'],
                        'nickName' => $value['nickName'],
                        'sax' => $value['sax'],
                        'userLevel' => $value['userLevel'],
                        'userJob' => $value['userJob'],
                        'jobDesc' => $value['jobDesc']
                    );
                }
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                        'commentList' => $copy,
                        'total' => $total
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
    }
?>