// JavaScript Document
//===== BEGIN: class Activity =====/
function Topic( domSelector , submitAdd)
{
    this.domSelector = domSelector;
    this.submitAdd = submitAdd;
    this.lock = false;
    this.parentNode = null;
    this.domReady();
}
Topic.prototype.domReady = function()
{
    var ui = $(this.domSelector);
    var uiMode = $(this.submitAdd);
    var self = this;

    $(ui).on('click','[name=createActivityType]',function(){
        $('[name=activityTypeAdd]').modal('show');
    });

    $(ui).on('click','[name=editActivityType]',function(){
        $('[name=activityTypeEdit]').modal('show');
    });

    $('[name=submit]',uiMode).click(function(){
        var topic_name = $('[name=topic_name]').val();
        var active_topic_id = $('[name=active_topic_id]').val();
        var type = $('[name=type]',uiMode).children('option:selected').val();
        var data = [];
        var errorMessage = false;

        if(topic_name == ''){ alert('请添加题目名'); }

        switch(type) {
            case 'ONE':
                var val = $('input:radio[name="is_standard_answer"]:checked').val();
                if( isNaN(val)) {errorMessage = '请选择一个正确的答案';}

                var k;
                k = 0;
                $('input[name="answer_name"]').each(function(obj){
                    var is_standard_answer = false;
                    k = k + 1;
                    if( k == val ){is_standard_answer = true;}
                    if( $(this).val() != '' )
                    {
                        data.push({
                            answer_name: $(this).val(),
                            is_standard_answer:is_standard_answer
                        })
                    }
                });

                var status = false;
                var i;
                for (var i = 0; i < data.length; i++) {
                    if( data[i].is_standard_answer == true ){status = true;}
                };

                if( status == false ){errorMessage = '请将正确的答案填写完整';}

                break;
            case 'MORE':
                var num=[];
                $('input[name="is_standard_answer"]:checked').each(function(obj){
                    num.push(parseInt($(this).val()))
                });
                if( num == []) {errorMessage = '请选择一个正确的答案';}

                var k;
                k = 0;
                $('input[name="answer_name"]').each(function(obj){
                    var is_standard_answer = false;
                    k = k + 1;
                    if( $.inArray(k,num) != -1 ){is_standard_answer = true;}
                    data.push({
                        answer_name: $(this).val(),
                        is_standard_answer:is_standard_answer
                    })
                });
                var i;
                for (var i = 0; i < data.length; i++) {
                    if( data[i].is_standard_answer == true )
                    {
                        if(data[i].answer_name == '') {errorMessage = '请将正确的答案填写完整';}
                    }
                };

                break;
            case 'TEXT':
                var answer_name = $('[name=answer_name]').val();
                if( answer_name != '' )
                {
                    data.push({
                        answer_name: answer_name,
                        is_standard_answer:true
                    })
                }else{
                    errorMessage = '请将正确的答案填写完整';
                }
                break;
        }

        if( errorMessage )
        {
            alert(errorMessage);
            return
        }

        if (!self.lock) {
            self.lock = true;
            $.ajax({
                url: '/core/topic/createTopic',
                data: {
                    topic_name: topic_name,
                    active_topic_id: active_topic_id,
                    type: type,
                    data: data
                },
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    self.lock = false;
                    if (response.httpStatusCode == 200) {
                        // alert('添加成功');
                        showmodal('添加成功');
                        location.replace(location.href)
                    }
                    else {
                        showmodal('添加失败');
                        // alert('添加失败');
                        location.replace(location.href)
                    }
                    $('[name=activityAdd]').modal('hide');
                }
            });
        }

    });

    $('[name=type]',uiMode).change(function() {
        var type = $(this).children('option:selected').val();
        $('[id=answer]',uiMode).html('');
        var topic;
        switch(type){
            case 'ONE':
                topic =     '<div class="col-sm-10 label-12" style="padding-left: 100px">';
                topic +=    '<label>';
                topic +=    'A : <input type="text" name="answer_name"> <input type="radio" value="1" name="is_standard_answer"> <i></i></label>';
                topic +=    '</div>';
                topic +=    '<div class="col-sm-10 label-12" style="padding-left: 100px">';
                topic +=    '<label>';
                topic +=    'B : <input type="text" name="answer_name"> <input type="radio" value="2" name="is_standard_answer"> <i></i></label>';
                topic +=    '</div>';
                topic +=    '<div class="col-sm-10 label-12" style="padding-left: 100px">';
                topic +=    '<label>';
                topic +=    'C : <input type="text" name="answer_name"> <input type="radio" value="3" name="is_standard_answer"> <i></i></label>';
                topic +=    '</div>';
                topic +=    '<div class="col-sm-10 label-12" style="padding-left: 100px">';
                topic +=    '<label>';
                topic +=    'D : <input type="text" name="answer_name"> <input type="radio" value="4" name="is_standard_answer"> <i></i></label>';
                topic +=    '</div>';

                break;
            case 'MORE':

                topic =     '<div class="col-sm-10 label-12" style="padding-left: 100px">';
                topic +=    '<label>';
                topic +=    'A : <input type="text" name="answer_name"> <input type="checkbox" value="1" name="is_standard_answer"> <i></i></label>';
                topic +=    '</div>';
                topic +=    '<div class="col-sm-10 label-12" style="padding-left: 100px">';
                topic +=    '<label>';
                topic +=    'B : <input type="text" name="answer_name"> <input type="checkbox" value="2" name="is_standard_answer"> <i></i></label>';
                topic +=    '</div>';
                topic +=    '<div class="col-sm-10 label-12" style="padding-left: 100px">';
                topic +=    '<label>';
                topic +=    'C : <input type="text" name="answer_name"> <input type="checkbox" value="3" name="is_standard_answer"> <i></i></label>';
                topic +=    '</div>';
                topic +=    '<div class="col-sm-10 label-12" style="padding-left: 100px">';
                topic +=    '<label>';
                topic +=    'D : <input type="text" name="answer_name"> <input type="checkbox" value="4" name="is_standard_answer"> <i></i></label>';
                topic +=    '</div>';

                break;
            case 'TEXT':

                topic =     '<div class="col-sm-10 label-12" style="padding-left: 120px">';
                topic +=    '<label>';
                topic +=    '<textarea placeholder="参考答案" name="answer_name" style="resize: none;width: 400px;height: 100px;"></textarea>';
                topic +=    '</label>';
                topic +=    '</div>';

                break;
        }
        $('[id=answer]',uiMode).html(topic);
    });

    $(ui).on('click','[name=delsubmit]',function(){
        if(confirm("您真的确定要删除吗"))
        {
            var node = $(this).parents('tr');
            var topic_id = $('[name=topic_id]',node).val();

            $.ajax({
                url: '/core/topic/delTopic',
                data: {
                    topic_id: topic_id
                },
                type: 'POST',
                dataType:'json',
                success: function(response){
                    self.lock = false;
                    if ( response.httpStatusCode == 200 )
                    {
                        showmodal('删除成功');
                        $(node).fadeOut(500, function(){
                            $(node).remove();
                        });
                    }
                    else
                    {
                        showmodal('删除失败');
                    }
                }
            });
        }
    });

};


