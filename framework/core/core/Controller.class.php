<?php
    class Controller {
        private static $retValue = array(
            'hasError' => false,
            'content' => null,
            'message' => '请求成功'
        );
        public static function initCode() {
            $str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $key = "";
            for($i = 0; $i < 32; $i++)
             {
                 $key .= $str{mt_rand(0,32)};    //生成php随机数
             }
             return md5($key);
        }

        // 设置返回的content信息
        public static function setContent($content) {
            self::$retValue['content'] = $content;
        }

        // 服务器错误
        public static function serverError($error) {
            self::$retValue = $error;
        }

        // 将数据返回给前端
        public static function send() {
            echo json_encode(self::$retValue);
        }
    }
?>