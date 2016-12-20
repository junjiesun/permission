@extends('clayout')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>添加用户</h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/user">用户管理</a></li>
            <li class="active"><a href="/user/add">添加用户</a></li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="form-horizontal" id="form">
                        <div class="form-group"><label class="col-sm-2 control-label">名称</label>
                            <div class="col-sm-10"><input type="text" autocomplete="off" name="username" class="form-control"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">邮箱</label>
                            <div class="col-sm-10"><input type="email" autocomplete="off" name="email" class="form-control"> <span class="help-block m-b-none"></span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">用户类型</label>
                            <div class="col-sm-10">
                                <select name="user_type" class="form-control m-b">
                                    <option value="ADMIN">管理员</option>
                                    <option value="USER">普通用户</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">密码</label>
                            <div class="col-sm-10">
                                <input type="text" autocomplete="off" name="password" class="form-control">
                            <span class="help-block m-b-none">字母开头(6~16位,包涵数字、字母、下划线,区分大小写)</span></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">确认密码</label>
                            <div class="col-sm-10">
                                <input type="text" name="rp-password" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">状态</label>
                            <div class="col-sm-10"><label class="checkbox-inline">
                                <input type="radio" name="can_login"  value="1"> 开启</label> <label class="checkbox-inline">
                                <input type="radio" name="can_login" checked="checked" value="0"> 关闭</label></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        {{--<div class="form-group hide"><label class="col-sm-2 control-label">同步到ldap</label>--}}
                            {{--<div class="col-sm-10"><label class="checkbox-inline">--}}
                                {{--<input type="radio" name="sync_user"  value="1"> 是</label> <label class="checkbox-inline">--}}
                                {{--<input type="radio" name="sync_user" checked="checked" value="0"> 否</label></div>--}}
                        {{--</div>--}}
                        <div class="form-group"><label class="col-sm-2 control-label">添加到公共权限组</label>
                            <div class="col-sm-10"><label class="checkbox-inline">
                                <input type="radio" name="public_group" checked="checked" value="1"> 是</label> <label class="checkbox-inline">
                                <input type="radio" name="public_group"  value="0"> 否</label></div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <!-- <button type="submit" class="btn btn-white cancel">取消</button> -->
                                <button type="submit" class="btn btn-primary save">保存</button>
                            </div>
                        </div>
                        </div>
                    </div>

            </div>
        </div>
    </div>
</div>

@section('js')
    @parent
    <script src="/static/js/permission/adduser.js?720"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var user = new Adduser('#form');
        });
    </script>
@append
@endsection