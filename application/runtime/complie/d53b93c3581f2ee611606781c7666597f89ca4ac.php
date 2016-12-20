<?php $__env->startSection('content'); ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>权限组管理</h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/permgroup">权限组管理</a></li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content"  id="permgroup">
                    <div class="">
                        <a class="btn btn-primary " href="/permgroup/add">创建组</a>
                    </div>
                    <table id="DataTables_Table_0" class="table table-striped table-bordered table-hover dataTables-example dataTable dtr-inline" role="grid" aria-describedby="DataTables_Table_0_info">
                        <thead>
                            <tr role="row">
                                <th class="sorting_asc" >名称</th>
                                <th class="sorting_asc" >描述</th>
                                <th class="sorting_asc" >操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            
                            	foreach ($permGrouplist as $group)
                            	{
                               		if ( !$group->is_display )
									{
										continue;
									}
										
                               		echo '<tr class="gradeA odd" role="row">';
                                    echo '<td class="sorting_1" style="vertical-align: middle;">' . $group->name . '</td>';
                                    echo '<td class="sorting_1" style="vertical-align: middle;">' . $group->description . '</td>';
                                    echo '<td>';
									if ( $group->is_editor )
									{
                                    	echo '<botton class="btn btn-white btn-sm del" style="margin: 0 3px;"><i class="fa fa-trash"></i> 删除 </botton>';
                                    	echo '<botton class="btn btn-white btn-sm edit" style="margin: 0 3px;"><i class="fa fa-edit"></i> 修改 </botton>';
                                    	echo '<botton class="btn btn-white btn-sm adduser" style="margin: 0 3px;"><i class="fa fa-group"></i> 添加用户 </botton>';
                                    	echo '<a class="btn btn-white btn-sm" style="margin: 0 3px;" href="/permgroup/user?gid=' . $group->permission_group_id . '"><i class="fa fa-gears"></i> 用户权限 </a>';
                                    }
									else
									{
										echo '<botton class="btn btn-white btn-sm" style="margin: 0 3px; background-color: #f9f9f9; color: #ccc;"><i class="fa fa-trash"></i> 删除 </botton>';
                                    	echo '<botton class="btn btn-white btn-sm" style="margin: 0 3px; background-color: #f9f9f9; color: #ccc;"><i class="fa fa-edit"></i> 修改 </botton>';
                                    	echo '<botton class="btn btn-white btn-sm" style="margin: 0 3px; background-color: #f9f9f9; color: #ccc;"><i class="fa fa-group"></i> 添加用户 </botton>';
                                    	echo '<botton class="btn btn-white btn-sm" style="margin: 0 3px; background-color: #f9f9f9; color: #ccc;"><i class="fa fa-gears"></i> 用户权限 </botton>';
									}
                                	echo '<input type="hidden" name="perm_group_id" value="' . $group->permission_group_id . '">';
                            		echo '</td>';
                            		echo '</tr>';
								}
							?>
                        </tbody>
                    </table>
                    <?php
                    if(!empty( $permGrouplist )){
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
<div class="modal inmodal in" id="permuserModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 6px;">
        <div class="modal-dialog">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4>添加用户至&nbsp;&nbsp;&nbsp;<span>葡萄</span></h4>
                </div>
                <div class="modal-body ibox">
                    <div class="ibox-content">
                        <div class="form-group user-select">
                          <select id="select-user-list" class="selectpicker" data-size='10' data-width="90%" multiple data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                          </select>
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary save-user-group">保存</button>
                </div>
                <input type="hidden" name="type_id" value="">
                <input type="hidden" name="type" value="orginaze">
            </div>
        </div>
    </div>
</div>

<?php $__env->startSection('js'); ?>
    @parent
    <link rel="stylesheet" href="/static/css/bootstrap-select.min.css">
    <script src="/static/js/bootstrap-select.js"></script>

    <script src="/static/js/permission/getuser.js"></script>
    <script src="/static/js/permission/permgroup.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var permGroup = new PermGroup('#permgroup', '#permuserModal');
        });
    </script>
<?php $__env->appendSection(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('clayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>