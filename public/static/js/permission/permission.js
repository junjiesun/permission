// JavaScript Document
//===== BEGIN: class Banner =====/
function Permission( domSelector)
{
	this.clickBtn;
	this.domSelector = domSelector;
	this.domReady();
}

Permission.prototype.domReady = function()
{

	var ui = $(this.domSelector);
	var self = this;

	$('.save', ui).click(function(){
		self.permissionPost(ui);
	});

	$(ui).on('click', '.del', function(){

		if(confirm("您真的确定要删除吗？"))
		{
			var node = $(this).parents('tr');
			var pid = $('input[name=pid]', node).val();

			$.ajax({
				url: '/permission/del',
				data: {
					pid: pid
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

	$(ui).on('click','.setting', function()
	{
		var node = $(this).parents('tr');
		var pid = $('input[name=pid]', node).val();
		var ptype = $(this).attr('data-type');
		self.lock = true;
		$.ajax({
		    url: '/permission/edittype',
			data:{
			    pid: pid,
			    type:ptype
			},
			type:'POST',
			dataType:'json',
		    success: function(response){
				self.lock = false;
				if ( response.httpStatusCode == 200 )
				{
					showmodal('操作成功');
					setTimeout(function(){ location.href="/permission"}, 1500);
				}
				else
				{
					showmodal(response.message);
				}
		    }
		});
	})

};


Permission.prototype.permissionPost = function(ui)
{
	var self = this;
	var pid = $('[name=pid]', ui).val();
	var ptype = $('[name=ptype]', ui).val();
	var name = $('[name=perm_name]', ui).val();
	var c = $('[name=perm_c]', ui).val();
	var m = $('[name=perm_m]', ui).val();
	
	var type = $('[name=type]', ui).val();
	url = '/permission/addpost';
	if(type == 'EDIT')
	{
		url = '/permission/editpost';
	}
	if ( name === null || name === '' || name === undefined)
	{	
		showmodal("请填写权限名称");
		return false;
	}
	if ( c === null || c === '' || c === undefined)
	{	
		showmodal("controller不能为空");
		return false;
	}
	
	if ( !self.lock )
	{
		self.lock = true;
		$.ajax({
		    url: url,
			data:{
			    pid: pid,
			    name: name,
			    c : c,
			    m : m,
			    type:ptype
			},
			type:'POST',
			dataType:'json',
		    success: function(response){
				self.lock = false;
				if ( response.httpStatusCode == 200 )
				{
					showmodal('操作成功');
					setTimeout(function(){ location.href="/permission"}, 1500);
				}
				else
				{
					showmodal(response.message);
				}
		    }
		});
	}
}


