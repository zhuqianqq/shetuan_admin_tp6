<?php

namespace app\admin\controller;

use app\admin\BaseController;
use app\admin\service\MessageService;
use think\Request;
use app\admin\service\SysClassService;

class Message extends BaseController
{
    /**
     * 消息列表
     * @param  Request $request
     * @return json
     */
    public function messageList(Request $request)
    {
        $param = [];
        $param['page'] = input('page', 1);
        $param['pageSize'] = input('pageSize', 10);
        $param['grade'] = input('grade', '', 'int');
        $param['classId'] = input('classId', '', 'int');
        $resutlt = MessageService::getMessageList($param);

        return json_ok($resutlt);
    }

    /**
     * 班级信息
     */
    public function checkMessage()
    {
        $result = SysClassService::classInfo();
        return json_ok($result);
    }

}
