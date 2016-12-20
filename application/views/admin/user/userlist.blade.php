@extends('clayout')
@section('content')
{{--<script src="/static/js/plugins/jsTree/jstree.min.js"></script>--}}
<link rel="stylesheet" type="text/css" href="/static/css/plugins/jsTree/style.min.css">
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>用户管理</h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/user">用户管理</a></li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row" id="userlist">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="form-inline"  style="padding-bottom:30xp;">
                        <form action="/user" method="get">
                            <div class="form-group">
                                <label for="exampleInputName2">用户名</label>
                                <input type="text" name="n" class="form-control" placeholder="" value="<?php echo $username;?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail2">邮箱</label>
                                <input type="text" name="e" class="form-control" placeholder="xxxx@mail.com" value="<?php echo $email;?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputName2">可否登录</label>
                                <select class="form-control" name="cl">
                                    <option value="all" <?php if($canLogin == 'all') echo 'selected="selected"';?>>全部</option>
                                    <option value="true" <?php if($canLogin == 'true') echo 'selected="selected"';?>>是</option>
                                    <option value='false' <?php if($canLogin === 'false') echo 'selected="selected"';?> >否</option>
                                </select>
                            </div>
                            <div class="form-group">　
                                <button type="submit" class="btn btn-info">搜索</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="">
                        <a class="btn btn-primary " href="/user/add">添加用户</a>
                    </div>
                    
                    <table id="DataTables_Table_0" class="table table-striped table-bordered table-hover dataTables-example dataTable dtr-inline" role="grid" aria-describedby="DataTables_Table_0_info">
                        <thead>
                            <tr role="row">
                                <th class="sorting_asc" >姓名</th>
                                <th class="sorting_asc" >邮箱</th>
                                <th class="sorting_asc" >状态</th>
                                <th class="sorting_asc" >操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userlist as $users){ ?>
                                <tr class="gradeA odd" role="row">
                                    <td class="sorting_1"><?php echo $users->name;?></td>
                                    <td><?php echo $users->email;?></td>
                                    <td class='status'><?php if(intval($users->can_login) === 1){
                                       echo '<span class="label label-primary">开启</span>';
                                   }else{
                                       echo '<span class="label label-warning">关闭</span>';
                                   }?></td>
                                   <td>
                                       <?php if(intval($users->can_login)  === 1 ):?>
                                       <botton class="btn btn-white btn-sm close-btn"><i class="fa fa-eye-slash"></i> <span>关闭</span></botton>
                                   <?php else :?>
                                   <botton class="btn btn-white btn-sm open-btn"><i class="fa fa-eye"></i> 开启 </botton>
                               <?php endif;?>
                               <botton class="btn btn-white btn-sm del"><i class="fa fa-trash"></i> 删除 </botton>
                               <botton class="btn btn-white btn-sm perm"><i class="fa fa-cog"></i> 权限 </botton>
                           <input type="hidden" name="uid" value="<?= $users->user_id;?>">
                            </td>
                           </tr>
                           <?php } ?>
                       </tbody>
                   </table>
               <?php if(!empty($userlist)){
                    echo $paginateView;
                }else{
                    echo '<h4 style="text-align:center;">暂无记录<h4>';
                }?>
            </div>
         </div>
    </div>
</div>
</div>
</div>


@section('js')
    @parent
    <script src="/static/js/permission/userlist.js"></script>
    <script src="/static/js/plugins/layer/layer.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var user = new userList('#userlist');
            $('.tip').mouseover(function(){
                obj = $(this);
                layer.tips(obj.attr('data-title'), obj, {
                    tips: [2, '#000000'],
                    time: 2000
                });
            })
        });
    </script>

@append


@endsection