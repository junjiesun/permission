@extends('clayout')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>菜单管理</h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/menu">菜单管理</a></li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="">
                            <a class="btn btn-primary " href="/menu/add">创建菜单</a>
                        </div>
                        <div class="panel-body">
                        <div class="panel-group" id="accordion">
                        <?php 
                            foreach ($menulist as $menuId => $menu){
                        ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$menu['menu_id']?>" aria-expanded="false" class="collapsed">
                                           <?php echo $menu['name'];?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapse<?=$menu['menu_id']?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="panel-body">
                                         <table id="DataTables_Table_0" class="table table-striped table-bordered table-hover dataTables-example dataTable dtr-inline" role="grid" aria-describedby="DataTables_Table_0_info">
                                                <thead>
                                                    <tr role="menu">
                                                        <th class="sorting_asc" >名称</th>
                                                        <th class="sorting_asc" >&nbsp;</th>
                                                        <th class="sorting_asc" >状态</th>
                                                        <th class="sorting_asc" >排序</th>
                                                        <th class="sorting_asc" >描述</th>
                                                        <th class="sorting_asc" >操作</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(isset($menu['sub']) && !empty($menu['sub'])){ 
                                                        foreach ($menu['sub'] as $row) {
                                                    ?>
                                                        <tr class="gradeA odd" role="row" >
                                                            <td class="sorting_1"><?php echo $row['name'];?></td>
                                                            <td class="sorting_1">
                                                            <?php if($row['parent_menu_id']) echo $allMenu[$row['parent_menu_id']].'  /  '.$row['name'];?>
                                                            </td>
                                                            <td class="sorting_1 status">
                                                            <?php if(intval($row['is_close']) === 0){
                                                             echo '<span class="label label-primary">开启</span>';
                                                            }else{
                                                             echo '<span class="label label-warning">关闭</span>';
                                                            }?>
                                                            </td>
                                                            <td class="sorting_1"><?php echo $row['sort'];?></td>
                                                            <td class="sorting_1"><?php echo $row['description'];?></td>
                                                            <td>
                                                                <?php if(intval($row['is_close'])  === 0 ):?>
                                                                    <botton class="btn btn-white btn-sm status-btn"  data-status="1"><i class="fa fa-eye-slash"></i> <span>关闭</span></botton>
                                                                <?php else :?>
                                                                    <botton class="btn btn-white btn-sm status-btn" data-status="0"><i class="fa fa-eye"></i> 开启 </botton>
                                                                <?php endif;?>
                                                                <botton class="btn btn-white btn-sm del"  ><i class="fa fa-trash"></i> 删除 </botton>
                                                                <botton class="btn btn-white btn-sm edit" ><i class="fa fa-edit"></i> 修改 </botton>
                                                                <input type="hidden" name="mid" value="<?=$row['menu_id'];?>">
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
                        <?php
                        if(!empty( $menulist )){
//                            echo $paginateView;
                        }else{
                            echo '<div class="text-center">无数据</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
    </div>
</div>


@section('js')
    @parent

    <script src="/static/js/permission//menu.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var menu = new Menu('.ibox-content');
        });
    </script>
@append
@endsection