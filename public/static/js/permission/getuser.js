// JavaScript Document
$.getUser = {
	'selectChecked' :   true,
	'checkedpicker' : '#select-user-list'
}
/* id : 查询条件id， type 查询条件类型，val 单个选中*/
$.getUser.domReady = function(id, type, val)
{
	var instance = $.getUser;
	selectDOM = instance.checkedpicker;
	$.ajax({
			url: '/user/all',
			data: {
				type: type,
				id: id,
				ele : selectDOM
			},
			type: 'POST',
			dataType:'json',
			success: function(data)
			{
				selectDOM = data.ele;
				selected = $(selectDOM).data('value');
				selected = selected == '' ? val : selected;

			    $(selectDOM).find('option').remove();
				optionHtml = '<option value="">请选择用户</option>';
				for (var i = 0; i < data.allUser.length; i++) 
				{
					optionHtml += '<option data-subtext="'+data.allUser[i].email+'" value="'+data.allUser[i].user_id+'" '+(data.allUser[i].user_id == selected?"selected='selected'":'')+'>'+data.allUser[i].name+'</option>'; 
				};
				$(selectDOM).html(optionHtml);
				if(typeof(selected) == 'object')
				{
					$(selectDOM).selectpicker('val', selected);
				}
				$(selectDOM).selectpicker('refresh');
				$(selectDOM).parent('div').find('button').removeClass('btn-default');
				// 多个默认选中
				if(instance.selectChecked)
				{ 
					$(selectDOM).selectpicker('val', eval('('+data.typeUser+')'));
				}
			}
	});
}