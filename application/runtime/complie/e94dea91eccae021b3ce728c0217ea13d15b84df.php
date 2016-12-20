<?php $__env->startSection('content'); ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?php echo $type === 'ADD' ? '创建权限组':'修改权限组';?></h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/permgroup">权限组管理</a></li>
            <?php
            if($type === 'ADD'){
                ?>
                <li class="active"><a href="/permgroup/add">创建权限组</a></li>
                <?php
            }else{
                ?>
                <li class="active"><a href="/permgroup/edit/<?php echo $permGroupInfo['permission_group_id'];?>">修改权限组</a></li>
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
                        <div class="form-group"><label class="col-sm-2 control-label">名称</label>
                            <div class="col-sm-10"><input type="text" value="<?php if($type=='EDIT'){ echo $permGroupInfo['name'];}?>" name="perm_group_name" class="form-control"></div>
                        </div>
                        <div class="form-group"><label class="col-sm-2 control-label">权限列表</label>
                            <div class="col-sm-10">
                                <label class=""> 
                                    <input type="checkbox" name="checkall">全选&nbsp;&nbsp;&nbsp;
                                </label>
                                <div class="panel-body">
                                <div class="panel-group" id="accordion">
                                <?php 
                                    foreach ($permissionList as $controller => $permission){
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
                        <div class="form-group"><label class="col-sm-2 control-label">描述</label>
                            <div class="col-sm-10"><input type="text" value="<?php if($type=='EDIT'){ echo $permGroupInfo['description'];}?>" name="description" class="form-control"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <!-- <button type="submit" class="btn btn-white cancel">取消</button> -->
                                <button type="submit" class="btn btn-primary save">保存</button>
                            </div>
                        </div>
                        <input type="hidden" value="<?php echo $type;?>" name="type"/>
                        <input type="hidden" value="<?php if($type=='EDIT'){ echo $permGroupInfo['permission_group_id'];}?>" name="perm_group_id"/>
                        <input type="hidden" value="<?php if($type=='EDIT'){ echo json_encode($permGroupInfo['permissionIds']);}?>" name="perm_ids"/>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<?php $__env->startSection('js'); ?>
    @parent
    <script src="/static/js/permission/permgroup.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var permGroup = new PermGroup('#form');
        });
    </script>
<?php $__env->appendSection(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('clayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>