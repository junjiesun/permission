@extends('clayout')
@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-sm-12">
            <div class="middle-box text-center animated fadeInRightBig">
                <h3 class="font-bold"><?php echo (isset($showmessage)? $showmessage: '对不起，您没有权限操作该项！');?></h3>
                <div class="error-desc">
                    <div id="showtime"></div>
                    <br><a href="javascript:history.go(-1);" class="btn btn-primary m-t">返回上一页</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var t=3;
    setInterval("refer()",1000);
    function refer(){
        if(t==0){
            location="/";
        }
        document.getElementById('showtime').innerHTML=""+t+"秒后自动刷新跳转";
        t--;
    }
</script>
@endsection
