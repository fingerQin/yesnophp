<?php
/**
 * 文件表。
 * @author winerQin
 * @date 2015-11-13
 */

namespace models;

class Files extends DbBase {

	/**
	 * 表名。
	 * @var string
	 */
	protected $_table_name = 'ms_files';

    /**
     * 获取文件列表。
     * @param number $user_type 用户类型：－1全部、1管理员、2普通用户 。
     * @param number $user_id 用户ID或管理员ID。
     * @param string $file_md5 文件md5值。
     * @param number $file_type 文件类型：1-图片、2-其他文件。
     * @param string $start_time 文件上传时间开始。
     * @param string $end_time 文件上传时间截止。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
	public function getList($user_type, $user_id, $file_md5, $file_type, $start_time , $end_time, $page, $count) {
	    $offset  = $this->getPaginationOffset($page, $count);
	    $columns = ' * ';
	    $where   = ' WHERE status = :status ';
	    $params = [
	        ':status' => 1,
	    ];
	    if (strlen($file_md5) > 0) {
	        $where .= ' AND file_md5 = :file_md5 ';
	        $params[':file_md5'] = $file_md5;
	    }
	    if (strlen($start_time) > 0) {
	        $where .= ' AND created_time > :start_time ';
	        $params[':start_time'] = $start_time;
	    }
	    if (strlen($end_time) > 0) {
	        $where .= ' AND created_time < :end_time ';
	        $params[':end_time'] = $end_time;
	    }
	    if ($file_type != -1) {
	        $where .= ' AND file_type = :file_type ';
	        $params[':file_type'] = $file_type;
	    }
	    switch ($user_type) {
	        case 1:
	        case 2:
	            $where .= ' AND user_type = :user_type AND user_id = :user_id ';
	            $params[':user_type'] = $user_type;
	            $params[':user_id']   = $user_id;
	            break;
	        case -1:
	            break;
	        default: // 查询不到。
	            $where .= ' AND user_type = :user_type AND user_id = :user_id ';
	            $params[':user_type'] = -1;
	            $params[':user_id']   = -1;
	            break;
	    }
	    $order_by = ' ORDER BY file_id DESC ';
	    $sql = "SELECT COUNT(1) AS count FROM {$this->_table_name} {$where}";
	    $sth = $this->link->prepare($sql);
	    $sth->execute($params);
	    $count_data = $sth->fetch();
	    $total  = $count_data ? $count_data['count'] : 0;
	    $sql = "SELECT {$columns} FROM {$this->_table_name} {$where} {$order_by} LIMIT {$offset},{$count}";
	    $sth = $this->link->prepare($sql);
	    $sth->execute($params);
	    $list = $sth->fetchAll();
	    $result = array(
	        'list'   => $list,
	        'total'  => $total,
	        'page'   => $page,
	        'count'  => $count,
	        'isnext' => $this->IsHasNextPage($total, $page, $count),
	    );
	    return $result;
	}

	/**
	 * 获取一组文件。
	 * -- 1、如果取一个不存在的文件。此file_id对应的数据会没有。
	 * @param array $arr_file_id 
	 * @return array
	 */
	public function getFile($arr_file_id) {
		if (empty($arr_file_id)) {
			return [];
		}
		$where_in_params = $this->createWhereIn($arr_file_id);
		$sql = "SELECT file_id,file_name FROM {$this->_table_name} "
			 . "WHERE file_id IN({$where_in_params['question']}) AND status = :status";
		$params = [':status' => 1];
		$params = array_merge($where_in_params['values'], $params);
		$sth = $this->link->prepare($sql);
		$sth->execute($params);
		$result = $sth->fetchAll();
		return $result ? $result : [];
	}

	/**
	 * 删除文件。
	 * @param number $file_id 文件ID。
	 * @return boolean
	 */
	public function deleteFile($file_id) {
	    $files_model = new Files();
	    $data = [
	        'status' => 2,
	    ];
	    $where = [
	        'file_id' => $file_id,
	        'status'  => 1
	    ];
	    return $files_model->update($data, $where);
	}
	
	/**
	 * 添加文件。
	 * @param string $file_name 文件名。
	 * @param int $file_type 文件类型。
	 * @param int $file_size 文件大小。
	 * @param string $file_md5 文件MD5值。
	 * @param int $user_type 用户类型。
	 * @param int $user_id 用户ID。
	 * @return int file_id
	 */
	public function addFiles($file_name, $file_type, $file_size, $file_md5, $user_type = 2, $user_id = 0) {
	    $data = [
	        'file_name'    => $file_name,
	        'file_type'    => $file_type,
	        'file_size'    => $file_size,
	        'file_md5'     => $file_md5,
	        'user_type'    => $user_type,
	        'user_id'      => $user_id,
	        'status'       => 1,
	        'created_time' => $_SERVER['REQUEST_TIME']
	    ];
	    return $this->insert($data);
	}
}