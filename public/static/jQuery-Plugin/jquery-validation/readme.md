## jQuery Validation Plugin介绍

> - jQuery Validate 插件为表单提供了强大的验证功能，让客户端表单验证变得更简单，同时提供了大量的定制选项，满足应用程序各种需求。该插件捆绑了一套有用的验证方法，包括 URL 和电子邮件验证，同时提供了一个用来编写用户自定义方法的 API。所有的捆绑方法默认使用英语作为错误信息，且已翻译成其他 37 种语言。
> - [ jQuery Validate 官网](http://jqueryvalidation.org/)
> - [GitHub 源码](https://github.com/jzaefferer/jquery-validation)
> - [本教程源码下载 ( Demo1-Demo5 )](https://github.com/oldinaction/git/tree/master/jQuery-Plugin/jquery-validation)

## 使用方法

- 引入JS文件

  ```html
  <script type="text/javascript" src="lib/jquery-1.11.1.js"></script><!-- jquery库文件 -->
  <script type="text/javascript" src="dist/jquery.validate-1.15.0.js"></script><!-- jquery validate核心库文件 -->
  <script type="text/javascript" src="dist/additional-methods.js"></script><!-- jquery validate扩展验证方法 -->
  <script type="text/javascript" src="dist/localization/messages_zh.js"></script><!-- jquery validate错误信息中文提示 -->
  ```

- 写验证规则
- 调用验证方法: `$("#formId").validate();`

## 牛刀小试

### 验证规则通过html标签属性或者class形式定义

如：`<input name="username" class="required" minlength="2" maxlength="4" />`表示“该字段为必须输入, 最小长度为2个字符, 最大长度为4个字符”。其中`class="required"`还可写成`required="true"`(同时支持Html5的required属性, 即省略值true)。

**[Demo1](https://github.com/oldinaction/git/blob/master/jQuery-Plugin/jquery-validation/demo1.html)**

```html
<!DOCTYPE html>
<html>
<head>
    <title>jQuery Validate - 验证规则写在html标签上 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> </head>
<body>
    <form id="formId" method="get" action="">
        <fieldset>
            <legend>表单验证</legend>
            <p>
                <label>Name</label>
                <input name="username" required minlength="2" maxlength="4" /> </p>
            <p>
                <label>E-Mail</label>
                <input name="email" class="email" required/> </p>
            <p>
                <input class="submit" type="submit" value="提交" /> </p>
        </fieldset>
    </form>

    <!-- jquery库文件 -->
    <script type="text/javascript" src="lib/jquery-1.11.1.js"></script>
    <!-- jquery validate核心库文件 -->
    <script type="text/javascript" src="lib/jquery.validate-1.15.0.js"></script>
    <!-- jquery validate错误信息中文提示 -->
    <script type="text/javascript" src="lib/messages_zh.js"></script>
    <script>
        $("#formId").validate();

    </script>
</body>
</html>
```

### 验证规则写到 js 代码中

**[Demo2](https://github.com/oldinaction/git/blob/master/jQuery-Plugin/jquery-validation/demo2.html)**

```html
<!DOCTYPE html>
<html>
<head>
    <title>jQuery Validate - 验证规则写js代码中 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
    <form id="formId" method="get" action="">
        <fieldset>
            <legend>表单验证</legend>
            <p>
                <label class="title">Name</label>
                <input id="name" name="name" />
            </p>
            <p>
                <label class="title">text</label>
                <textarea name="text" cols="22"></textarea>
            </p>
            <p>
                <input class="submit" type="submit" value="提交" />
            </p>
        </fieldset>
    </form>

    <script type="text/javascript" src="lib/jquery-1.11.1.js"></script>
    <script type="text/javascript" src="lib/jquery.validate-1.15.0.js"></script>
    <script type="text/javascript" src="lib/messages_zh.js"></script>
    <script>
        $("#formId").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    remote: { // ajax验证(后台只能返回 true/false )
                        url: 'demo2.php?action=add',
                        type: 'get',
                        data: { // 传入参数
                            name: function() {
                                return $('#name').val();
                            }
                        },
                        beforeSend: function() {
                            console.log(1);
                        },
                        complete: function() {
                            console.log(2);
                        }
                    }
                },
                text: "required" //required的另一种写法
            },
            // 可修改默认的提示信息
            messages: {
                name: {
                    required: "需要输入名称",
                    remote: "用户名已存在"
                },
                text: "需要输入文本内容"
            },
        });

    </script>
</body>
</html>
```

## jQuery Validation插件级别函数

jQuery Validation插件级别函数包括 `validate()` 、 `valid()` 、 `rules()` , 这些函数可直接使用jQuery对象(form表单相关对象)调用。[官方API 文档](http://jqueryvalidation.org/documentation/)

### validate() 的可选参数介绍

- 设置可选参数:

  ```javascript
  // 方式一
  $("#formId").validate({
      debug: false, // 是否开启debug模式: 只验证不提价表单 (默认false)
      rules: {}, // 相应字段的验证规则
      messages: {}, // 验证不通过的提示信息
      ignore: ":hidden", // 忽略某些表单元素不验证(插件默认已经忽略了隐藏元素不验证, 包括display:none的)
      errorClass: 'error', // 类型 String，默认 "error"。指定错误提示的 css 类名，可以自定义错误提示的样式。
      errorElement: 'label', // 类型 String，默认 "label"。指定使用什么标签标记错误
      OnSubmit: true, // 类型 Boolean，默认 true，指定是否提交时验证
      onfocusout: true, // 类型 Boolean，默认 true，指定是否在获取焦点时验证
      onkeyup: true, // 类型 Boolean，默认 true，指定是否在敲击键盘时验证
      onclick: true, // 类型 Boolean，默认 true，指定是否在鼠标点击时验证（一般验证 checkbox、radiobox）
      focusInvalid: true, // 类型 Boolean，默认 true。提交表单后，未通过验证的表单（第一个或提交之前获得焦点的未通过验证的表单）会获得焦点
      focusCleanup: true, // 类型 Boolean，默认 false。当未通过验证的元素获得焦点时，移除错误提示（避免和 focusInvalid 一起使用）
      wrapper: '', // 类型 String，指定使用什么标签再把上边的 errorELement 包起来
      errorLabelContainer: '', // 类型 Selector，把错误信息统一放在一个容器里面
      showErrors: , // 类型 Funciotn，可以显示总共有多少个未通过验证的元素
      highlight: , // 类型 Funciotn，可以给未通过验证的元素加效果、闪烁等
      unhighlight: , // 类型 Funciotn，元素通过验证后去掉效果
      ...更多参数...   
  });
  // 方式二
  $.validator.setDefaults({
      debug: true,
      rules: {},
      ...更多参数...
  });
  ```

- submitHandler: 获取提交句柄

  ```javascript
  submitHandler:function(form){
      alert("提交句柄: 此处可在验证完成后提交表单前进行相关操作!");   
      form.submit();
      // $(form).ajaxSubmit(); // 使用ajax进行提交
  }    
  ```

- invalidHandler: 验证出错时表单控制句柄

  ```javascript
  invalidHandler: function() {
      $( "#info" ).text( validator.numberOfInvalids() + "个字段验证出错" );
  }
  ```

- errorPlacement: 更改错误信息显示的位置

  ```javascript
  errorPlacement: function(error, element) {  
      error.appendTo(element.parent()); // 把错误信息放在验证的元素后面
  }
  ```

- success: 每个字段验证通过后处理

  ```javascript
  // 用法一: 加class
  success: "valid", // 通过验证后给该字段加class属性值valid
  // 用法二: 执行回调函数
  success: function(errorElement) {
      errorElement.addClass("valid").text("Ok!");// 通过验证后在该字段的错误提示信息元素上加valid类并设置text值
  }
  ```

- groups: 对一组元素的验证，用一个错误提示

  ```javascript
  groups:{
  		username:"fname lname"// 还可用 errorPlacement 控制把出错信息放在哪里
  	},
  ```

关于更多参数介绍请前往[官方参数介绍](http://jqueryvalidation.org/validate)

### valid() 函数获取验证状态

```javascript
var $form = $("#formId");
$form.validate(); // 对表单进行验证
$( "button" ).click(function() {
  alert( "Valid: " + $form.valid() ); // 如果验证通过 $form.valid() 返回 true
});
```

### rules() 函数给字段增加/删除验证规则

```javascript
$( "#myinput" ).rules(); // 返回元素的验证规则
// 给"#myinput"增加一个最少输入2个字符的验证
$( "#myinput" ).rules( "add", {
  required: true,
  minlength: 2
});
$( "#myinput" ).rules( "remove" ); // 移除所有验证
$( "#myinput" ).rules( "remove", "min max" ); // 移除最小 最大值验证
```

## Validator对象

Validator对象可通过 `var validator = $( "#formId" ).validate();` 获取(此时并没有进行验证, 验证的触发在提交表单、表单字段值发生变化等情况下发生), 他包含一些公共方法和静态方法。

- 公用方法

  ```javascript
  validator.form(); // 验证表单, 返回 true/false
  validator.element( "#myinput" ); // 验证某个表单元素, 返回 true/false
  validator.resetForm(); // 重置验证表单状态
  validator.showErrors({ // 不管该字段是否通过验证, 都展示提示信息
    "username": "用户名是必填箱哦"
  });
  validator.numberOfInvalids(); // 经过验证后, 获取验证不通过的字段个数
  ```

- 静态方法

  ```javascript
  $.validator.addMethod( name, method [, message ] ); // 自定义验证方法
  $.validator.format( template, argument, argumentN… ); // 提示信息解析函数
  $.validator.setDefaults( options ); // 设置默认参数
  $.validator.addClassRules( name, rules ); // 增加css类规则, 只要是这个类的字段都进行相关验证。只有当常规验证通过后才会验证加了class的字段
  ```

  实例 ( 更多: **[Demo3](https://github.com/oldinaction/git/blob/master/jQuery-Plugin/jquery-validation/demo3.html)** )

  ```javascript
  var template = jQuery.validator.format("{0} 不是一个有效值");
  alert(template("abc"));// abc不是一个有效值

  $.validator.addClassRules({
    mydate: {
      required: true,
    },
  });
  ```

- $.validator.methods.XXX.call();

如：`$.validator.methods.digits.call(this, value, element)` 在自定义函数中常需要调用另外一个验证函数。this指$.validator, value 和 element 是 digits 函数需要接受的参数

## 验证函数

### 插件内置验证函数

名称  |  描述 ( 返回值类型均为Boolean )
--|--
required()  |  必填验证元素
required(dependency-expression)  |  必填元素依赖于表达式的结果(required: "#mycheck:checked" 表达式的值为真，则需要验证。)
required(dependency-callback)  |  必填元素依赖于回调函数的结果(返回为真，表示需要验证)
remote(url)  |  请求远程校验
minlength(length)  |  设置最小长度
maxlength(length)  |  设置最大长度
rangelength(range)  |  设置一个长度范围 [min,max]
min(value)  |  设置最小值
max(value)  |  设置最大值
range(range)  |  设置值的范围
email()  |  验证电子邮箱格式
url()  |  验证 URL 格式
date()  |  验证日期格式（类似 30/30/2008 的格式，不验证日期准确性只验证格式）
dateISO()  |  验证 ISO 类型的日期格式
digits()  |  验证自然数(0、1、2)
number()  |  验证十进制数字(-1.2、0、1、1.5)
equalTo(other)  |  验证两个输入框的内容是否相同

> **radio、checkbox、select** 的验证：
>
> - radio的required表示必须选中一个
> - checkbox的required表示必须选中
> - select的required表示选中的value不能为空
> - checkbox的minlength表示必须选中的最小个数,maxlength表示最大的选中个数,rangelength:[2,3]表示选中个数区间
> - select(multiple="multiple")的minlength表示选中的最小个数（可多选的select）,maxlength表示最大的选中个 数,rangelength:[2,3]表示选中个数区间


### 插件扩展验证函数(更多请看`additional-methods.js`)

名称  |  描述 ( 返回值类型均为Boolean )
--|--
accept(extension) |  验证相同后缀名的字符串
creditcard()  |  验证信用卡号
phoneUS() |  验证美式的电话号码
ipv4()  |  验证ipv4地址
...  |  ...

### $.validator.addMethod() 自定义验证方法

验证函数可以写在html页面, 也可以写在 `additional-methods.js` 文件中, 对于大型项目第二种常用。(实例: **[Demo4](https://github.com/oldinaction/git/blob/master/jQuery-Plugin/jquery-validation/demo4.html)**)

- 方法的定义

  ```javascript
  // value: 当前元素的值 element: 当前元素
  $.validator.addMethod("domain", function(value, element) {
    return this.optional(element) || /^http:\/\/www.aezo.cn\//.test(value);
  }, "链接请以本网站地址http://www.aezo.cn/开头");

  // params传递的额外参数
  $.validator.addMethod("value", function(value, element, params) {
    var flag = false;
    if($.validator.methods.digits.call(this, value, element)) {
        var a = parseInt($(params[0]).val());
        var b = parseInt($(params[1]).val());
        if(value == a + b) {
          flag = true;
        }
    }
    return flag;
  }, $.validator.format("请正确输入 {0} + {1} 的值"));
  ```

- 方法的调用

  ```javascript
  $("#formId").validate({
      rules: {
          site: {
            domain: true,
          },
          value: {
            required: true,
            value: ["input[name='a']", "input[name='b']"],
          }
      }
  });
  ```

## jQuery Validation 使用心得

### 关于触发元素验证

我们一般是使用 `onchange` 事件做表单验证, 就是输入框的值发生改变才验证。而 jQuery Validation 暂时不支持此种方式来验证，他是使用点击、元素获取失去焦点、键盘按下等来进行触发验证的。一般我们的新增和修改写在同一个页面，这就产生了一个问题，有这样一种情况：我们要判断某个用户进行用户名修改时数据库中是否有重复的。当我们进到修改页面，不修改用户名，只是让他获取焦点后再失去焦点，这样插件就会提示验证不通过。解决办法是自己写一个扩展方法，将该字段原来的值以参数的形式传入，然后和现在的值进行比较。部分代码如下，具体见 **[Demo5](https://github.com/oldinaction/Git/blob/master/jQuery-Plugin/jquery-validation/demo5.html)** (可与[Demo2](https://github.com/oldinaction/git/blob/master/jQuery-Plugin/jquery-validation/demo2.html)对比)

```html
<script>
    $.validator.addMethod("checkName", function(value, element, params) {
        var flag = false;
        if(params == value) {
            flag = true;
        } else {
            $.ajax({
                url: 'demo5.php?action=add',
                type: 'get',
                data: {"name": value},
                async: false, // 关闭异步比较重要, 否则 flag 返回一直是 false
                success: function(data) {
                    if(data == 'true') { // 存在此用户名
                        flag = false;
                    } else if(data == 'false') { // 不存在此用户名
                        flag = true;
                    } else {
                        console.log("后台出错");
                    }
                },
                error: function() {
                    console.log("ajax出错");
                }
            });
        }
        return flag;
    }, "此用户名已经存在");

    $("#formId").validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
                checkName: $("input[name='name']").val(),
            },
        },
    });
</script>
```



> 原文地址
> - [http://www.cnblogs.com/oldinaction/p/5361176.html](http://www.cnblogs.com/oldinaction/p/5361176.html)
>
> 参考网址
> - [http://jqueryvalidation.org/documentation/](http://jqueryvalidation.org/documentation/)
> 
> - [http://www.runoob.com/jquery/jquery-plugin-validate.html](http://www.runoob.com/jquery/jquery-plugin-validate.html)
