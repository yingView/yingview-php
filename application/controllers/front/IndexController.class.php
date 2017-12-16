<?php
    class IndexController extends Controller {
        // 公共资源，如导航信息，产品分类信息等数据
        public static function indexAction(){
            $sub_path = date('Ymd').'/';
            echo '请求错误';
        }
        
        public static function getCaptchaAction(){ // 获取二维码
            $c = new Captcha();
            $c->generateCode();
            $_SESSION['captcha'] = $c->getCode();
        }

        public static function queryBannerAction(){
            $mysql = new Mysql($GLOBALS['config']);
            $sql = "select * from banners";
            $bannerList = $mysql -> getAll($sql);
            if ($bannerList) {
                // foreach( $fileList as $key => $value) {
                //     $fileList[$key]['download'] = "/yingview.php?fileCode={$value[fileCode]}&method=downLoad&rpcname=file";
                // }
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
    }
?>