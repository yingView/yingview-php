<?php
    class BookController extends Controller {
        public static function GetBookByUserCode() {
            $userCode = $_GET['userCode'];
            self :: send();
        }
    }