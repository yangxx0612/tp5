<?php
/**
 * Created by PhpStorm.
 * UserValid: yangxiang
 * Date: 2019-05-14
 * Time: 16:41
 */

namespace app\admin\controller;


use app\common\controller\BaseController;
use app\constants\ErrorCode;

class MenuController extends BaseController
{
    /**
     * @var \app\admin\model\MenuModel
     */
    protected $model;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('menu');
    }

    public function menuList()
    {

    }

    /**
     * @desc 添加菜单
     * @link /menu/menu_add
     */
    public function menu_add()
    {
        //获取参数
        $data = $this->request->param();

        //执行写入
        $return = service('menu')->save($data);
        if ($return['code'] == ErrorCode::RET_SUCCESS) {
            successJson('添加菜单成功');
        } else {
            errorJson($return['code'], $return['msg']);
        }
    }

    /**
     * @desc 添加菜单
     * @link /menu/menu_edit
     */
    public function menu_edit()
    {
        //获取参数
        $data = $this->request->param();
        $menuId = $this->request->param('menu_id', 0);

        $info = $this->model->find($menuId);
        if (empty($info)) {
            errorJson(ErrorCode::PARAM_INVALID, '菜单详情不存在');
        }

        //执行写入
        $flag = $this->model->updateOne($menuId, $data);
        if ($flag) {
            successJson('修改菜单成功');
        } else {
            errorJson(ErrorCode::DB_EXEC_FAIL, '修改菜单失败,' . $this->model->getError());
        }
    }
}