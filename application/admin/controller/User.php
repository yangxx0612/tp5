<?php

namespace app\admin\controller;

use app\admin\service\UserService;
use app\common\controller\Base;
use app\constants\ErrorCode;
use think\Request;

class User extends Base
{
    /**
     * @desc 查询类标数据
     * @link /admin/user/userList
     * @param Request $request
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userList(Request $request)
    {
        $params = [];

        //分页信息
        $pageNo = $request->param('page_no');
        $pageSize = $request->param('page_size');

        $userService = new UserService();
        $data['total'] = $userService->getCount($params);
        if ($data['total'] > 0) {
            $data['item'] = (new UserService())->getList($pageNo, $pageSize, $params);
        }

        return $this->successJson('查询成功', $data);
    }

    /**
     * @desc 用户添加
     * @link /admin/user/userAdd
     * @param Request $request
     */
    public function userAdd(Request $request)
    {
        $data['username'] = $request->param('username');
        $data['password'] = $request->param('password');
        $data['sex'] = $request->param('sex');

        //参数校验
        $result = $this->validate($data, '\app\admin\validate\User');
        if ($result !== true) {
            $this->errorJson(ErrorCode::PARAM_INVALID, $result);
        }


    }
}