<?php $__env->startSection('content'); ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?php echo $type === 'ADD' ? '添加权限':'编辑权限';?></h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/permission">权限管理</a></li>
            <?php
            if($type === 'ADD'){
                ?>
                <li class="active"><a href="/permission/add">添加权限</a></li>
                <?php
            }else{
                ?>
                <li class="active"><a href="/permission/edit/<?php echo $permissionInfo->permission_id;?>">编辑权限</a></li>
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
                            <div class="col-sm-10"><input type="text" value="<?php if($type=='EDIT'){ echo $permissionInfo->name;}?>" name="perm_name" class="form-control"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">Controller</label>
                            <div class="col-sm-10"><input type="text" value="<?php if($type=='EDIT'){ echo $permissionInfo->controller;}?>" name="perm_c" class="form-control"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">Method</label>
                            <div class="col-sm-10"><input type="text" value="<?php if($type=='EDIT'){ echo $permissionInfo->method;}?>" name="perm_m" class="form-control"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">类型</label>
                        <div class="col-sm-10">
                            <select name="ptype" class="form-control m-b">
                                <option value="menu">菜单</option>
                                <option value="page">页面</option>
                                <option value="service">服务</option>
                            </select>
                        </div>
                            
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="submit" class="btn btn-primary save">保存</button>
                            </div>
                        </div>
                        <input type="hidden" value="<?php echo $type;?>" name="type"/>
                        <input type="hidden" value="<?php if($type=='EDIT'){ echo $permissionInfo->permission_id;}?>" name="pid"/>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php $__env->startSection('js'); ?>
@parent
<script src="/static/js/permission/permission.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var perm = new Permission('#form');
    });
</script>
<?php $__env->appendSection(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('clayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>