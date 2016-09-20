<?php
/**
 * service基类。
 * @author winerQin
 * @date 2015-10-30
 */
namespace services;

abstract class BaseService {

    /**
     * 计算是否有下一页。
     *
     * @param int $total 总条数。
     * @param int $page 当前页。
     * @param int $count 每页显示多少条。
     * @return bool
     */
    public static function isHasNextPage($total, $page, $count) {
        if (! $total || ! $count) {
            return false;
        }
        $total_page = ceil($total / $count);
        if (! $total_page) {
            return false;
        }
        if ($total_page <= $page) {
            return false;
        }
        return true;
    }

    /**
     * 计算并返回每页的offset.
     *
     * @param int $page 页码。
     * @param int $count 每页显示记录条数。
     * @return int
     */
    public static function getPaginationOffset($page, $count) {
        $count = ($count <= 0) ? 10 : $count;
        $page = ($page <= 0) ? 1 : $page;
        return ($page == 1) ? 0 : (($page - 1) * $count);
    }

}