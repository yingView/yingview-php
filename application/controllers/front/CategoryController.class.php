<?php
    class CategoryController extends Controller {
        public static function quertCategoryAction(){
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

        public static function deletetCategoryByCodeAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $categoryCode = $_GET['categoryCode'];
            $sql = "delete from categorys where categoryCode='$categoryCode'";
            echo $sql;
            if ($mysql -> query($sql)) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
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
        public static function editCategoryAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $categoryId = $_GET['categoryId'];
            $categoryCode = $_GET['categoryCode'];
            $categoryName = $_GET['categoryName'];
            $parentCategoryId = $_GET['parentCategoryId'];
            $categoryStatus = $_GET['categoryStatus'];
            $sql = "update categorys set 
            categoryName='$categoryName',
            parentCategoryId=$parentCategoryId,
            categoryStatus=$categoryStatus
                where 
                categoryCode='$categoryCode'";
            if ($categoryCode === '' ) {
                $categoryCode = self::initCode();
                $sql = "insert into categorys values( null, '$categoryCode', '$categoryName', $parentCategoryId, $categoryStatus)";
            }
            if ($mysql -> query($sql)) {
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '操作成功',
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
    }
?>