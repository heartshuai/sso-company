<?php

namespace Yxs\SsoCompany\Http\Controllers;

use Dcat\Admin\Http\Controllers\AuthController as BaseAuthController;
use Dcat\Admin\Layout\Content;
use Yxs\SsoCompany\SsoCompanyServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoCompanyController  extends BaseAuthController
{

    private static $checkUrl;

    //登录跳转到对应的
    /**
     * @var mixed
     */
    private static $role_id;
    /**
     * @var mixed
     */
    private static $is_create;
    /**
     * @var mixed
     */
    private static $id;

    public function ssoCompanyLogin(Request $request){
        if ($this->guard()->check()) {
            return redirect($this->getRedirectPath());
        }
        self::$checkUrl=SsoCompanyServiceProvider::setting('checkUrl');
        self::$role_id=SsoCompanyServiceProvider::setting('role_id');
        self::$is_create=SsoCompanyServiceProvider::setting('is_create');
        self::$id=SsoCompanyServiceProvider::setting('id');
        $login_url=route('dcat.admin.sso_company_login');
        $redirect_url=self::$checkUrl.'?ref='.$login_url.
        $id=$request->get(self::$id);
        if(empty($id)){
            return redirect($redirect_url);
        }
        $userInfo=self::getUserInfo($id,$login_url);
        if(empty($userInfo)){
            abort(403,'用户不存在，请联系管理员');
        }
        return $this->loginHandle($userInfo);

    }
    private function loginHandle($userInfo){
        $username=$userInfo->user;
        $email=$userInfo->mail;
        $name=$userInfo->display;
        $userModel = config('admin.database.users_model');
        $userInfo=$userModel::where('username', $username)->first();
        if(empty($userInfo)){
            if(self::$is_create){
                $password=bcrypt($email);
                $user = new $userModel(compact('username', 'password', 'name'));
                $user->save();
                $roles=self::$role_id;
                $roleModel = config('admin.database.roles_model');
                $roleModel=new $roleModel();
                $roleModel->find($roles);
                $user->roles()->attach($roles);
                $userInfo=$userModel::where('username', $username)->first();
            }else{
                abort(403,'用户不存在，且未开启默认新建功能,请联系管理员');
            }
        }
        Auth::guard('admin')->login($userInfo);
        $login_url=route('dcat.admin.sso_company_login');
        return redirect($login_url);
    }
    public function getLogin(Content $content)
    {
        abort(404);

    }
    public function postLogin(Request $request){
        abort(404);
    }



    private static function getUserInfo($id, $ref)
    {
        if (!$id) {
            return false;
        }
        $id_key=self::$id;
        $url = sprintf("%s?$id_key=%s&ref=$ref", self::$checkUrl, $id, urlencode($ref));

        $data = file_get_contents($url);
        if (!$data) {
            return false;
        }
        @$data = json_decode($data);
        if (!$data) {
            return false;
        }
        if (!$data->user || !$data->mail) {
            return false;
        }
        return $data;
    }
}