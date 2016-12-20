showmodal = function(text)
{
    if(text == '') return false;
    if($("#dialog-msg")){ $("#dialog-msg").remove();}
    text = text.toString();
    alertModal = '<div class="modal fade" id="dialog-msg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
    alertModal +='<div class="modal-dialog" role="document">';
    alertModal +='<div class="modal-content">';
    alertModal +='<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title">提示信息</h4></div>';
    alertModal +='<div class="modal-body" name="content">';
   
    if(text.indexOf('成功') >= 0){
        msg = '<span class="label label-success">Success!  </span>';
    }else{
        msg = '<span class="label label-warning">Error !</span>';
    }
    alertModal += msg+ "&nbsp;&nbsp;&nbsp;" + text;
    alertModal +='</div>';
    alertModal +='</div>';
    alertModal +='</div>';
    alertModal +='</div>';
    $(alertModal).modal('show');
    setTimeout(function(){$("#dialog-msg").modal('hide'); }, 1500);
}
