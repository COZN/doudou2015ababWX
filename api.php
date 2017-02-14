<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
//开启微信自动回复功能
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    //接收
    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $msgType = $postObj->MsgType;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>"; 
                $newsTpl = "<xml>
                            <ToUserName><![CDATA[toUser]]></ToUserName>
                            <FromUserName><![CDATA[fromUser]]></FromUserName>
                            <CreateTime>12345678</CreateTime>
                            <MsgType><![CDATA[news]]></MsgType>
                            <ArticleCount>%s</ArticleCount>
                            %s
                            </xml>";
                if(!empty( $keyword ))
                {
                    //接收文本信息   
                    if( $msgType == 'text'){
                        $msgType = "text";
                        if( 1 == $keyword ){
                           $contentStr = "110"; 
                        }
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }else if( 'image' == $msgType){
                        $msgType = 'news';
                        $count = 1;
                        $str = '<Articles>';
                        for($i=1;$i<=$count;$i++){
                            $str .= '<item>
                                    <Title><![CDATA[我的天]]></Title>
                                    <Description><![CDATA[疯狂的api]]></Description>
                                    <PicUrl><![CDATA[https://mp.weixin.qq.com/misc/getheadimg?fakeid=3096430347&token=154153930&lang=zh_CN]]></PicUrl>
                                    <Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MzA4OTI2ODEwNg==&mid=2681037458&idx=1&sn=2466f564c4f18e70cfeeeea20120d196&chksm=8a244877bd53c1613464426269b239e0a7486321596bbde42cb71cf0404a741b940fc38cb944&scene=0#rd]]></Url>
                                    </item>';
                        }
                        $str .= '</Articles>';
                        $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, $msgType, $count, $str);
                        echo $resultStr;
                    }
                }else{
                    echo "Input something...";
                }   
        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>