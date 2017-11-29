<?php
    class IndexController extends Controller {
        // 公共资源，如导航信息，产品分类信息等数据
        public static function indexAction(){
            
            $sub_path = date('Ymd').'/';
            echo '请求错误';
        }

        public static function navlistAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "select * from navs where navStatus=1";
            $navlist = $mysql -> getAll($sql);
            if ( $navlist ) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'navList' => $navlist
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

        public static function categoryAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "select * from categorys where categoryStatus=1";
            $categoryList = $mysql -> getAll($sql);
            if ( $categoryList ) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'categoryList' => $categoryList
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

            $upload = new Upload();
            $filename = $upload->multiUp($_FILES['files'], $folder);
            if ($filename) {
                $sql = "insert into files values ";
                $lastShowTime = time();
                foreach($filename as $indx => $value) {
                    if (!$indx) {
                        $sql .= "(null, '$value[fileCode]','$value[fileName]',$_POST[type],'$value[url]','$_POST[userCode]','$value[mime]', $lastShowTime)";
                    } else {
                        $sql .= ",(null, '$value[fileCode]','$value[fileName]',$_POST[type],'$value[url]','$_POST[userCode]','$value[mime]', $lastShowTime)";
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

        // public static function MiniImageAction(){ // 压缩图片
        //     $fileCode = $_GET['fileCode'];
        //     if (!$fileCode) {
        //         return;
        //     }
        //     $mysql = new Mysql($GLOBALS['config']);
        //     $file = $mysql -> getRow("select * from files where filesCode='$fileCode'");
        //     if ($file) {
        //         header('Content-type:image/png');
        //         $lastShowTime = time();
        //         $mysql -> query("update users set lastShowTime=$lastShowTime where filesCode='$fileCode'");
        //         if (!is_file(UPLOAD_PATH . "thumb_$file[filesName]")) {
        //             $img = new Image();
        //             $img->thumbnail(ROOT.$file['url'],290,180, UPLOAD_PATH);
        //         }
        //         $str = file_get_contents(UPLOAD_PATH . "thumb_$file[filesName]");
        //         echo $str;
        //     };
        // }

        public static function DownLoadAction(){
            $fileCode = $_GET['fileCode'];
            if (!$fileCode) {
                return;
            }
            $mysql = new Mysql($GLOBALS['config']);
            $file = $mysql -> getRow("select * from files where filesCode='$fileCode'");
            if ($file) {
                header('Content-type:image/png');
                $lastShowTime = time();
                $mysql -> query("update users set lastShowTime=$lastShowTime where filesCode='$fileCode'");
                // 下载附件
                echo 123;
            };
        }

        public static function GetCaptchaAction(){
            $c = new Captcha();
            $c->generateCode();
            $_SESSION['captcha'] = $c->getCode();
        }
    }
?>