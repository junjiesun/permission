// JavaScript Document
//===== BEGIN: class Banner =====/
function PermGroup( domSelector, permuserModal)
{
	this.clickBtn;
	this.domSelector = domSelector;
	this.permuserModal = permuserModal;
	this.doInitPerm();
	this.initUserPerm(domSelector);
	this.domReady();
}
PermGroup.prototype.doInitPerm = function()
{
	var existPerm = $('input[name=perm_ids]',$(this.domSelector)).val();
	if(existPerm != '')
	{
		var existPerm = eval('('+existPerm+')');
		for(i in existPerm){
			$('#p'+ existPerm[i]).prop('checked',true);
		}
	}
}

PermGroup.prototype.initUserPerm = function(ui)
{
	$('input[type=checkbox][name=checkallsub]',ui).each(function(i,o){
		checkObj = $(o);
		// console.log(checkObj);
		panel = $(this).parents('.panel');
		$(panel).find('[name=perm]').each(function(j,oj){
			if($(oj).prop('checked') == true){
				$(checkObj).prop('checked',true);
				return true;
			}
		})
	})
}

PermGroup.prototype.domReady = function()
{

	var ui = $(this.domSelector);
	var userModal = $(this.permuserModal);
	var self = this;

	$('input[type=checkbox][name=checkallsub]',ui).each(function(i,o){
		if($(o).prop('checked') == true)
		{
			$(o).parent().find('a').css('color','red');
		}
	})

	$('.cancel', ui).click(function(){
		self.cancel(ui);
	});

	$('.save', ui).click(function(){
		self.groupPost(ui);
	});

	$('[name=checkall]',ui).click(function(){
	    if(this.checked){    
	        $('input[type=checkbox]',ui).prop("checked",true);
	    }else{    
	        $('input[type=checkbox]',ui).removeAttr("checked"); 
	    }    
	});

	$('input[type=checkbox][name=checkallsub]').click(function(){
		panel = $(this).parents('.panel');

		if(this.checked){
		    $('input[name=perm]',panel).prop("checked",true);
	    }else{    
	        $('input[type=checkbox]',panel).removeAttr("checked"); 
	    }    
	})
	//权限组删除
	$(ui).on('click', '.del', function(){

		if(confirm("您真的确定要删除吗？"))
		{
			var node = $(this).parents('tr');
			var perm_group_id = $('input[name=perm_group_id]', node).val();

			$.ajax({
				url: '/permgroup/del',
				data: {
					perm_group_id: perm_group_id
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

	//权限组成员删除
	$(ui).on('click', '.udel', function(){

		if(confirm("您真的确定要删除吗？"))
		{
			var node = $(this).parents('tr');
			var uid = $('input[name=guid]', node).val();
			var gid = $('input[name=gid]', userModal).val();

			$.ajax({
				url: '/permgroup/userdel',
				data: {
					uid: uid,
					gid: gid
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
		var perm_group_id = $('input[name=perm_group_id]', nodes).val();
		location.href = '/permgroup/edit/'+perm_group_id;
	})

	$(ui).on('click','.adduser', function()
	{
		var nodes = $(this).parents('tr');
		var perm_group_id = $('input[name=perm_group_id]', nodes).val();
		title = $(nodes).find('td:first').html();
		
		$('input[name=type_id]',userModal).val(perm_group_id);

		$('h4',userModal).find('span').html(title+'组');

		//init user selectpicker
		$.getUser.domReady(perm_group_id,'permgroup');
		
		userModal.modal();
		$('.save-user-group', userModal).click(function(){
			self.groupUserPost(userModal);
		});
	});

	$(ui).on('click','.user-perms', function()
	{
		var nodes = $(this).parents('tr');
		var guid = $('input[name=guid]', nodes).val();
		var gid = $('input[name=gid]', userModal).val();
		title = $(nodes).find('td:first').html();
		
		$('input[type=checkbox]',userModal).removeAttr('checked');

		$('input[name=uid]',userModal).val(guid);

		$('h4',userModal).find('span').html(title);
		userModal.modal();

		$.ajax({
		    url: '/permgroup/userperm',
			data:{
			    uid: guid,
			    gid: gid
			},
			type:'POST',
			dataType:'json',
		    success: function(data){
				self.lock = false;
				if(data.length == 0){
					$('input[type=checkbox][name=checkallsub]').click();
				}else{
					for(i in data)
					{
						$('#p'+ data[i]).prop('checked',true);
						panel = $('#p'+ data[i]).parents('.panel');
						$('input[name=checkallsub]',panel).prop("checked",true);
					}
				}
		    }
		});
		self.initUserPerm(userModal);
		$('.save-user-perm', userModal).click(function(){
			self.groupUserPermPost(userModal);
		});
	});	
};

PermGroup.prototype.cancel = function(ui)
{
	$("input[type=text]:visible",ui).val('');
	$("input[type=checkbox]",ui).attr('checked',false);
	this.lock = false;
}

PermGroup.prototype.groupPost = function(ui)
{
	var self = this;
	var perm_group_id = $('[name=perm_group_id]', ui).val();
	var type = $('[name=type]', ui).val();
	var name = $('[name=perm_group_name]', ui).val();
	var description = $('[name=description]', ui).val();
	
	var permChecked = [];

	$("input[name=perm]:checked").each(function(i,p){
		permChecked.push($(p).val());
	});

	if(permChecked.length == 0) {
		showmodal("请选择至少一个权限");
		return false;
	}

	url = '/permgroup/addpost';
	if(type == 'EDIT')
	{
		url = '/permgroup/editpost';
	}
	if ( name === null || name === '' || name === undefined)
	{	
		showmodal("请填写权限组名称");
		return false;
	}

	if(description === null || description === '' || description === undefined)
	{
		showmodal('请填写描述');
		return false;
	}
	
	if ( !self.lock )
	{
		self.lock = true;
		$.ajax({
		    url: url,
			data:{
			    perm_group_id: perm_group_id,
			    name: name,
			    description : description,
			    permission : permChecked
			},
			type:'POST',
			dataType:'json',
		    success: function(response){
				self.lock = false;
				if ( response.httpStatusCode == 200 )
				{
					showmodal('操作成功');
					setTimeout(function(){ location.href="/permgroup"}, 1500);
				}
				else
				{
					showmodal(response.message);
				}
		    }
		});
	}
}

PermGroup.prototype.groupUserPost = function(ui)
{
	uids = $('#select-user-list',ui).val();
	group_id = $("input[name=type_id]",ui).val();
	if ( !self.lock )
	{
		self.lock = true;
		$.ajax({
		    url: '/permgroup/adduser',
			data:{
			    uids: uids,
			    group_id: group_id
			},
			type:'POST',
			dataType:'json',
		    success: function(response){
				self.lock = false;
				if ( response.httpStatusCode == 200 )
				{
					$(ui).modal('hide');
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

PermGroup.prototype.groupUserPermPost = function(userModal)
{
	var gid = $('[name=gid]', userModal).val();
	var uid = $('[name=uid]', userModal).val();

	var permChecked = [];
	$("input[name=perm]:checked",userModal).each(function(i,p){
		permChecked.push($(p).val());
	});
	if ( !self.lock )
	{
		self.lock = true;
		$.ajax({
		    url: '/permgroup/usereditpost',
			data:{
			    gid: gid,
			    uid: uid,
			    permission : permChecked
			},
			type:'POST',
			dataType:'json',
		    success: function(response){
				self.lock = false;
				if ( response.httpStatusCode == 200 )
				{
					showmodal('操作成功');
					setTimeout(function(){ location.href="/permgroup/user?gid="+gid}, 1500);
				}
				else
				{
					showmodal(response.message);
				}
		    }
		});
	}
}



