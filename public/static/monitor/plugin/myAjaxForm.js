/**
 * Created by wangkaihui on 16/8/15.
 */
(function( $ ) {
    $.fn.myAjaxForm = function() {
        this.each(function(){
            var el = $(this);
            var method = el.attr("method");
            method = method?method:"post";
            var url = el.attr("action");
            var options = {
                // target:        "#msg",   // target element(s) to be updated with server response
                beforeSubmit:  showRequest,  // pre-submit callback
                success: showResponse,
                url:       url ,        // override for form's 'action' attribute
                type:      method,        // 'get' or 'post', override for form's 'method' attribute
                dataType:  "json",        // 'xml', 'script', or 'json' (expected server response type)
                clearForm: false ,       // clear all form fields after successful submit
                resetForm: false        // reset the form after successful submit
            };
            // bind form using 'ajaxForm'
            el.ajaxForm(options);
        });

        function showRequest(formData, jqForm, options){
            $(".btn").attr("disabled", true);
            return true;
        }

        function showResponse(responseText, statusText){
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "onclick": null,
                "showDuration": "400",
                "hideDuration": "500",
                "timeOut": "2000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }

            if(responseText.code == 201){
                if(responseText.data){
                    toastr.success(responseText.data);
                    setTimeout(function(){
                        if(responseText.redirect) window.location.assign(responseText.redirect);
                    }, 2000);
                }else{
                    if(responseText.redirect) window.location.assign(responseText.redirect);
                }

            }else if(responseText.code == 200){
                if(responseText.redirect) window.location.assign(responseText.redirect);
            }
            else{
                if(responseText.data) toastr.error(responseText.data);
                $(".btn").attr("disabled", false);
            }
            return false;
        }

    };
})( jQuery );