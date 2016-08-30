<?php
/**
 * 游戏封装。
 * @author winerQin
 * @date 2016-04-28
 */

namespace services;

use common\YCore;
use models\DbBase;
use models\GmLedou;
use models\GmLedouConsume;
use models\GmGame;
use models\GmBetRecord;
use models\GmBetRecordNumber;
use models\User;

class GameService extends BaseService {

    // 消费类型。
    const CONSUME_TYPE_ADD = 1; // 增加。
    const CONSUME_TYPE_CUT = 2; // 扣减。
    
    /**
     * 单倍投注每手10乐逗。
     * -- 1元充值1000乐逗。
     * @var int
     */
    public static $single_ledou = 10;

    /**
     * 消费乐豆[增加/扣除]。
     * @param number $user_id 用户ID。
     * @param number $ledou 乐豆数量。
     * @param number $consume_type 消费类型。1增加、2扣减。
     * @param number $consume_code 消费编码。
     * @return boolean
     */
    public static function ledouConsume($user_id, $ledou, $consume_type, $consume_code) {
        $ledou_model = new GmLedou();
        $user_ledou_info = $ledou_model->fetchOne([], ['user_id' => $user_id]);
        if ($consume_type == 1) {
            if (empty($user_ledou_info)) {
                $data = [
                    'user_id'      => $user_id,
                    'ledou'        => $ledou,
                    'created_time' => $_SERVER['REQUEST_TIME']
                ];
                $ok = $ledou_model->insert($data);
                if (!$ok) {
                    YCore::exception(-1, '服务器繁忙,请稍候重试');
                }
            } else {
                $data = [
                    'ledou'         => $user_ledou_info['ledou'] + $ledou,
                    'modified_time' => $_SERVER['REQUEST_TIME']
                ];
                $where  = [
                    'user_id' => $user_id,
                    'ledou'   => $user_ledou_info['ledou']
                ];
                $ok = $ledou_model->insert($data);
                if (!$ok) {
                    YCore::exception(-1, '服务器繁忙,请稍候重试');
                }
            }
        } else if ($consume_type == 2) {
            if (empty($user_ledou_info) || $user_ledou_info['ledou'] < $ledou) {
                YCore::exception(-1, '乐豆数量不足');
            }
            $data = [
                'ledou'         => $user_ledou_info['ledou'] - $ledou,
                'modified_time' => $_SERVER['REQUEST_TIME']
            ];
            $where = [
                'user_id' => $user_id,
                'ledou'   => $user_ledou_info['ledou']
            ];
            $ok = $ledou_model->update($data, $where);
            if (!$ok) {
                YCore::exception(-1, '服务器繁忙,请稍候重试');
            }
        }
        $ledou_consume_model = new GmLedouConsume();
        $data = [
            'user_id'      => $user_id,
            'consume_type' => $consume_type,
            'consume_code' => $consume_code,
            'ledou'        => $ledou,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        $ok = $ledou_consume_model->insert($data);
        if (!$ok) {
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        return true;
    }

    /**
     * 获取用户投注记录。
     * @param number $user_id 用户ID。
     * @param string $game_code 游戏编码。
     * @param number $bet_status 中奖状态。0待开奖、1已中奖、2未中奖。
     * @param number $bet_level 中奖等级。
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getBetRecordList($user_id = -1, $game_code = '', $bet_status = -1, $bet_level = -1, $page = 1, $count = 20) {
        $offset     = self::getPaginationOffset($page, $count);
        $from_table = ' FROM ms_bet_record ';
        $columns    = ' * ';
        $where      = ' WHERE 1 ';
        $params     = [];
        if (strlen($game_code) > 0) {
            $game_model = new GmGame();
            $game_info = $game_model->fetchOne([], ['game_code' => $game_code]);
            if (empty($game_info)) {
                YCore::exception(-1, '游戏不存在');
            }
            $where .= ' AND game_id = :game_id ';
            $params[':game_id'] = $game_info['game_id'];
        }
        if ($user_id != -1) {
            $where .= ' AND user_id = :user_id ';
            $params[':user_id'] = $user_id;
        }
        if ($bet_status != -1) {
            $where .= ' AND bet_status = :bet_status ';
            $params[':bet_status'] = $bet_status;
        }
        if ($bet_level != -1) {
            $where .= ' AND bet_level = :bet_level ';
            $params[':bet_level'] = $bet_level;
        }
        $order_by = ' ORDER BY id DESC ';
        $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total  = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
        $user_model = new User();
        foreach ($list as $key => $item) {
            $userinfo = $user_model->fetchOne([], ['user_id' => $item['user_id']]);
            $item['username']    = $userinfo ? $userinfo['username'] : '-';
            $item['mobilephone'] = $userinfo ? $userinfo['mobilephone'] : '-';
            $item['email']       = $userinfo ? $userinfo['email'] : '-';
        }
        $result = array(
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count),
        );
        return $result;
    }

    /**
     * 获取所有游戏。
     * @return array
     */
    public static function getAllGame() {
        $game_model = new GmGame();
        $columns = [
            'game_id',
            'game_name',
            'game_code'
        ];
        return $game_model->fetchAll($columns);
    }

    /**
     * 获取乐豆消费记录。
     * @param number $user_id 用户ID。
     * @param number $consume_type 消费类型：1增加、2扣减
     * @param number $page 当前页码。
     * @param number $count 每页显示条数。
     * @return array
     */
    public static function getLedouConsume($user_id = -1, $consume_type = -1, $page = 1, $count = 20) {
        $offset     = self::getPaginationOffset($page, $count);
        $from_table = ' FROM ms_ledou_consume ';
        $columns    = ' * ';
        $where      = ' WHERE 1 ';
        $params     = [];
        if ($user_id != -1) {
            $where .= ' AND user_id = :user_id ';
            $params[':user_id'] = $user_id;
        }
        if ($consume_type != -1) {
            $where .= ' AND consume_type = :consume_type ';
            $params[':consume_type'] = $consume_type;
        }
        $order_by = ' ORDER BY id DESC ';
        $sql = "SELECT COUNT(1) AS count {$from_table} {$where}";
        $default_db = new DbBase();
        $count_data = $default_db->rawQuery($sql, $params)->rawFetchOne();
        $total  = $count_data ? $count_data['count'] : 0;
        $sql = "SELECT {$columns} {$from_table} {$where} {$order_by} LIMIT {$offset},{$count}";
        $list = $default_db->rawQuery($sql, $params)->rawFetchAll();
        $user_model = new User();
        foreach ($list as $key => $item) {
            $userinfo = $user_model->fetchOne([], ['user_id' => $item['user_id']]);
            $item['username']    = $userinfo ? $userinfo['username'] : '-';
            $item['mobilephone'] = $userinfo ? $userinfo['mobilephone'] : '-';
            $item['email']       = $userinfo ? $userinfo['email'] : '-';
        }
        $result = array(
            'list'   => $list,
            'total'  => $total,
            'page'   => $page,
            'count'  => $count,
            'isnext' => self::IsHasNextPage($total, $page, $count),
        );
        return $result;
    }

    /**
     * 获取投注记录关联的投注号码。
     * @param number $bet_id 投注记录ID。
     * @return array
     */
    protected static function getBetRecordNumber($bet_id) {
        
    }

    /**
     * 用户投注。
     * @param number $user_id 用户ID。
     * @param number $game_id 游戏ID。
     * @param number $bet_ledou 总乐豆数量。
     * @param array $bet_number_or_money 投注号码与乐豆。可以是多行。每行对应一个投注号码与乐豆数量。
     * @return boolean
     */
    public static function userBet($user_id, $game_id, $bet_ledou, $bet_number_or_money) {
        $game_model = new GmGame();
        $game_info = $game_model->fetchOne([], ['game_id' => $game_id]);
        if (empty($game_info)) {
            YCore::exception(-1, '游戏不存在');
        }
        if ($bet_ledou <= 0) {
            YCore::exception(-1, '投注乐豆必须大于0');
        }
        $total_bet_ledou = 0; // 保存每行投注号码的乐豆。
        foreach ($bet_number_or_money as $bet) {
            $bet_times = self::checkBetNumber($game_info['game_code'], $bet['bet_number']);
            if ($bet_times == 0) {
                YCore::exception(-1, '投注号码有误');
            }
            if ($bet['bet_ledou'] != $bet_times * self::$single_ledou) {
                YCore::exception(-1, '投注乐逗数量有误');
            }
            $total_bet_ledou += $bet['bet_ledou'];
        }
        $default_db = new DbBase();
        $bet_record_model = new GmBetRecord();
        $data = [
            'user_id'      => $user_id,
            'game_id'      => $game_id,
            'bet_ledou'    => $bet_ledou,
            'bet_status'   => 0,
            'reward_ledou' => 0,
            'created_time' => $_SERVER['REQUEST_TIME']
        ];
        $default_db->beginTransaction();
        $bet_id = $bet_record_model->insert($data);
        if (!$bet_id) {
            $default_db->rollBack();
            YCore::exception(-1, '服务器异常,请稍候重试');
        }
        $bet_record_number_model = new GmBetRecordNumber();
        foreach ($bet_number_or_money as $bet) {
            $data = [
                'bet_id'       => $bet_id,
                'bet_ledou'    => $bet['bet_ledou'],
                'bet_number'   => $bet['bet_number'],
                'bet_status'   => 0,
                'bet_level'    => 0,
                'created_time' => $_SERVER['REQUEST_TIME']
            ];
            $ok = $bet_record_number_model->insert($data);
            if (!$ok) {
                $default_db->rollBack();
                YCore::exception(-1, '服务器繁忙,请稍候重试');
            }
        }
        $ok = self::ledouConsume($user_id, $total_bet_ledou, self::CONSUME_TYPE_CUT, "cut_{$game_info['game_code']}_bet");
        if (!$ok) {
            $default_db->rollBack();
            YCore::exception(-1, '服务器繁忙,请稍候重试');
        }
        $default_db->commit();
        return true;
    }

    /**
     * 验证投注号码是否合法并且返回投注数量。
     * @param string $game_code 游戏编码。
     * @param string $bet_number 投注号码。
     * @return int
     */
    protected static function checkBetNumber($game_code, $bet_number) {
        switch ($game_code) {
            case 'ssq':
                return self::ssq_check_be_number($bet_number);
                break;
            case 'dlt':
                return self::dlt_check_be_number($bet_number);
                break;
            case 'ssc':
                return self::ssc_check_be_number($bet_number);
                break;
            default:
                YCore::exception(-1, '游戏编码不正确');
                break;
        }
    }

    /**
     * 检查双色球投注号码是否合法并返回投注号码对应多少注。
     * -- 1、拆分红球蓝球。
     * -- 2、拆分红球或蓝球保存于数组中。
     * -- 3、判断每个红球或蓝球是否在指定的数字范围。
     * -- 4、判断红球与蓝球过滤前后号码的数量是否一致。一致说明号码合法。
     * -- 5、计算投注数量。
     * @param string $bet_number 投注号码。
     * @return number
     */
    public static function ssq_check_be_number($bet_number) {
        $ball = explode(':', $bet_number);
        if (count($ball) != 2) {
            return 0;
        }
        $str_red_ball  = $ball[0];
        $str_blue_ball = $ball[1];
    
        $arr_red_ball  = explode(',', $str_red_ball);
        $arr_blue_ball = explode(',', $str_blue_ball);
    
        // 红色球取整调整。
        $_arr_red_ball  = [];
        foreach ($arr_red_ball as $ball) {
            $ball = intval($ball);
            if ($ball < 1 || $ball > 33) {
                return 0;
            }
            $_arr_red_ball[] = $ball;
        }
        // 蓝色球取整调整。
        $_arr_blue_ball = [];
        foreach ($arr_blue_ball as $ball) {
            $ball = intval($ball);
            if ($ball < 1 || $ball > 16) {
                return 0;
            }
            $_arr_blue_ball[] = $ball;
        }
        // 去除重复号码
        $_arr_red_ball  = array_unique($_arr_red_ball);
        $_arr_blue_ball = array_unique($_arr_blue_ball);
        // 去重后号码的数量。
        $red_ball_count  = count($_arr_red_ball);
        $blue_ball_count = count($_arr_blue_ball);
        // 判断过滤前后号码数量是否一致。一致说明是合法的号码。
        if (count($arr_red_ball) != $red_ball_count || $red_ball_count > 20) {
            return 0;
        }
        if (count($arr_blue_ball) != $blue_ball_count || $blue_ball_count > 16) {
            return 0;
        }
        $arr_red_ball  = $_arr_red_ball;
        $arr_blue_ball = $_arr_blue_ball;
        $cycle_times   = 6; // 循环次数。
        $left_val      = $red_ball_count;
        for ($i = 1; $i < $cycle_times; $i++) {
            $_val = $red_ball_count - $i;
            $left_val = $left_val * $_val;
        }
        return ($left_val / (6 * 5 * 4 * 3 * 2 * 1)) * $blue_ball_count;
    }

    /**
     * 检查大乐透投注号码是否合法并返回投注号码对应多少注。
     * -- 1、拆分红球蓝球。
     * -- 2、拆分红球或蓝球保存于数组中。
     * -- 3、判断每个红球或蓝球是否在指定的数字范围。
     * -- 4、判断红球与蓝球过滤前后号码的数量是否一致。一致说明号码合法。
     * -- 5、计算投注数量。
     * @param string $bet_number 投注号码。
     * @return number
     */
    public static function dlt_check_be_number($bet_number) {
        $ball = explode(':', $bet_number);
        if (count($ball) != 2) {
            return 0;
        }
        $str_red_ball  = $ball[0];
        $str_blue_ball = $ball[1];
    
        $arr_red_ball  = explode(',', $str_red_ball);
        $arr_blue_ball = explode(',', $str_blue_ball);
    
        // 红色球取整调整。
        $_arr_red_ball  = [];
        foreach ($arr_red_ball as $ball) {
            $ball = intval($ball);
            if ($ball < 1 || $ball > 35) {
                return 0;
            }
            $_arr_red_ball[] = $ball;
        }
        // 蓝色球取整调整。
        $_arr_blue_ball = [];
        foreach ($arr_blue_ball as $ball) {
            $ball = intval($ball);
            if ($ball < 1 || $ball > 12) {
                return 0;
            }
            $_arr_blue_ball[] = $ball;
        }
        // 去除重复号码
        $_arr_red_ball  = array_unique($_arr_red_ball);
        $_arr_blue_ball = array_unique($_arr_blue_ball);
        // 去重后号码的数量。
        $red_ball_count  = count($_arr_red_ball);
        $blue_ball_count = count($_arr_blue_ball);
        // 判断过滤前后号码数量是否一致。一致说明是合法的号码。
        if (count($arr_red_ball) != $red_ball_count || $red_ball_count > 18) {
            return 0;
        }
        if (count($arr_blue_ball) != $blue_ball_count || $blue_ball_count > 12) {
            return 0;
        }
        $arr_red_ball  = $_arr_red_ball;
        $arr_blue_ball = $_arr_blue_ball;
    
        // 红球组合数量。
        $cycle_times = 5; // 循环次数。
        $left_val    = $red_ball_count;
        for ($i = 1; $i < $cycle_times; $i++) {
            $_val = $red_ball_count - $i;
            $left_val = $left_val * $_val;
        }
        // 蓝球组合数量。
        $cycle_times = 2; // 循环次数。
        $right_val   = $blue_ball_count;
        for ($i = 1; $i < $cycle_times; $i++) {
            $_val = $blue_ball_count - $i;
            $right_val = $right_val * $_val;
        }
        return ($left_val / (5 * 4 * 3 * 2 * 1)) * ($right_val / (2*1));
    }

    /**
     * 检查时时彩投注号码是否合法并返回投注号码对应多少注。
     * -- 1、数字1~30.
     * -- 2、猜中一个号1陪25.
     * @param string $bet_number 投注号码。
     * @return number
     */
    function ssc_check_be_number($bet_number) {
        $arr_ball  = explode(',', $bet_number);
        $_arr_ball  = [];
        foreach ($arr_ball as $ball) {
            $ball = intval($ball);
            if ($ball < 1 || $ball > 30) {
                return 0;
            }
            $_arr_ball[] = $ball;
        }
        // 去除重复号码
        $_arr_ball  = array_unique($_arr_ball);
        // 去重后号码的数量。
        $ball_count  = count($_arr_ball);
        // 判断过滤前后号码数量是否一致。一致说明是合法的号码。
        if (count($arr_ball) != $ball_count) {
            return 0;
        }
        return $ball_count;
    }
}