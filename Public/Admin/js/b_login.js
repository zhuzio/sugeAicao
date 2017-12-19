//alert($)
$(document).ready(function(){//页面加载完成再加载脚本
	$('input[name="button"]').click(function(event){
		var $name = $('input[name="adminName"]');
		var $password = $('input[name="password"]');
		var $remember = $('input[name="remember"]');
		var $text = $(".text");
		var _name = $.trim($name.val());//去掉字符串多余空格
		var _password = $.trim($password.val());
		var _remember = $.trim($remember.val());

		if(_name==''){
			$text.text('请输入用户名');
			$name.focus();
			return;
		}
		if(_password==''){
			$text.text('请输入密码');
			$name.focus();
			return;
		}
		if(_name!='' &&_password!=''){
			$.ajax({
				type:'POST',
				url:MODULE+'/Login/loginAct',
				data:{'adminName':_name,'password':_password,'remember':_remember},
				async:false,
				cache:false,
				success: function(data){
					if(data.status==1){
						$text.text("登陆成功，请稍后...");
						window.location.href =MODULE+"/Index/index";
					}
					else if(data.status==-1){
						$text.text("用户名或密码错误.");
					}else{
						$text.text("您的权限已被禁用，请联系管理员.");
					}
				},
				error: function(json){

				}
			});
		}else{
			$text.text("用户名或密码错误.");
		}
		

	});

});