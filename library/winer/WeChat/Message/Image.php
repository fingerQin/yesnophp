<?php
/**
 * 图片消息封装。
 * @author winerQin
 * @date 2016-05-12
 */

namespace winer\WeChat\Message;

class Image extends AbstractMessage {
    
    /**
     * 消息类型。
     * 
     * @var string
     */
    public $MsgType = 'image';
    
    /**
     * 通过素材管理中的接口上传多媒体文件，得到的id。
     * 
     * @var number
     */
    protected $MediaId = 0;
    
    /**
     * 非公用部分。
     * 
     * @var string
     */
    protected $propertys = [
            'MediaId' 
    ];
    
    /**
     * 构造方法。
     * 
     * @param string $MediaId 素材ID。
     */
    public function __construct($MediaId) {
        $this->MediaId = $MediaId;
    }
}