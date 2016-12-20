/**
 * Created by wangkaihui on 16/8/24.
 */

$(function(){
    $("a").each(
        function(){
            var norefresh = $(this).attr("norefresh");
            if(norefresh) return ;
            var mhref = $(this).attr("href")
            $(this).click(function(){
                $('.J_iframe', window.parent.document).each(function(){
                    if(!$(this).is(":hidden")){
                        var name = $(this).attr("name");
                        $("*[name='"+name+"']",window.parent.document).attr("data-id",mhref)
                    }
                });
            });
        }
    );
})