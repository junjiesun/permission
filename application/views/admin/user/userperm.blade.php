@extends('clayout')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>用户权限配置</h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/user">用户管理</a></li>
            <li class="active"><a href="/user/permission/<?php echo $userInfo['user_id'];?>">用户权限配置</a></li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="form-horizontal" id="form">
                        <div class="form-group"><label class="col-sm-2 control-label">姓名</label>
                            <div class="col-sm-10"><input type="text" disabled value="<?php echo $userInfo['name'];?>" name="username" class="form-control"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">权限组</label>
                            <div class="col-sm-10"><label class="checkbox-inline">
                                <?php foreach($permissionGroup as $group):?>
                                    <input type="checkbox" name="perm" value="<?php echo $group->permission_group_id;?>" id="g<?php echo $group->permission_group_id;?>"><?php echo $group->name;?> </label> 
                                    <label class="checkbox-inline">
                                <?php endforeach;?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <!-- <button type="submit" class="btn btn-white cancel">取消</button> -->
                                <button type="submit" class="btn btn-primary save-perm">保存</button>
                            </div>
                        </div>
                        <input type="hidden" name="uid" value="<?php echo $userInfo['user_id'];?>">
                        <input type="hidden" name="gids" value="<?php echo json_encode($userInfo['permissionGIds']);?>">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


@section('js')
    @parent
    <script src="/static/js/permission/userlist.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var user = new userList('.ibox-content');
        });
    </script>
@append
@endsection
