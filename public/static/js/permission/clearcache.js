// JavaScript Document
//===== BEGIN: class DataSync =====/
function Clearcache( domSelector )
{
    this.domSelector = domSelector;
    this.lock = false;
    this.domReady();
}
Clearcache.prototype.domReady = function()
{
	var ui = $(this.domSelector);
	var self = this;
	
	
	$('[name=clearcache]', ui).click(function(){
		
		if ( !self.lock )
		{
			self.lock = true;
			$('[name=results]', ui).html('缓存清理中...');
			
			$.ajax({
				url: '/doclearpost',
				type:'GET',
				dataType:'json',
				success: function(response){

					setTimeout(function(){
						self.lock = false;
						
						if ( response.httpStatusCode == 200 )
						{
							var text = "缓存清理成功";
							$('[name=results]', ui).html(text);
							
						}
						else
						{
							alert(response.message);
						}
					}, 1500);
					
				}
			});
		}

	});
	
	
};

