<?php
    class IndexController extends Controller {
        public static function indexAction(){

            $aaa = new Mysql($GLOBALS['config']);
            var_dump($aaa -> getAll('select * from users'));
            // echo self :: initCode(16);
        }
    }
?>