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
            $folder = 'content';
            if ($_POST['type'] === "0") {
                $folder = 'photo'; // 头像
            } else if ($_POST['type'] === '1') {
                $folder = 'cover'; // 封面
            } else if ($_POST['type'] === '2') {
                $folder = 'content'; // 文章
            }

            $upload = new Upload();
            $filename = $upload->multiUp($_FILES['files'], $folder);
            if ($filename) {
                $sql = "insert into files values ";
                foreach($filename as $indx => $value) {
                    if (!$indx) {
                        $sql .= "(null, '$value[fileCode]','$value[fileName]',$_POST[type],'$value[url]','$_POST[userCode]','$value[mime]')";
                    } else {
                        $sql .= ",(null, '$value[fileCode]','$value[fileName]',$_POST[type],'$value[url]','$_POST[userCode]','$value[mime]')";
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

            // `fileId` int PRIMARY KEY AUTO_INCREMENT,
            // `filesCode` varchar(64) NOT NULL,
            // `filesName` varchar(70) NOT NULL,
            // `type` TINYINT, /* 0 代表头像 1，代表封面，2、代表文章*/
            // `url` varchar(500) NOT NULL, /* 访问路径 */
            // `userCode` varchar(64) NOT NULL,
            // `filesMime` varchar(16)            
            self :: send();
        }

    }
?>