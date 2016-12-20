@extends('clayout')
@section('content')
<style type="text/css">
td{vertical-align: middle !important;}
</style>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>权限管理</h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/permission">权限管理</a></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content"  id="perm">
                    <div class="">
                        <a class="btn btn-primary " href="/permission/add">添加权限</a>
                    </div>
                    <div class="panel-body">
                        <div class="panel-group" id="accordion">
                        <?php 
                            foreach ($permissionList as $controller => $permission){
                        ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$permission['permission_id']?>" aria-expanded="false" class="collapsed">
                                           <?php echo $permission['name'] .'->'.$permission['controller'];?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapse<?=$permission['permission_id']?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="panel-body">
                                         <table id="DataTables_Table_0" class="table table-striped table-bordered table-hover dataTables-example dataTable dtr-inline" role="grid" aria-describedby="DataTables_Table_0_info">
                                                <thead>
                                                    <tr role="row">
                                                        <th class="sorting_asc" >名称</th>
                                                        <th class="sorting_asc" >Route</th>
                                                        <th class="sorting_asc" >类型</th>
                                                        <th class="sorting_asc" >操作</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(isset($permission['sub']) && !empty($permission['sub'])){ 
                                                        foreach ($permission['sub'] as $row) {
                                                    ?>
                                                        <tr class="gradeA odd" role="row" >
                                                            <td class="sorting_1"><?php echo $row['name'];?></td>
                                                            <td class="sorting_1">
                                                            <?php 
                                                                if(empty($row['method'])){
                                                                    echo $row['controller'];
                                                                }else{
                                                                    echo $row['controller'].'&nbsp;/&nbsp;'.$row['method'];
                                                                }
                                                            ?>
                                                            </td>
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
                                                            <td>
                                                                <div class="ibox-tools" style="float:left;">
                                                                    <a href="javascript:void(0);" data-toggle="dropdown" class="dropdown-toggle" aria-expanded="false">
                                                                    <span class="btn btn-info">权限类型<span class="caret"></span></span>
                                                                    </a>
                                                                    <ul class="dropdown-menu dropdown-user">
                                                                        <li>
                                                                            <?php if($type != 'menu'){ ?>
                                                                            <a href="javascript:void(0);" class="setting" name="assignto" data-type="menu">菜单</a>
                                                                            <?php } ?>
                                                                            <?php if($type != 'page'){ ?>
                                                                            <a href="javascript:void(0);" class="setting" name="assignto" data-type="page">页面</a>
                                                                            <?php } ?>
                                                                            <?php if($type != 'service'){ ?>
                                                                            <a href="javascript:void(0);" class="setting" name="assignto" data-type="service">服务</a>
                                                                            <?php } ?>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <botton class="btn btn-white btn-sm del"  ><i class="fa fa-trash"></i> 删除 </botton>
                                                                <a class="btn btn-white btn-sm edit" href="/permission/edit/<?php echo $row['permission_id'];?>"><i class="fa fa-edit"></i> 修改 </a>
                                                                <input type="hidden" name="pid" value="<?= $row['permission_id'];?>">
                                                            </td>
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
            </div>
        </div>
    </div>
</div>

@section('js')
    @parent
    <script src="/static/js/permission/permission.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var permission = new Permission('#perm');
        });
    </script>
@append

@endsection
