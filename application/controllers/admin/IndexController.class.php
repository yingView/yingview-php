<?php
    class IndexController extends Controller {
        public static function indexAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "select * from systems";
            $system = $mysql -> getRow($sql);
            if ($system['logo']) {
                $fileArray = explode('/', $system['logo']);
                $fileName = $fileArray[count($fileArray) - 1];
                $fileCode = explode('.', $fileName);
                $system['logo'] = array(
                    'viewAdd' => $system['logo'],
                    'fileName' => $fileName,
                    'fileCode' => $fileCode[0],
                    'download' => "/yingview.php?fileCode={$fileCode[0]}&method=downLoad&rpcname=file"
                );
            };
            if ($system['logo2']) {
                $fileArray = explode('/', $system['logo2']);
                $fileName = $fileArray[count($fileArray) - 1];
                $fileCode = explode('.', $fileName);
                $system['logo2'] = array(
                    'viewAdd' => $system['logo2'],
                    'fileName' => $fileName,
                    'fileCode' => $fileCode[0],
                    'download' => "/yingview.php?fileCode={$fileCode[0]}&method=downLoad&rpcname=file"
                );
            };
            if ( $system ) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'system' => $system
                    )
                );
            } else {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功'
                    )
                );
            }
            self :: send();
        }

        public static function updateSystemAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "delete from systems";
            $system = $mysql -> query($sql);
            $system = $_GET['system'];
            $system = str_replace("\\","",$system);
            $system = json_decode($system);
            $system = get_object_vars($system);
            $sql = "insert into systems values(null, '$system[name]', '$system[host]', '$system[desc]', '$system[markLeft]','$system[markRight]', '$system[logo]', '$system[logo2]')";
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

        public static function bannerListAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "select * from banners order by bannerId";
            $bannerList = $mysql -> getAll($sql);
            if ( $bannerList ) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'bannerList' => $bannerList
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

        public static function editBannerAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $bannerList = $_GET['bannerList'];
            $bannerList = str_replace("\\","",$bannerList);
            $bannerList = json_decode($bannerList);
            if ($bannerList) {
                $sql = "delete from banners";
                $mysql -> query($sql);
                $sql = "insert into banners values";
                foreach($bannerList as $value) {
                    $value = get_object_vars($value);
                    $sql .= "(null, '$value[toUrl]', '$value[imgUrl]'),";
                }
                $sql = substr($sql, 0, -1);
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
            } else {
                self :: setContent(
                    array('isSuccess' => false,
                        'message' => '操作失败'
                    )
                );
            }
            self :: send();
        }

        public static function querySystemFileAction(){
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
            $sql = "select * from files where type=3";
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