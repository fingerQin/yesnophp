<?php
/**
 * 视频消息封装。
 * @author winerQin
 * @date 2016-05-12
 */

namespace winer\WeChat\Message;

class Video extends AbstractMessage {

	/**
	 * 消息类型。
	 * @var string
	 */
	public $MsgType = 'video';

	/**
	 * 视频消息的标题。
	 * @var string
	 */
	protected $Title = '';

	/**
	 * 视频消息的描述
	 * @var string
	 */
	protected $Description = '';

	/**
	 * 通过素材管理中的接口上传多媒体文件，得到的id。
	 * @var number
	 */
	protected $MediaId = 0;

	/**
	 * 非公用部分。
	 * @var string
	 */
	protected $propertys = [
			'Title',
			'Description',
			'MediaId'
	];

	/**
	 * 构造方法。
	 * @param string $Title 视频消息的标题。
	 * @param string $Description 视频消息的描述。
	 * @param string $MediaId 素材ID。
	*/
	public function __construct($Title, $Description, $MediaId) {
		$this->Title       = $Title;
		$this->Description = $Description;
		$this->MediaId     = $MediaId;
	}
}