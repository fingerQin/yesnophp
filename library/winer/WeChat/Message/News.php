<?php
/**
 * 图文消息封装。
 * @author winerQin
 * @date 2016-05-12
 */

namespace winer\WeChat\Message;

class News extends AbstractMessage {
    
    /**
     * 消息类型。
     * 
     * @var string
     */
    public $MsgType = 'news';
    
    /**
     * 图文内容。
     * 
     * @var unknown
     */
    protected $articles = [];
    
    /**
     * 构造方法。
     * 
     * @param string $articles 图文内容。
     *        -- $articles for example start --
     *        $articles = [
     *        [
     *        'Title' => '文章标题',
     *        'Description' => '文章描述',
     *        'PicUrl' => '图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200',
     *        'Url' => '点击图文消息跳转链接',
     *        ],
     *        ]
     *        -- $articles for example end --
     */
    public function __construct($articles) {
        $this->articles = $articles;
    }
    
    /**
     * 创建当前消息对象的XML。
     * 
     * @return string
     */
    public function makeXML() {
        $ArticleCount = count($this->articles);
        $xml = "<xml>" . "<ToUserName><![CDATA[{$this->ToUserName}]]></ToUserName>" .
             "<FromUserName><![CDATA[$this->FromUserName]]></FromUserName>" .
             "<CreateTime>{$this->CreateTime}</CreateTime>" .
             "<MsgType><![CDATA[{$this->MsgType}]]></MsgType>" .
             "<ArticleCount><![CDATA[{$ArticleCount}]]></ArticleCount>" .
             "<Articles>";
        foreach ($this->articles as $article) {
            $xml .= "<item>" . "<Title><![CDATA[{$article['Title']}]]></Title>" .
                 "<Description><![CDATA[{$article['Description']}]]></Description>" .
                 "<PicUrl><![CDATA[{$article['PicUrl']}]]></PicUrl>" .
                 "<Url><![CDATA[{$article['Url']}]]></Url>" .
                 "</item>";
        }
        $xml .= "</Articles></xml>";
        return $xml;
    }
}