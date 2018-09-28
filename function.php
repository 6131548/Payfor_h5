function GetClientIp(){
        $cip="unknown";
        if($_SERVER['REMOTE_ADDR']){
            $cip = $_SERVER['REMOTE_ADDR'];
        }elseif(getenv('REMOTE_ADDR')){
            $cip = getenv('REMOTE_ADDR');
        }
        return $cip;
    }   
