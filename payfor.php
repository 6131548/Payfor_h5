 class Payfor{	
 	public function index(){
		$headers = array();
                $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
                $headers[] = 'Connection: Keep-Alive';
                $headers[] = 'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3';
                $headers[] = 'Accept-Encoding: gzip, deflate';
                $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:22.0) Gecko/20100101 Firefox/22.0';
                $res = config('wechat');
                $appid = $res['appid'];
                $mch_id = $res['mch_id'];
                $key = $res['key'];
               
                $rand = rand(00000,99999);
                //$out_trade_no = date('YmdHis',time()).$rand;//平台内部订单号
		$out_trade_no = time().rand(0000,9999)
                $nonce_str=MD5($out_trade_no);//随机字符串
                $body = $pro_name;//内容
                $total_fee = $tatol*100; //金额
                //$total_fee = 1;
                $spbill_create_ip =  GetClientIp();
                $notify_url = "http://www.xxx.com/xxx"; //回调地址(支付完成之后微信会将结果参数带入这个方法) 这个根据自己逻辑需求填写
                $trade_type = 'MWEB';//交易类型 具体看API 里面有详细介绍
                $scene_info ='{"h5_info":{"type":"Wap","wap_url":"http://www.xxx.com","wap_name":"支付"}}';//场景信息 必要参数
                $signA ="appid=$appid&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";

                $strSignTmp = $signA."&key=$key"; //拼接字符串 注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML 是否正确
                $sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写

                $post_data="<xml><appid>$appid</appid><body>$body</body><mch_id>$mch_id</mch_id><nonce_str>$nonce_str</nonce_str><notify_url>$notify_url</notify_url><out_trade_no>$out_trade_no</out_trade_no><scene_info>$scene_info</scene_info><spbill_create_ip>$spbill_create_ip</spbill_create_ip><total_fee>$total_fee</total_fee><trade_type>$trade_type</trade_type><sign>$sign</sign>
                </xml>";//拼接成XML格式 *XML格式文件要求非常严谨不能有空格这点一定要注意
                 
                // 这里是官方给的微信支付接口签名校验工具可以对你拼接的xml数据进行校验对比重点是对比签名sign是否正确
                // https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=20_1
                 
                $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信下单接口连接不用更改

                $dataxml = $this->http_post($url,$post_data,$headers);//传参调用curl请求
                $objectxml = (array)simplexml_load_string($dataxml,'SimpleXMLElement',LIBXML_NOCDATA); //将微信返回的XML 转换成数组
                // var_dump($objectxml);
                 if($objectxml['return_code'] =='SUCCESS'){
                    $web_url= $objectxml['mweb_url'];
                    $this->assign('web_url',$web_url) ;

                 }
	}
	
	public  function http_post($url='',$post_data=array(),$header=array(),$timeout=30) { 
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                 
                $response = curl_exec($ch);

                curl_close($ch);

                return $response;
    }
}		 
		 
