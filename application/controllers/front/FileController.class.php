<?php
    class FileController extends Controller {
        // 附件上传
        public static function uploadAction(){ 
            if (!$_FILES['files'] || !$_FILES['files']['name'] || !count($_FILES['files']['name'])) {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '文件不存在'
                    )
                );
                self :: send();
                return;
            }
            $folder = 'contents';
            if ($_POST['type'] === "0") {
                $folder = 'photos'; // 头像
            } else if ($_POST['type'] === '1') {
                $folder = 'covers'; // 封面
            } else if ($_POST['type'] === '2') {
                $folder = 'contents'; // 文章
            }
            $subjectCode = $_POST['subjectCode'];
            if ($subjectCode === null) {
                $subjectCode = 0;
            }
            $upload = new Upload();
            $filename = $upload->multiUp($_FILES['files'], $folder);
            if ($filename) {
                $sql = "insert into files values ";
                $lastShowTime = time();
                foreach($filename as $indx => $value) {
                    if (!$indx) {
                        $sql .= "(null, '$value[fileCode]','$value[fileName]',$_POST[type],'$value[url]','$_POST[userCode]', '$subjectCode', '$value[mime]', $lastShowTime)";
                    } else {
                        $sql .= ",(null, '$value[fileCode]','$value[fileName]',$_POST[type],'$value[url]','$_POST[userCode]', '$subjectCode', '$value[mime]', $lastShowTime)";
                    }
                    
                    if($_POST['type'] === '1') { // 如果上传的文件是头像，则进行压缩 和删除原图片 采用替换
                        $img = new Image();
                        $img->thumbnail(ROOT.$value['url'],290,180, UPLOAD_PATH."$folder/");
                        // unlink(UPLOAD_PATH . UPLOAD_PATH."$folder/$value[fileName]");
                    }
                };
                $mysql = new Mysql($GLOBALS['config']);
                if ($mysql -> query($sql)) {
                    self :: setContent(
                        array('isSuccess' => true,
                            'message' => '上传成功',
                            'files' => $filename
                        )
                    );
                } else {
                    foreach($filename as $value) {
                        unlink(UPLOAD_PATH . "$folder/$value[fileName]");
                    }
                    self :: setContent(
                        array('isSuccess' => false,
                            'message' => '上传失败'
                        )
                    );
                }
                
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '上传失败'
                    )
                );
            }           
            self :: send();
        }

        // 生成缩略图
        public static function MiniImageAction(){ 
            $fileCode = $_GET['fileCode'];
            if (!$fileCode) {
                return;
            }
            $mysql = new Mysql($GLOBALS['config']);
            $file = $mysql -> getRow("select * from files where fileCode='$fileCode'");
            if ($file) {
                header('Content-type:image/png');
                $lastShowTime = time();
                $mysql -> query("update users set lastShowTime=$lastShowTime where fileCode='$fileCode'");
                if (!is_file(UPLOAD_PATH . "thumb_$file[fileName]")) {
                    $img = new Image();
                    $img->thumbnail(ROOT.$file['url'],290,180, UPLOAD_PATH);
                }
                $str = file_get_contents(UPLOAD_PATH . "thumb_$file[fileName]");
                echo $str;
            };
        }

        // 下载附件
        public static function downLoadAction(){
            $fileCode = $_GET['fileCode'];
            if (!$fileCode) {
                return;
            }
            $mysql = new Mysql($GLOBALS['config']);
            $file = $mysql -> getRow("select * from files where fileCode='$fileCode'");
            if ($file) {
                // 更新最后访问时间
                $lastShowTime = time();
                $mysql -> query("update users set lastShowTime=$lastShowTime where fileCode='$fileCode'");

                // 下载
                $fileName = $file['fileName'];
                $type = $file['type'];
                if ($type === '0') {
                    $fileName = UPLOAD_PHOTO_PATH . $fileName;
                } else if ($type === '1') {
                    $fileName = UPLOAD_COVER_PATH . $fileName;
                } else if ($type === '2') {
                    $fileName = UPLOAD_CONTENT_PATH . $fileName;
                }
                $fileinfo = pathinfo($fileName);
                header('Content-type: application/x-'.$fileinfo['extension']);
                header('Content-Disposition: attachment; filename='.$fileinfo['basename']);
                header('Content-Length: '.filesize($fileName));
                readfile($fileName);
                exit();
            };
        }

        // 根据userCode 删除附件
        public static function deleteByUserCodeAction(){
            $userCode = $_GET['userCode'];
            if (!$userCode) {
                return;
            }
            $mysql = new Mysql($GLOBALS['config']);
            $files = $mysql -> getAll("select * from files where userCode='$userCode'");
            $sql = "delete from files where userCode='$userCode'";
            if ($mysql -> query($sql)) {
                // 删除附件
                foreach( $files as $file) {
                $fileName = $file['fileName'];
                    $type = $file['type'];
                    if ($type === '0') {
                        $fileName = UPLOAD_PHOTO_PATH . $fileName;
                    } else if ($type === '1') {
                        $fileName = UPLOAD_COVER_PATH . $fileName;
                    } else if ($type === '2') {
                        $fileName = UPLOAD_CONTENT_PATH . $fileName;
                    }
                    unlink($fileName);
                }
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

        // 根据fileCode 删除附件
        public static function deleteByFileCodeAction(){
            $fileCode = $_GET['fileCode'];
            if (!$fileCode) {
                return;
            }
            $mysql = new Mysql($GLOBALS['config']);
            $file = $mysql -> getRow("select * from files where fileCode='$fileCode'");
            $sql = "delete from files where fileCode='$fileCode'";
            if ($mysql -> query($sql)) {
                // 删除附件
                $fileName = $file['fileName'];
                $type = $file['type'];
                if ($type === '0') {
                    $fileName = UPLOAD_PHOTO_PATH . $fileName;
                } else if ($type === '1') {
                    $fileName = UPLOAD_COVER_PATH . $fileName;
                } else if ($type === '2') {
                    $fileName = UPLOAD_CONTENT_PATH . $fileName;
                }
                unlink($fileName);
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

        // 通过附件code查询
        public static function queryByFileCodeAction(){
            $fileCode = $_GET['fileCode'];
            if (!$fileCode) {
                return;
            }
            $mysql = new Mysql($GLOBALS['config']);
            $file = $mysql -> getRow("select * from files where fileCode='$fileCode'");
            if ($file) {
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

        // 通过usercode查询
        public static function queryByUserCodeAction(){
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
            $sql = "select * from files where userCode='$userCode'";
            $total = count($mysql -> getAll($sql));
            
            $sql.=" limit $current ,$size";
            $fileList = $mysql -> getAll($sql);
            if ($fileList) {
                foreach( $fileList as $key => $value) {
                    $fileList[$key]['download'] = "/yingview.php?fileCode={$value[fileCode]}&method=downLoad&rpcname=file";
                }
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'fileList' => $fileList,
                        'total' => $total
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
    }
?>