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
                //手机端(openid)
                $fromUsername = $postObj->FromUserName;
                //微信公众账号
                $toUsername = $postObj->ToUserName;
                //自定义$msgType，接收消息类型
                $msgType = $postObj->MsgType;
                //用户发送的消息
                $keyword = trim($postObj->Content);
                $time = time();
                //发送文本模板
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
                //发送图文模板
                $newsTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[$s]]></MsgType>
                            <ArticleCount>%s</ArticleCount>
                            %s
                            </xml>"; 
                //接收文本信息       
                if( $msgType == 'text'){
                    if(!empty( $keyword ))
                    {
                        //以文本形式回复
                        $msgType = "text";
                        if( 1 == $keyword){
                            //回复内容
                            $contentStr = "110";
                        }else{
                            $contentStr = "没提供那么多服务";
                        }
                        //格式化XML数据
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }else{
                        echo "Input something...";
                    }
                //接收图片
                }elseif( $msgType == 'image'){
                        $msgType = "text";
                        $contentStr = '这是图片';
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                }elseif( $msgType == 'location' ){
                    $msgType = 'news';
                    $count = 1;
                    $str = '<Acticles>'
                    for($i=1;$i<=1;$i++){
                        $str .= '<item>
                                <Title><![CDATA[我的天]]></Title>
                                <Description><![CDATA[这个新闻好看]]></Description>
                                <PicUrl><![CDATA[http://n.sinaimg.cn/news/transform/20170214/4rmn-fyamkqa6192887.jpg]]></PicUrl>
                                <Url><![CDATA[http://news.sina.com.cn/china/xlxw/2017-02-14/doc-ifyameqr7515522.shtml]]></Url>
                                </item>';
                    }
                    $str .= '</Articles>';
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, $msgType, $count, $contentStr);
                    echo $resultStr;
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