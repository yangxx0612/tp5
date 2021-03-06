<?php
/**
 * Created by PhpStorm.
 * UserValid: yangxiang
 * Date: 2019-05-13
 * Time: 17:52
 */

namespace app\admin\controller;

use app\common\controller\BaseController;
use app\constants\Common;
use app\constants\ErrorCode;

class GroupController extends BaseController
{
    /**
     * @desc 用户组model
     * @var \app\admin\model\GroupModel
     */
    protected $model;

    /**
     * @desc 初始化函数
     * @return bool|void
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('group');
    }

    /**
     * @desc 用户组列表
     * @link /group/groupList
     */
    public function group_list()
    {
        $params = [];

        //分页信息
        $pageNo = $this->request->param('page_no', Common::FIRST_PAGE);
        $pageSize = $this->request->param('page_size', Common::PAGE_SIZE);

        $data['total'] = $this->model->getCount($params);
        if ($data['total'] > 0) {
            $data['item'] = $this->model->getList($pageNo, $pageSize, $params);
        }

        successJson('查询成功', $data);
    }

    /**
     * @desc 用户组添加
     * @link /group/group_add
     */
    public function group_add()
    {
        //参数校验
        $data = $this->request->param();

        //添加数据
        $flag = $this->model->insertOne($data);
        if ($flag) {
            successJson(ErrorCode::DB_EXEC_FAIL, '添加用户组成功');
        } else {
            errorJson(ErrorCode::DB_EXEC_FAIL, '添加用户组失败,' . $this->model->getError());
        }
    }

    /**
     * @desc 用户组编辑
     * @link /group/groupEdit
     */
    public function group_edit()
    {
        //参数校验
        $data = $this->request->param();

        //判断用户组是否存在
        $groupId = $this->request->param('group_id', 0);
        $info = $this->model->find($groupId);
        if (empty($info)) {
            errorJson(ErrorCode::PARAM_INVALID, '用户组不存在');
        }

        //添加数据
        $flag = $this->model->updateOne($groupId, $data);
        if ($flag) {
            successJson(ErrorCode::DB_EXEC_FAIL, '修改用户组成功');
        } else {
            errorJson(ErrorCode::DB_EXEC_FAIL, '修改用户组失败');
        }
    }
}