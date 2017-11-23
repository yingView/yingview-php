<?php
    class Framework {
        public static function run() {
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
            define("DS", DIRECTORY_SEPARATOR);
            define("ROOT", getcwd() . DS);
            define("APP_PATH", ROOT . 'application' . DS);
            define("FRAMWORK_PATH", ROOT . 'framework' . DS);
            define("PUBLIC_PATH", ROOT . 'public' . DS);
            define("CONFIG_PATH", APP_PATH . 'config' . DS);
            define("CONTROLLER_PATH", APP_PATH . 'controllers' . DS);
            define("MODEL_PATH", APP_PATH . 'models' . DS);
            define("CONTROLLER", isset($_GET['c']) ? ucfirst($_GET['c']) : 'Index');
            define("ACTION", isset($_GET['a']) ? $_GET['a'] : 'index');
            define("PLATFORM", isset($_GET['p']) ? $_GET['p'] : 'admin');
            define("CUR_CONTROLLER_PATH", CONTROLLER_PATH . PLATFORM . DS);
        }
    }
?>