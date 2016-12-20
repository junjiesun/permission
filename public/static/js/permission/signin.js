// JavaScript Document
//===== BEGIN: class Signin =====/
function Signin( domSelector )
{
    this.domSelector = domSelector;
    this.lock = false;
    this.domReady();
}
Signin.prototype.domReady = function()
{
	var ui = $(this.domSelector);
	var self = this;
	
	$('[name=submit]', ui).click(function(){
		self.sendSignin(ui);
	});
	
	$('[name=password]', ui).keypress(function(event){
	    if ( event.which == 13 )
	    {
            self.sendSignin(ui);
            event.preventDefault();
	    }
    });
	
};

fChkMail=function(mail)
{
	var reg=/^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,5}$/; 
	var bchk=reg.test(mail); 
	return bchk; 
}

Signin.prototype.sendSignin = function(ui)
{
	var self = this;
	var username = $('[name=username]', ui).val();
	var password = $('[name=password]', ui).val();
	var memberPass = $('input[name=memberPass]',ui).is(":checked")?1:0;
	
	if ( password !== null && password !== '' && password !== undefined 
		&& username !== null && username !== '' && username !== undefined 
	)
	{	
		if(!fChkMail(username))
		{
			alert('请使用邮箱登录');
			return false;
		}

		if ( !self.lock )
		{
			self.lock = true;
			$.ajax({
			    url: '/service/signin',
				data:{
				    username: username,
				    password: password,
				    memberPass:memberPass
				},
				type:'POST',
				dataType:'json',
			    success: function(response){
		
					self.lock = false;
					if ( response.httpStatusCode == 200 )
					{
						window.location.href = response.data.url; 
					}
					else
					{
						alert(response.data.message);
					}
			    }
			});
		}
	
	}
	else
	{
		alert("请检查帐号或密码");
	}
	
	
	return 0;
};

