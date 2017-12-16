<?php
    class NavController extends Controller {
        public static function navlistAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "select * from navs order by navId";
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

        public static function editNavAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $navList = $_GET['navList'];
            $navList = json_decode($navList);
            
            if ($navList) {
                $sql = "delete from navs";
                $mysql -> query($sql);
                $sql = "insert into navs values";
                foreach($navList as $value) {
                    $value = get_object_vars($value);
                    $sql .= "(null, $value[navIndex], '$value[navName]', '$value[navUrl]', $value[parentId], $value[navTarget]),";
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
    }
?>