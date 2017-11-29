<?php
    class Framework {
        public static function run() {
            // 设置主机名字
            $GLOBALS['localhost'] = 'http://127.0.0.1';
            // 设置时区;
            ini_set('date.timezone','Asia/Shanghai');
            // 开启session
            session_start();
            // 关闭警告
            error_reporting(0);
            self :: init();
            self :: autoload();
            self :: dispatch();
        }

        private static function dispatch() {
            $controller_name = CONTROLLER . 'Controller';
            $action_name = ACTION . 'Action';
            $controller = new $controller_name();
            $controller :: $action_name();
        }
        private static function autoload() {
            spl_autoload_register(array(__CLASS__, 'load'));
        }
        private static function load($classname) {
            if (substr($classname, -10) == 'Controller') {
                include CUR_CONTROLLER_PATH . "{$classname}.class.php";
            } else if (substr($classname, -5) == 'Model') {
                include MODEL_PATH . "{$classname}.class.php";
            } else {
                echo '无';
            }
        }
        private static function init() {

            foreach( $_POST as $key => $value) {
                $_GET[$key] = $value;
            };

            define("DS", DIRECTORY_SEPARATOR);
            define("ROOT", getcwd() . DS);
            define("APP_PATH", ROOT . 'application' . DS);
            define("FRAMWORK_PATH", ROOT . 'framework' . DS);
            define("PUBLIC_PATH", ROOT . 'public' . DS);
            define("CONFIG_PATH", APP_PATH . 'config' . DS);
            define("CONTROLLER_PATH", APP_PATH . 'controllers' . DS);
            define("MODEL_PATH", APP_PATH . 'models' . DS);
            define('CORE_PATH', FRAMWORK_PATH . 'core' . DS);
            define('DB_PATH', FRAMWORK_PATH . 'databases' . DS);
            define('LIB_PATH', FRAMWORK_PATH . 'libraries' . DS);
            define("CONTROLLER", isset($_GET['rpcname']) ? ucfirst($_GET['rpcname']) : 'Index');
            define("ACTION", isset($_GET['method']) ? $_GET['method'] : 'index');
            define("PLATFORM", isset($_GET['p']) ? $_GET['p'] : 'front');
            define("CUR_CONTROLLER_PATH", CONTROLLER_PATH . PLATFORM . DS);
            define("UPLOAD_PATH", PUBLIC_PATH . 'uploads' . DS);
            define("FRONT_UPLOAD_PATH",  '/public/uploads' . DS);
            define("FRONT_UPLOAD_COVER_PATH", '/public/uploads/covers' . DS);
            define("FRONT_UPLOAD_CONTENT_PATH", '/public/uploads/contents' . DS);
            define("FRONT_UPLOAD_PHOTO_PATH", '/public/uploads/photos' . DS);
 
            // 设置跨域
            header('Access-Control-Allow-Origin:*');
            // 设置编码
            header("Content-type: text/html; charset=utf-8");
            // 引入基础控制器
            include CORE_PATH . 'Controller.class.php';

            // 引入数据库基础模型
            include CORE_PATH . 'Model.class.php';
            // 邮件发送
            include LIB_PATH . 'Mail.class.php';
            // 上传附件
            include LIB_PATH . 'Upload.class.php';
            // 缩略图和水印
            include LIB_PATH . 'Image.class.php';
            // 添加验证码
            include LIB_PATH . 'Captcha.class.php';

            // 载入数据库配置项

            $GLOBALS['config'] = include CONFIG_PATH . 'config.php';
            $GLOBALS['mailconfig'] = include CONFIG_PATH . 'mailconfig.php';
            include DB_PATH . 'Mysql.class.php';

        }
    }
?>