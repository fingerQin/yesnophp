<?php
/**
 * 一元购活动参与历史表表。
 * @author winerQin
 * @date 2016-06-28
 */

namespace models;

class GmYygHistory extends DbBase {
    
    /**
     * 表名。
     * 
     * @var string
     */
    protected $_table_name = 'gm_yyg_history';
    
    /**
     * 分表数量
     * 
     * @var number
     */
    protected $_split_table_count = 5;
}