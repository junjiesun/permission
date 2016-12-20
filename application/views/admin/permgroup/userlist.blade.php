@extends('clayout')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?php echo $permGroupInfo['name'];?> 权限组成员列表</h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/permgroup">权限组管理</a></li>
            <li class="active"><a href="/permgroup/user?gid=<?php echo  $permGroupInfo['permission_group_id'];?>">用户权限</a></li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content"  id="permgroup">
                    <table id="DataTables_Table_0" class="table table-striped table-bordered table-hover dataTables-example dataTable dtr-inline" role="grid" aria-describedby="DataTables_Table_0_info">
                        <thead>
                            <tr role="row">
                                <th class="sorting_asc" >名称</th>
                                <th class="sorting_asc" >操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupUserList as $users){ ?>
                                <tr class="gradeA odd" role="row">
                                    <td class="sorting_1"><?php echo $users->name;?></td>
                                    <td>
                                        <botton class="btn btn-white btn-sm udel"  ><i class="fa fa-trash"></i> 删除 </botton>
                                        <botton class="btn btn-white btn-sm user-perms"  ><i class="fa fa-gears"></i> 权限配置 </botton>
                                        <input type="hidden" name="guid" value="<?= $users->user_id;?>">
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php
                    if(!empty( $groupUserList )){
                        echo $paginateView;
                    }else{
                        echo '<div class="text-center">无数据</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal in" id="permuModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 6px;">
        <div class="modal-dialog">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4><span>葡萄</span>&nbsp;&nbsp;&nbsp;权限配置</h4>
                </div>
                <div class="modal-body ibox">
                    <div class="ibox-content">
                        <div class="panel-body">
                        <div class="panel-group" id="accordion">
                        <?php 
                            foreach ($groupPermission as $controller => $permission){
                        ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <input type="checkbox" name="checkallsub">&nbsp;&nbsp;&nbsp;
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$permission['permission_id']?>" aria-expanded="false" class="collapsed">
                                           <?php echo $permission['name'];?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapse<?=$permission['permission_id']?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="panel-body">
                                         <table id="DataTables_Table_0" class="table table-striped table-bordered table-hover dataTables-example dataTable dtr-inline" role="grid" aria-describedby="DataTables_Table_0_info">
                                                <thead>
                                                    <tr role="row">
                                                        <th class="sorting_asc" >名称</th>
                                                        <th class="sorting_asc" >类型</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(isset($permission['sub']) && !empty($permission['sub'])){ 
                                                        foreach ($permission['sub'] as $row) {
                                                    ?>
                                                        <tr class="gradeA odd" role="row" >
                                                            <td class="sorting_1"><input type="checkbox" name="perm" id="p<?=$row['permission_id'];?>" value="<?=$row['permission_id'];?>">&nbsp;&nbsp;&nbsp;<?php echo $row['name'];?></td>
                                                            <td class="sorting_1">
                                                            <?php
                                                                $type = strtolower($row['type']);
                                                                switch ($type) {
                                                                    case 'menu':
                                                                        echo '菜单';
                                                                        break;
                                                                    case 'page':
                                                                        echo '页面';
                                                                        break;
                                                                    default:
                                                                        echo '服务';
                                                                        break;
                                                                }
                                                            ?></td>
                                                        </tr>
                                                    <?php } }?>
                                                </tbody>
                                            </table>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary save-user-perm">保存</button>
                </div>
                <input type="hidden" name="uid" value="">
                <input type="hidden" name="gid" value="<?=$permGroupInfo['permission_group_id'];?>">
            </div>
        </div>
    </div>
</div>

@section('js')
    @parent
    <link rel="stylesheet" href="/static/css/bootstrap-select.min.css">
    <script type="text/javascript" src="/static/js/permission/getuser.js"></script>
    <script src="/static/js/bootstrap-select.js"></script>

    <script src="/static/js/permission/permgroup.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var permGroup = new PermGroup('#permgroup', '#permuModal');
        });
    </script>

@append
@endsection
