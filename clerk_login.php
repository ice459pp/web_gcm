<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <link href="css/login.css" rel="stylesheet">
    <title>業務測試</title>	
</head>

<body>
<div class="login">
  <div class="login-triangle"></div>
  
  <h2 class="login-header">業務登入</h2>

  <form class="login-container">
  	<p>信箱帳號</p>
    <p><input id="login_email" type="email" placeholder="Email"></p>
    <p>密碼</p>
    <p><input id="login_password" type="password" placeholder="Password"></p>
    <p><input id="btn_login" type="button" value="登入"></p>
  </form>
</div>

<div class="register">
  <div class="register-triangle"></div>
  
  <h2 class="register-header">業務註冊</h2>

  <form class="register-container">
  	<p>信箱帳號</p>
    <p><input id="register_email" type="email" placeholder="Email"></p>
    <p>密碼</p>
    <p><input id="register_password" type="password" placeholder="Password"></p>
    <p>使用者名稱</p>
    <p><input id="register_name" type="text" placeholder="Username"></p>
    <p><input id="btn_register" type="button" value="註冊"></p>
  </form>
</div>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('#btn_login').click(function() {
		var email = $('#login_email').val();
		var pwd = $('#login_password').val();
		if (email == '' || pwd == '') {
			alert('登入欄位資訊不正確');
			return;
		}
		loginToServer(email, pwd);
	});

	$('#btn_register').click(function() {
		var email = $('#register_email').val();
		var pwd = $('#register_password').val();
		var name = $('#register_name').val();
		if (email == '' || pwd == '' || name == '') {
			alert('註冊欄位資訊不正確');
			return;
		}
		registerToServer(email, pwd, name);
	});
});

function registerToServer(email, pwd, username) {
	$.ajax({
		type: 'POST',
		url: 'api/user_create.php',
		async: false,
		data: {email: email, userpwd: pwd, username: username, type: 'clerk'},
	}).done(function(r) {
		var rs = eval('(' + r + ')');
		if(rs.status == "OK") {
			loginToServer(email, pwd);
		} else {
			alert(rs.error);
		}

	});
}

function loginToServer(email, pwd) {
	var device_id = 'initial';
	$.ajax({
		type: 'POST',
		url: 'api/user_login.php',
		async: false,
		data: {email: email, userpwd: pwd, device_id: device_id, type: 'clerk'},
	}).done(function(r) {
		var rs = eval('(' + r + ')');
		if(rs.status == "OK") {
			var user = rs.result;
			window.location.href = 'roster_clerk.php?id=' + user.user_id;
		} else {
			alert(rs.error);
		}

	});
}
</script>
</body>
</html>
