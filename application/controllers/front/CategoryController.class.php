<?php
    class CategoryController extends Controller {
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
    }
?>