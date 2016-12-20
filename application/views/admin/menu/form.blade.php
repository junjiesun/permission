@extends('clayout')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?php echo $type === 'ADD' ? '创建菜单':'编辑菜单';?></h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/menu">菜单管理</a></li>
            <?php
            if($type === 'ADD'){
            ?>
                <li class="active"><a href="/menu/add">创建菜单</a></li>
            <?php
            }else{
            ?>
                <li class="active"><a href="/menu/edit/<?php echo $menuInfo->menu_id;?>">编辑菜单</a></li>
            <?php
            }
            ?>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="form-horizontal" id="form">
                        <div class="form-group"><label class="col-sm-2 control-label">父级菜单</label>
                            <div class="col-sm-10">
                                <select name="parent_menu_id" class="form-control m-b">
                                        <option value="0">顶级</option>
                                        <?php echo $parentMenu;?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group"><label class="col-sm-2 control-label">名称</label>
                            <div class="col-sm-10"><input type="text" value="<?php if($type=='EDIT'){ echo $menuInfo->name;}?>" name="menu_name" class="form-control"></div>
                        </div>
                         <div class="form-group"><label class="col-sm-2 control-label">绑定权限</label>
                            <div class="col-sm-10">
                                <div class="panel-body">
                                <div class="panel-group" id="accordion">
                                <?php 
                                    foreach ($permissionList as $controller => $permission){
                                ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h5 class="panel-title">
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
                                                                    <td class="sorting_1"><input type="radio" name="perm_id" id="p<?=$row['permission_id'];?>" value="<?=$row['permission_id'];?>">&nbsp;&nbsp;&nbsp;<?php echo $row['name'];?></td>
                                                                    <td class="sorting_1">
                                                                    <?php
                                                                        $types = strtolower($row['type']);
                                                                        switch ($types) {
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
                        </div>
                       <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">排序（同级下数字越大越靠后）</label>
                            <div class="col-sm-10"><input type="text" value="<?php if($type=='EDIT'){ echo $menuInfo->sort;}?>" name="sort" class="form-control"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">ICON图标（默认 fa-th-large）</label>
                            <div class="col-sm-10"><input type="text" value="<?php if($type=='EDIT'){ echo (isset($menuInfo->icon)? $this->menuInfo->icon: '');}?>" name="icon" class="form-control"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">描述</label>
                            <div class="col-sm-10"><input type="text" value="<?php if($type=='EDIT'){ echo $menuInfo->description;}?>" name="description" class="form-control"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">状态</label>
                            <div class="col-sm-10">
                            <label class="checkbox-inline"><input type="radio" name="is_open" value="0" <?php if($type=='EDIT'){ echo $menuInfo->is_close == 0 ? 'checked="checked"':'';}?> > 开启</label>
                            <label class="checkbox-inline"><input type="radio" name="is_open" value="1" <?php if($type=='EDIT'){ echo $menuInfo->is_close == 1 ? 'checked="checked"':'';}?>> 关闭</label></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <!-- <button type="submit" class="btn btn-white cancel">取消</button> -->
                                <button type="submit" class="btn btn-primary save">保存</button>
                            </div>
                        </div>
                        <input type="hidden" value="<?php echo $type;?>" name="type"/>
                        <input type="hidden" value="<?php if($type=='EDIT'){ echo $menuInfo->menu_id;}?>" name="menu_id"/>
                        <input type="hidden" value="<?php if($type=='EDIT'){ echo $menuInfo->parent_menu_id;}?>" name="parent_menu_id"/>
                        <input type="hidden" value="<?php if($type=='EDIT'){ echo $menuInfo->permission_id;}?>" name="have_perm_id"/>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@section('js')
    @parent

    <script src="/static/js/permission/menu.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var menu = new Menu('#form');
        });
    </script>
@append

@endsection