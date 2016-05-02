<?php
/**
 * 微信公众号消息[事件]处理封装。
 * @author winerQin
 * @date 2016-04-14
 */

namespace services\WeChat;

use services\BaseService;
use common\YCore;
use models\WxEvent;

class MsgService extends BaseService {

    /**
     * 微信公众号事件处理器。
     * @param string $wx_sn $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $event_post_str 事件POST过来的内容。
     * @return string
     */
    public static function doEventHandler($wx_sn, $event_post_str) {
        if (strlen($event_post_str) === 0) {
            YCore::throw_exception(-1, '没有任何内容');
        }
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($event_post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
        $from_username = $postObj->FromUserName;
        $to_username   = $postObj->ToUserName;
        self::writeEventLog($to_username, $from_username, $postObj->MsgType, $event_post_str);
        switch (strtolower($postObj->MsgType)) {
            case 'text':
                $content = $postObj->Content;
                return self::textMsgHandler($wx_sn, $from_username, $to_username, $content);
                break;
            case 'image':
                $pic_url = $postObj->PicUrl;
                return self::imageMsgHandler($wx_sn, $from_username, $to_username, $pic_url);
                break;
            case 'voice':
                $media_id = $postObj->MediaId;
                $format = $postObj->Format;
                return self::voiceMsgHandler($wx_sn, $from_username, $to_username, $media_id, $format);
                break;
            case 'video':
                $media_id = $postObj->MediaId;
                $thumb_media_id = $postObj->ThumbMediaId;
                return self::voiceMsgHandler($wx_sn, $from_username, $to_username, $media_id, $thumb_media_id);
                break;
            case 'shortvideo':
                $media_id = $postObj->MediaId;
                $thumb_media_id = $postObj->ThumbMediaId;
                return self::shortvideoMsgHandler($wx_sn, $from_username, $to_username, $media_id, $thumb_media_id);
                break;
            case 'location':
                $location_x = $postObj->Location_X;
                $location_y = $postObj->Location_Y;
                $scale      = $postObj->Scale;
                $label      = $postObj->Label;
                return self::locationMsgHandler($wx_sn, $from_username, $to_username, $location_x, $location_y, $scale, $label);
                break;
            case 'link':
                $title = $postObj->Titlel;
                $discription = $postObj->Description;
                $url = $postObj->Url;
                return self::linkMsgHandler($wx_sn, $from_username, $to_username, $title, $discription, $url);
                break;
            case 'event':
                return self::eventMsgHandler($wx_sn, $postObj);
                break;
            default:
                return self::otherMsgHandler($wx_sn, $from_username, $to_username);
                break;
        }
    }

    /**
     * 文本消息内容处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @param string $content 文本内容。
     * @return string
     */
    protected static function textMsgHandler($wx_sn, $from_username, $to_username, $content) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 图片消息内容处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @param string $pic_url 图片内容。
     * @return string
     */
    protected static function imageMsgHandler($wx_sn, $from_username, $to_username, $pic_url) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 语音消息内容处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @param string $media_id 语音消息媒体id，可以调用多媒体文件下载接口拉取数据。
     * @param string $format 语音格式，如amr，speex等。
     * @return string
     */
    protected static function voiceMsgHandler($wx_sn, $from_username, $to_username, $media_id, $format) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 小视频消息内容处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @param string $media_id 语音消息媒体id，可以调用多媒体文件下载接口拉取数据。
     * @param string $thumb_media_id 视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
     * @return string
     */
    protected static function shortvideoMsgHandler($wx_sn, $from_username, $to_username, $media_id, $thumb_media_id) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 地理位置消息内容处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @param double $location_x 地理位置维度。
     * @param double $location_y 地理位置经度。
     * @param int $scale 地图缩放大小。
     * @param string $label 地理位置信息。
     * @return string
     */
    protected static function locationMsgHandler($wx_sn, $from_username, $to_username, $location_x, $location_y, $scale, $label) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 链接消息内容处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @param string $title 消息标题。
     * @param string $description 消息描述。
     * @param string $url 消息链接。
     * @return string
     */
    protected static function linkMsgHandler($wx_sn, $from_username, $to_username, $title, $description, $url) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 事件消息内容处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param Object $event_ojb 事件对象。
     * @return string
     */
    protected static function eventMsgHandler($wx_sn, $event_ojb) {
        $from_username = $event_ojb->FromUserName;
        $to_username   = $event_ojb->ToUserName;
        switch (strtolower($event_ojb->Event)) {
            case 'subscribe':
                return self::subscribeEventMsgHandler($wx_sn, $from_username, $to_username);
                break;
            case 'unsubscribe':
                return self::unsubscribeEventMsgHandler($wx_sn, $from_username, $to_username);
                break;
            case 'scan':
                return self::scanEventMsgHandler($wx_sn, $from_username, $to_username);
                break;
            case 'location':
                $latitude  = $event_ojb->Latitude;
                $longitude = $event_ojb->Longitude;
                $precision = $event_ojb->Precision;
                return self::locationEventMsgHandler($wx_sn, $from_username, $to_username, $latitude, $longitude, $precision);
                break;
            case 'click':
                $event_key = $event_ojb->EventKey;
                return self::clickEventMsgHandler($wx_sn, $from_username, $to_username, $event_key);
                break;
            case 'view':
                $event_key = $event_ojb->EventKey;
                return self::viewEventMsgHandler($wx_sn, $from_username, $to_username, $event_key);
                break;
            default :
                return self::otherEventMsgHandler($wx_sn, $from_username, $to_username);
                break;
        }
    }

    /**
     * 关注事件处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @return string
     */
    protected static function subscribeEventMsgHandler($wx_sn, $from_username, $to_username) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 取消关注事件处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @return string
     */
    protected static function unsubscribeEventMsgHandler($wx_sn, $from_username, $to_username) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 扫描二维码事件处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @return string
     */
    protected static function scanEventMsgHandler($wx_sn, $from_username, $to_username) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 上报地理位置事件处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @param string $event_type 事件类型。
     * @param unknown $latitude 纬度。
     * @param unknown $longitude 经度。
     * @param unknown $precision 精度。
     * @return string
     */
    protected static function locationEventMsgHandler($wx_sn, $from_username, $to_username, $latitude, $longitude, $precision) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 点击菜单拉取消息时的事件推送处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @param string $event_key 事件KEY。通过KEY值可以执行特定的响应。
     * @return string
     */
    protected static function clickEventMsgHandler($wx_sn, $from_username, $to_username, $event_key) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 点击菜单跳转链接时的事件推送处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @param string $event_key 事件KEY。通过KEY值可以执行特定的响应。
     * @return string
     */
    protected static function viewEventMsgHandler($wx_sn, $from_username, $to_username, $event_key) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 其他事件的处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @return string
     */    
    protected static function otherEventMsgHandler($wx_sn, $from_username, $to_username) {
        return self::otherMsgHandler($wx_sn, $from_username, $to_username);
    }

    /**
     * 其他消息处理。
     * @param string $wx_sn 系统分配给微信公众号的唯一编号。
     * @param string $from_username 发送方微信号，若为普通用户，则是一个OpenID。
     * @param string $to_username 接收方微信号。
     * @return string
     */
    protected static function otherMsgHandler($wx_sn, $from_username, $to_username) {
        $textTpl = "<xml>"
    			 . "<ToUserName><![CDATA[%s]]></ToUserName>"
    			 . "<FromUserName><![CDATA[%s]]></FromUserName>"
    			 . "<CreateTime>%s</CreateTime>"
    			 . "<MsgType><![CDATA[%s]]></MsgType>"
    			 . "<Content><![CDATA[%s]]></Content>"
    			 . "</xml>";
        $msgType = 'text';
        $contentStr = "Welcome to wechat world!";
        $xml = sprintf($textTpl, $from_username, $to_username, $_SERVER['REQUEST_TIME'], $msgType, $contentStr);
        //YCore::yaf_log(-1, $xml);
        return $xml;
    }

    /**
     * 记录微信公众号消息/事件记录。
     * @param string $developer 开发者账号。
     * @param string $openid openid。
     * @param string $msg_type 消息类型。
     * @param string $event_xml XML原始内容。
     * @return void
     */
    protected static function writeEventLog($developer, $openid, $msg_type, $event_xml) {
        $wx_event_model = new WxEvent();
        $data = [
            'openid'       => $openid,
            'developer'    => $developer,
            'msg_type'     => $msg_type,
            'event_xml'    => $event_xml,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        $wx_event_model->insert($data);
    }
}