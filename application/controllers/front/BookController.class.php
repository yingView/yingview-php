<?php
    class BookController extends Controller {

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
            $bookInfo = get_object_vars(json_decode($content));
            $bookId = 'null';
            $bookCode = $bookInfo['bookCode'];
            $bookPhoto = $bookInfo['bookPhoto'];
            $bookName = $bookInfo['bookName'];
            $categoryCode = $bookInfo['categoryCode'];
            $userCode = $bookInfo['userCode'];
            $bookView = 0;
            $bookMark = 0;
            $bookCommentNum = 0;
            $bookDesc = $bookInfo['bookDesc'] ? $bookInfo['bookDesc'] : 'null';
            $bookCreateDate = $operate === 'submit' ? time() : 0;
            $bookStatus = $operate === 'submit' ? 1 : 0;
            $sql = "";
            $mysql = new Mysql($GLOBALS['config']);
            if (!$bookCode) {
                $bookCode = self::initCode();
                $sql = "insert into books values (
                    $bookId,
                    '$bookCode',
                    '$bookPhoto',
                    '$bookName',
                    '$categoryCode',
                    '$userCode',
                    '$bookView',
                    '$bookMark',
                    $bookCommentNum,
                    '$bookDesc',
                    $bookCreateDate,
                    $bookStatus
                )";
            } else if ($bookCode) {
                $bookCode = addslashes($bookCode);
                $time = $mysql -> getRow("select * from books where bookCode='$bookCode'");
                $time = $time['bookCreateDate'];
                $bookCreateDate = $time ? $time : time();
                $sql = "update books set 
                bookPhoto='$bookPhoto',
                bookName='$bookName',
                categoryCode='$categoryCode',
                bookDesc=$bookDesc,
                bookCreateDate=$bookCreateDate,
                bookStatus=$bookStatus
                where 
                bookCode='$bookCode' and userCode='$userCode'";
            }
            if ($mysql -> query($sql)) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                        'bookCode' => $bookCode
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

        public static function getBookListByUserCodeAction() {
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
            $sql = "select * from books where userCode='$userCode' ";
            if ($self != 'true') {
                $sql .= "and bookStatus != 0";
            }
            $total = count($mysql -> getAll($sql));
            $current = ($current - 1) * $size;
            $sql.=" limit $current ,$size";
            $bookList = $mysql -> getAll($sql);
            if ($bookList) {
                $copy = array();
                foreach($bookList as $key => $value) {
                    $value['bookPhoto'] = array(
                        'url' => FRONT_UPLOAD_COVER_PATH . $value['bookPhoto'],
                        'fileName' => $value['bookPhoto']
                    );
                    $copy[] = $value;
                }
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
                        'bookList' => $copy,
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

        public static function getBookByCodeAction() {
            $bookCode = $_GET['bookCode'];
            // 从session中取出userCode userCode = $userCode;
            if ($bookCode === null) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '操作失败'
                    )
                );
            } else {
                $mysql = new Mysql($GLOBALS['config']);
                $bookInfo = $mysql -> getRow("select books.*, users.* from books left join users on books.userCode = users.userCode where bookCode='$bookCode'");
                if ($bookInfo) {
                    $bookView = $mysql -> getRow("select sum(articalView) as 'view', sum(articalMark) as 'mark' from articals where bookCode='$bookCode' and articalType=2 and articalStatus!=0");
                    self :: setContent(
                        array('isSuccess' => true,
                            'message' => '操作成功',
                            'bookInfo' => array(
                                'bookCode' => $bookInfo['bookCode'],
                                'bookName' => $bookInfo['bookName'],
                                'categoryCode' => $bookInfo['categoryCode'],
                                'bookPhoto' => array(
                                    'url' => FRONT_UPLOAD_COVER_PATH . $bookInfo['bookPhoto'],
                                    'fileName' => $bookInfo['bookPhoto']
                                ),
                                'bookView' => $bookView['view'],
                                'bookMark' => $bookView['mark'],
                                'bookCommentNum' => $bookInfo['bookCommentNum'],
                                'bookDesc' => $bookInfo['bookDesc'],
                                'bookCreateDate' => $bookInfo['bookCreateDate'],
                                'bookStatus' => $bookInfo['bookStatus'],
                                'userCode' => $bookInfo['userCode'],
                                'userName' => $bookInfo['userName'],
                                'userPhoto' => FRONT_UPLOAD_PHOTO_PATH . $bookInfo['userPhoto'],
                                'nickName' => $bookInfo['nickName'],
                                'sax' => $bookInfo['sax'],
                                'userLevel' => $bookInfo['userLevel'],
                                'userJob' => $bookInfo['userJob'],
                                'jobDesc' => $bookInfo['jobDesc'],
                                'userJob' => $bookInfo['userJob'],
                                'userJob' => $bookInfo['userJob']
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
            }
            self :: send();
        }

        public static function deleteBookByCodeAction() {
            $bookCode = $_GET['bookCode'];
            // 从session中取出userCode userCode = $userCode;
            if ($bookCode === null) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '操作失败'
                    )
                );
            } else {
                $mysql = new Mysql($GLOBALS['config']);
                $bookInfo = $mysql -> getRow("select * from books where bookCode='$bookCode'");
                $sql = "delete from books where bookCode='$bookCode'";
                if ($mysql -> query($sql)) {
                    self :: deleteFilesByFileNameAction($bookInfo['bookPhoto']);
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
        public static function deleteFilesByFileNameAction($bookPhoto){
            if (!$bookPhoto) {
                return false;
            }
            $mysql = new Mysql($GLOBALS['config']);
            if ($mysql -> query("delete from files where fileName='$bookPhoto'")) {
                $bookPhoto = UPLOAD_PHOTO_PATH . $bookPhoto;
                unlink($bookPhoto);
            }  
        }
    }
?>