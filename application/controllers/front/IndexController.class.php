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

        public static function queryByKeyWorkAction() {
            $keyword = $_GET['keyword'];
            $current = $_GET['current'];
            $size = $_GET['size'];
            $total = 0;
            $sql = "select articals.*, users.* from articals left join users on articals.userCode = users.userCode 
            where (articalTitle LIKE '%$keyword%' or articalContent LIKE '%$keyword%') and articalStatus = 1 order by articalView desc ";
            $mysql = new Mysql($GLOBALS['config']);
            $total = count($mysql -> getAll($sql));
            $current = ($current - 1) * $size;
            $sql.="limit $current ,$size";
            $articalArray = $mysql -> getAll($sql);
            $articalList = array();
            if ($articalArray) {
                foreach($articalArray as $artical) {
                    $articalList[] = array(
                        'articalCode' => $artical['articalCode'],
                        'articalTitle' => $artical['articalTitle'],
                        'categoryCode' => $artical['categoryCode'],
                        'articalContent' => $artical['articalContent'],
                        'articalPhoto' => FRONT_UPLOAD_COVER_PATH . $artical['articalPhoto'],
                        'articalImages' => $artical['articalImages'],
                        'articalCreateDate' => $artical['articalCreateDate'],
                        'articalType' => $artical['articalType'],
                        'articalView' => $artical['articalView'],
                        'articalMark' => $artical['articalMark'],
                        'articalCommentNum' => $artical['articalCommentNum'],
                        'articalStatus' => $artical['articalStatus'],
                        'userCode' => $artical['userCode'],
                        'userName' => $artical['userName'],
                        'userPhoto' => FRONT_UPLOAD_PHOTO_PATH . $artical['userPhoto'],
                        'nickName' => $artical['nickName'],
                        'sax' => $artical['sax'],
                        'userLevel' => $artical['userLevel'],
                        'userJob' => $artical['userJob'],
                        'jobDesc' => $artical['jobDesc'],
                        'userJob' => $artical['userJob'],
                        'userJob' => $artical['userJob']
                    );
                };
                self :: setContent(
                    array('isSuccess' => true,
                        'message' => '查询成功',
                        'articalList' => $articalList,
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