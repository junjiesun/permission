// JavaScript Document
//===== BEGIN: class Banner =====/
function userList( domSelector)
{
	this.domSelector = domSelector;
	this.domReady();
	this.doInitPerm();
}
userList.prototype.domReady = function()
{
	var self = this;
	var ui = $(this.domSelector);

	$('.cancel', ui).click(function(){
		self.cancel(ui);
	});

	$('.save-perm', ui).click(function(){
		self.userPermPost(ui);
	});



	$(ui).on('click', '.del', function(){

		if(confirm("您真的确定要删除吗？"))
		{
			var node = $(this).parents('tr');
			var user_id = $('input[name=uid]', node).val();

			$.ajax({
				url: '/service/userdel',
				data: {
					uid: user_id
				},
				type: 'POST',
				dataType:'json',
				success: function(response){
					self.lock = false;
					if ( response.httpStatusCode == 200 )
					{
						$(node).fadeOut(500, function(){
							$(node).remove();
						});
					}
					else
					{
						alert(response.message);
					}
				}
			});
		}
	});


	$(ui).on('click', '.open-btn', function()
	{
		btn = $(this);
		var nodes = $(btn).parents('tr');
		var user_id = $('input[name=uid]', nodes).val();
		var status = $('.status', nodes);
		$.ajax({
			url: '/service/userval',
			data: {
				status: 1,
				uid: user_id
			},
			type: 'POST',
			dataType:'json',
			success: function(response){
				if ( response.httpStatusCode == 200 )
				{
					$(btn).removeClass('open-btn').addClass('close-btn');
					$(btn).html('<i class="fa fa-eye-slash"></i> 关闭');
					$(status).html('<span class="label label-primary">开启</span>');
				}
				else
				{
					alert(response.message);
				}
			}
		});
	});

	$(ui).on('click', '.close-btn', function(){

		btn = $(this);
		var nodes = $(btn).parents('tr');
		var user_id = $('input[name=uid]', nodes).val();
		var status = $('.status', nodes);
		$.ajax({
			url: '/service/userval',
			data: {
				status: 0,
				uid: user_id
			},
			type: 'POST',
			dataType:'json',
			success: function(response){
				if ( response.httpStatusCode == 200 )
				{
					$(btn).removeClass('close-btn').addClass('open-btn');
					$(btn).html('<i class="fa fa-eye"></i> 开启');
					$(status).html('<span class="label label-warning">关闭</span>');
				}
				else
				{
					alert(response.message);
				}
			}
		});
	});

	$(ui).on('click', '.perm', function()
	{
		btn = $(this);
		var nodes = $(btn).parents('tr');
		var user_id = $('input[name=uid]', nodes).val();
		location.href="/user/permission/"+user_id
	});

	$(ui).on('click', '.organize', function(){
		var node = $(this).parents('tr');
		var user_id = $('input[name=uid]', node).val();
		$("input[name=organize_uid]").val(user_id);

	});

};
userList.prototype.doInitPerm = function()
{
	var existPerm = $('input[name=gids]',$(this.domSelector)).val();
	
	if(existPerm != '' )
	{
		var existPerm = eval('('+existPerm+')');
		for(i in existPerm){
			$('#g'+ existPerm[i]).attr('checked',true);
		}
	}
}


userList.prototype.userPermPost = function(ui)
{
	var self = this;
	var user_id = $('[name=uid]', ui).val();
	
	var permGroupChecked = [];

	$("input[type=checkbox]:checked").each(function(i,p){
		permGroupChecked.push($(p).val());
	});

	// if(permGroupChecked.length == 0) {
	// 	showmodal("请选择至少一个权限组");
	// 	return false;
	// }

	if ( user_id !== null && user_id !== '' && user_id !== undefined)
	{	
		if ( !self.lock )
		{
			self.lock = true;
			$.ajax({
			    url: '/user/permgroupost',
				data:{
				    user_id: user_id,
				    groups : permGroupChecked
				},
				type:'POST',
				dataType:'json',
			    success: function(response){
					self.lock = false;
					if ( response.httpStatusCode == 200 )
					{
						showmodal('操作成功');
					}
					else
					{
						showmodal(response.message);
					}
			    }
			});
		}
	}
	else
	{
		showmodal("用户数据错误");
	}
}
userList.prototype.cancel = function(ui)
{
	$("input[type=text]:visible",ui).val('');
	$("input[type=checkbox]",ui).attr('checked',false);
	this.lock = false;
}
