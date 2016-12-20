@extends('clayout')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>清理缓存</h2>
        <ol class="breadcrumb">
            <li>当前位置：<a href="/clearcache">清理缓存</a></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content" id="clearCache">
                        <button name="clearcache" type="button" class="btn btn-primary" data-toggle="button">清理所有缓存</button>
	                    <div name="results" style="margin-top: 15px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@section('js')
    @parent
    <script src="/static/js/permission/clearcache.js?v=160707"></script>
    <script type="text/javascript">

        $(document).ready(function(){
            var clearCache = new Clearcache('#clearCache');
        });

    </script>
@append

@endsection
