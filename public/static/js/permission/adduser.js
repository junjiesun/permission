// JavaScript Document
function Adduser( domSelector )
{
    this.domSelector = domSelector;
    this.lock = false;
    this.domReady();
}
Adduser.prototype.domReady = function()
{
	var ui = $(this.domSelector);
	var self = this;
	
	$('.cancel', ui).click(function(){
		self.cancel(ui);
	});

	$('.save', ui).click(function(){
		self.sendUser(ui);
	});

	// $("[name=user_type]",ui).change(function(){
	// 	$userType = $(this);
	// 	$syncUserDOM = $('input[name=sync_user]',ui).parent().parent().parent('div');
	//
	// 	var user_type = $userType.val();
	// 	if(user_type == 'ADMIN')
	// 	{
	// 		$('[name=password]', ui).val('');
	// 		$('[name=rp-password]', ui).val('');
	// 		$syncUserDOM.removeClass('show').addClass('hide');
	// 	}else
	// 	{
	// 		$('[name=password]', ui).val('');
	// 		$('[name=rp-password]', ui).val('');
	// 		$syncUserDOM.removeClass('hide').addClass('show');
	// 	}
	// })
};

Adduser.prototype.cancel = function(ui)
{
	$("input",ui).val('');
	this.lock = false;
}

fChkMail=function(mail)
{
	var reg=/^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,5}$/; 
	var bchk=reg.test(mail); 
	return bchk; 
}

Adduser.prototype.sendUser = function(ui)
{
	var self = this;
	var username = $('[name=username]', ui).val();
	var email = $('[name=email]', ui).val();
	var password = $('[name=password]', ui).val();
	var rp_password = $('[name=rp-password]', ui).val();
	var status = $('input[name=can_login]:checked', ui).val();
	var user_type = $('[name=user_type]', ui).val();
	// var sync_user = $('input[name=sync_user]:checked', ui).val();
	var public_group = $('input[name=public_group]:checked', ui).val();
	
	if(!fChkMail(email))
	{
		alert('请填写正确的邮箱');
		return false;
	}

	if ( username !== null && username !== '' && username !== undefined
		&& email !== null && email !== '' && email !== undefined
		&& password !== null && password !== '' && password !== undefined
		&& rp_password !== null && rp_password !== '' && rp_password !== undefined
	)
	{	
		if(password !== rp_password){
			alert('两次密码输入不一致');
			return false;
		}
		if ( !self.lock )
		{
			self.lock = true;
			$.ajax({
			    url: '/service/adduserpost',
				data:{
				    username: username,
				    password: password,
				    email : email,
				    can_login : status,
				    user_type : user_type,
				    // sync_user : sync_user,
				    public_group:public_group
				},
				type:'POST',
				dataType:'json',
			    success: function(response){
		
					self.lock = false;
					if ( response.httpStatusCode == 200 )
					{
						showmodal('添加成功');
						setTimeout(function(){ location.href="/user"}, 1500);
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
		alert("表单填写不完整");
	}
	
	
	return 0;
};

