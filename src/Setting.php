<?php

namespace Yxs\SsoCompany;

use Dcat\Admin\Extend\Setting as Form;

class Setting extends Form
{
    public function form()
    {
        $this->text('checkUrl','校验url')->required();

        $this->select('role_id', '默认角色')
            ->options(function () {
                $roleModel = config('admin.database.roles_model');

                return $roleModel::all()->pluck('name', 'id');
            })->required();
        $this->radio('is_create','是否创建')->options(['1'=>'创建','-1'=>'不创建'])->default(1);
        $this->text('id','id_key')->required();
//        $this->text('key2')->required();
    }
}
