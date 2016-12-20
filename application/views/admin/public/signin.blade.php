@extends('layout')
@section('title', '登录')
@section('content')
    @@parent

<div class="middle-box text-center loginscreen  animated fadeInDown" id="signin">
    <div>
        <div>
            <h1 class="logo-name">Mon</h1>
        </div>
        <h3>欢迎使用权限管理Demo</h3>
        <div class="form-group">
            <input type="email" name="username" class="form-control" placeholder="Username" required="">
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Password" required="">
        </div>
        <div class="form-group text-left">
            <input type="checkbox" name="memberPass">
            <label >记住密码</label>
        </div>
        <buttontton name="submit" class="btn btn-primary block full-width m-b">Login</buttontton>
    </div>
</div>

@endsection

@section('js')
    @parent
    <script src="/static/js/permission/signin.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var signin = new Signin('#signin');
        });
    </script>
@append