<?php
    class NavController extends Controller {
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
    }
?>