<?php

namespace app\admin\controller;

use app\common\controller\BaseController;
use app\constants\Common;
use app\constants\ErrorCode;
use think\Request;

class UserController extends BaseController
{
    /**
     * @desc 用户组model
     * @var \app\admin\model\UserModel
     */
    protected $model;

    /**
     * @desc 初始化函数
     * @return bool|void
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('user');
    }


    /**
     * @desc 查询类标数据
     * @link /user/user_list
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function user_list()
    {
        //用户搜索条件处理
        $params = $this->request->param();

        //分页信息
        $pageNo = $this->request->param('page_no', Common::FIRST_PAGE);
        $pageSize = $this->request->param('page_size', Common::PAGE_SIZE);

        //查询数据
        $data['total'] = $this->model->getCount($params);
        if ($data['total'] > 0) {
            $data['item'] = $this->model->getList($pageNo, $pageSize, $params);
        }

        successJson('查询成功', $data);
    }

    /**
     * @desc 用户添加
     * @link /user/user_add
     */
    public function user_add()
    {
        //获取参数
        $data = $this->request->param();

        //执行写入
        $flag = $this->model->insertOne($data);
        if ($flag) {
            successJson('添加用户成功');
        } else {
            errorJson(ErrorCode::DB_EXEC_FAIL, '添加用户失败,' . $this->model->getError());
        }
    }

    /**
     * @desc 用户编辑
     * @link /user/user_edit
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function user_edit(Request $request)
    {
        //获取参数
        $data = $this->request->param();

        //用户信息处理
        $userId = $request->param('user_id');
        $this->_userInfoExist($userId);

        //执行写入
        $flag = $this->model->updateOne($userId, $data, 'user.edit');
        if ($flag) {
            successJson('修改用户信息成功');
        } else {
            errorJson(ErrorCode::DB_EXEC_FAIL, '修改用户信息失败,' . $this->model->getError());
        }
    }

    /**
     * @desc 用户状态切换
     * @link /user/status_switch
     * @throws \think\exception\DbException
     */
    public function status_switch()
    {
        //参数校验
        $data['status'] = $this->request->param('status');
        $userId = $this->request->param('user_id');
        if (!in_array($data['status'], [Common::SWITCH_OPEN, Common::SWITCH_CLONE])) {
            errorJson(ErrorCode::PARAM_INVALID, '参数不合法');
        }

        //用户信息处理
        $user = $this->_userInfoExist($userId);

        //重复操作
        if ($user->status == $data['status']) {
            errorJson(ErrorCode::PARAM_INVALID, '重复操作');
        }

        //更新数据
        $flag = $this->model->updateOne($userId, $data, 'user.switch_status');
        if ($flag) {
            successJson('用户状态修改成功');
        } else {
            errorJson(ErrorCode::DB_EXEC_FAIL, '用户状态修改失败');
        }
    }

    /**
     * @desc 用户删除
     * @link /user/user_del
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function user_del()
    {
        $userId = $this->request->param('user_id');
        $user = $this->_userInfoExist($userId);

        //删除数据
        $flag = $user->delete();
        if ($flag) {
            successJson('删除用户成功');
        } else {
            errorJson(ErrorCode::DB_EXEC_FAIL, '删除用户失败');
        }
    }


    /**
     * @desc 验证用户信息是否存在
     * @param $userId
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function _userInfoExist($userId)
    {
        if (empty($userId)) {
            errorJson(ErrorCode::PARAM_INVALID, 'user_id参数不合法');
        }

        //用户信息查询
        $user = $this->model->find($userId);
        if (empty($user)) {
            errorJson(ErrorCode::PARAM_INVALID, '用户信息不存在');
        }

        return $user;
    }

}