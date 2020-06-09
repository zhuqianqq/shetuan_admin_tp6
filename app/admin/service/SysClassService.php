<?php
declare (strict_types=1);

namespace app\admin\service;


use app\admin\util\JwtUtil;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use app\admin\model\BaseModel;

/**
 * 班级
 * Class SysClassService
 * @package app\service
 */
class SysClassService
{
    /**
     * 班级列表
     * @param array $param 参数数组
     * @return json
     */
    public static function getSysClassList($param)
    {

        $model = Db::table('sys_user')->alias('sy');
        if (!empty($param['condition'])) {
            $where = 'sy.account like "%' . $param['condition'] . '%" or sy.user_name like "%' . $param['condition'] . '%" or sy.mobile like "%' . $param['condition'] . '%"';
            $model->where($where);
        }
        $res = $model->field('sy.user_id as userId,sy.account,sy.user_name as userName,sy.mobile,sy.user_type as userType,sy.school_id as schoolId,sy.create_time as createTime')
            ->paginate(['page' => $param['page'], 'list_rows' => $param['pageSize']])->toArray();
        if (empty($res)) {
            return json_ok((object)array(), 0);
        }
        $list = ['total' => $res['total'], 'currentPage' => $res['current_page'], 'lastPage' => $res['last_page'], 'data' => $res['data']];
        return json_ok($list, 0);
    }


    /**
     * 班级详情
     * @param string $sysUserId 管理员ID
     * @return json
     */
    public static function sysClassDetails($sysClassId)
    {
        $res = Db::table('st_class')->alias('sc')->field('sc.class_id as classId,sc.class_name as className,sc.grade,sy.school_id as schoolId')->where('class_id', $sysClassId)->find();
        if (empty($res)) {
            return json_ok((object)array(), 0);
        }
        return json_ok($res, 200);
    }

    /**
     * 班级删除
     * @param string $sysUserId 管理员ID
     * @return json
     */
    public static function sysClassDelete($sysClassId)
    {

        BaseModel::beginTrans();
        try {
            if (strpos($sysClassId, ',') !== false) {

                Db::table('st_class')->where('class_id', 'in', $sysClassId)->update(['enable' => '2']);
            } else {
                Db::table('st_class')->where('class_id', $sysClassId)->update(['enable' => '2']);
            }
        } catch (\Exception $e) {
            BaseModel::rollbackTrans();
            return json_error(100, $e->getMessage());
        }
        BaseModel::commitTrans();
        return json_ok((object)array(), 200);
    }

    /**
     * 班级编辑
     * @param array $param 参数数组
     * @return json
     */
    public static function sysClassUpdate($param)
    {
        $isSysClassId = Db::table('st_class')->alias('sc')->where('class_id', $param['sysClassId'])->find();
        if (empty($isSysClassId)) {
            return json_error(100, '记录不存在');
        }
        BaseModel::beginTrans();
        try {
            $data = [];
            $data['class_name'] = $param['class_name'];
            $data['enable'] = $param['enable'];
            $data['grade'] = $param['grade'];
            Db::name('st_class')->where('class_id', $param['sysClassId'])->data($data)->update();
        } catch (\Exception $e) {
            BaseModel::rollbackTrans();
            return json_error(100, $e->getMessage());
        }
        BaseModel::commitTrans();
        return json_ok((object)array(), 200);
    }

    /**
     * 班级新增
     * @param array $param 参数数组
     * @return json
     */
    public static function sysClassAdd($param)
    {

        $isClass = Db::table('st_class')->alias('sc')->where('class_name', $param['className'])->find();
        if ($isClass && $isClass['enable'] == 1) {
            return json_error(100, '班级名称已存在');
        }
        BaseModel::beginTrans();
        try {
            $data = [];
            $data['class_name'] = $param['className'];
            $data['grade'] = $param['grade'];

            Db::name('st_class')->save($data);
        } catch (\Exception $e) {
            BaseModel::rollbackTrans();
            return json_error(100, $e->getMessage());
        }
        BaseModel::commitTrans();
        return json_ok((object)array(), 200);
    }
}
