// JavaScript Document
//===== BEGIN: class Banner =====/
function Menu( domSelector )
{
	this.clickBtn;
	this.domSelector = domSelector;
	this.domReady();
}
Menu.prototype.domReady = function()
{
	var ui = $(this.domSelector);
	var self = this;
	
	//init default checked permission
	var mid = $('input[name=parent_menu_id]', ui).val();
	if(!mid) mid = 0;
	$("select[name=parent_menu_id]").val(parseInt(mid));
	var perm_id = $('input[name=have_perm_id]', ui).val();
	if(!perm_id) perm_id = 0;
	$("input[type='radio'][name='perm_id'][value='"+parseInt(perm_id)+"']").prop("checked", true);
	checkPerm = $("input[type='radio'][name='perm_id']:checked");
	openPannel = $(checkPerm).parent('td').css('color','red');
	openPannel = $(checkPerm).parents('.panel');
	$(openPannel).find('.panel-collapse').addClass('in').removeAttr('style').attr('aria-expanded',true);
	$(openPannel).find('.panel-title a').css('color','red');


	$('.cancel', ui).click(function(){
		self.cancel(ui);
	});

	$('.save', ui).click(function(){
		self.menuPost(ui);
	});

	var ui = $(this.domSelector);

	$(ui).on('click', '.del', function(){

		if(confirm("您真的确定要删除吗？"))
		{
			var node = $(this).parents('tr');
			var mid = $('input[name=mid]', node).val();

			$.ajax({
				url: '/menu/del',
				data: {
					mid: mid
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

	$(ui).on('click','.edit', function()
	{
		btn = $(this);
		var nodes = $(btn).parents('tr');
		var mid = $('input[name=mid]', nodes).val();
		location.href='/menu/edit/'+mid;
	})

	$(ui).on('click', '.status-btn', function()
	{
		btn = $(this);
		var nodes = $(btn).parents('tr');
		var mid = $('input[name=mid]', nodes).val();
		var status = $('.status', nodes);
		var dataStatus = $(btn).attr('data-status');

		$.ajax({
			url: '/menu/value',
			data: {
				status:dataStatus,
				mid: mid
			},
			type: 'POST',
			dataType:'json',
			success: function(response){
				if ( response.httpStatusCode == 200 )
				{
					if(dataStatus == 1)
					{	
						$(btn).attr('data-status',0);
						$(btn).removeClass('close-btn').addClass('open-btn');
						$(btn).html('<i class="fa fa-eye-slash"></i> 开启');
						$(status).html('<span class="label label-warning">关闭</span>');
					}else{
						$(btn).attr('data-status',1);
						$(btn).removeClass('open-btn').addClass('close-btn');
						$(btn).html('<i class="fa fa-eye-slash"></i> 关闭');
						$(status).html('<span class="label label-primary">开启</span>');
					}
				}
				else
				{
					showmodal(response.message);
				}
			}
		});
	});
};

Menu.prototype.cancel = function(ui)
{
	$("input:visible",ui).val('');
	this.lock = false;
}

Menu.prototype.menuPost = function(ui)
{
	var self = this;
	var menu_id = $('[name=menu_id]', ui).val();
	var type = $('[name=type]', ui).val();
	var perm_id = $('input[type=radio][name=perm_id]:checked', ui).val();
	var parent_menu_id = $('[name=parent_menu_id]', ui).val();
	var menu_name = $('[name=menu_name]', ui).val();
	var description = $('[name=description]', ui).val();
	var sort = $('[name=sort]', ui).val();
	var icon = $('[name=icon]', ui).val();
	var status = $('input[type=radio][name=is_open]:checked', ui).val();
	
	url = '/menu/addpost';
	if(type == 'EDIT')
	{
		url = '/menu/editpost';
	}

	if ( menu_name === null || menu_name === '' || menu_name === undefined)
	{	
		showmodal("请填写菜单名称");
		return false;
	}

	if ( perm_id === null || perm_id === '' || perm_id === undefined)
	{	
		showmodal("请选择权限");
		return false;
	}

	if(description === null || description === '' || description === undefined)
	{
		showmodal('请填写备注');
		return false;
	}

	if ( !self.lock )
		{
			self.lock = true;
			$.ajax({
			    url: url,
				data:{
				    parent_menu_id: parent_menu_id,
				    menu_name: menu_name,
				    menu_id : menu_id,
				    perm_id : perm_id,
				    description : description,
				    sort: sort,
					icon: icon,
				    status:status
				},
				type:'POST',
				dataType:'json',
			    success: function(response){
		
					self.lock = false;
					if ( response.httpStatusCode == 200 )
					{
						showmodal('操作成功');
						setTimeout(function(){ location.href="/menu"}, 1500);
					}
					else
					{
						showmodal(response.message);
					}
			    }
			});
	}
	
	
}

