<?php
    class BookController extends Controller {
        public static function getBookByUserCode() {
            $userCode = $_GET['userCode'];
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
            $sql = "select * from book where userCode='$userCode' ";
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