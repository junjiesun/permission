$(document).ready(function() {
    $(".form").myAjaxForm();
    //刷新
    $("#refresh").click(function(){
        $(".J_iframe").each(function(){
            if(!$(this).is(":hidden")){
                var name = $(this).attr("name");
                var src = $("*[name='"+name+"']").attr("data-id");
                // window.open(src,name,'')
                $("*[name='"+name+"']").attr("src",src)
            }
        });
    });

});