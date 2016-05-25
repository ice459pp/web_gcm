<?php
require_once('db/db_config.php');
$user_id = isset($_GET['id'])? $_GET['id']: '';
$s = $db->Query("SELECT email FROM users WHERE user_id = '" . $user_id . "' LIMIT 1");
$email = '';
if ($db->No($s) > 0) {
	$r = $db->fetch($s);
	$email = $r['email'];
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <link href="css/roster_clerk.css" rel="stylesheet">
    <style type="text/css">
    	.roster_row {
    		padding: 10px;
    		border: 1px solid #888;
    		cursor: pointer;
    	}
    	.roster_row:hover {

    	}
    </style>
    <title>業務測試</title>	
</head>
<body>
<div class="main">
	<h2>聊天列表</h2>

	<div class="tabs standard">
		<ul class="tab-links">
			<li id="tab1_link" class="active"><a href="#tab1">使用者</a></li>
			<li id="tab2_link" class=""><a href="#tab2">諮詢師</a></li>
			<li id="tab3_link" class=""><a href="#tab3">業務群</a></li>
		</ul>

		<div class="tab-content">
			<div id="tab1" class="tab active" style="display: block;">
				
			</div>

			<div id="tab2" class="tab" style="display: none;">
				
			</div>

			<div id="tab3" class="tab" style="display: none;">
				
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script type="text/javascript"> 
jQuery(document).ready(function() {
	email = '<?=$email?>';
	user_id = '<?=$user_id?>';

	// Standard
	jQuery('.tabs.standard .tab-links a').on('click', function(e)  {
		var currentAttrValue = jQuery(this).attr('href');

		// Show/Hide Tabs
		jQuery('.tabs ' + currentAttrValue).show().siblings().hide();

		// Change/remove current tab to active
		jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

		e.preventDefault();

	});

	// Animated Fade
	jQuery('.tabs.animated-fade .tab-links a').on('click', function(e)  {
		var currentAttrValue = jQuery(this).attr('href');

		// Show/Hide Tabs
		jQuery('.tabs ' + currentAttrValue).fadeIn(400).siblings().hide();

		// Change/remove current tab to active
		jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

		e.preventDefault();
	});

	// Animated Slide 1
	jQuery('.tabs.animated-slide-1 .tab-links a').on('click', function(e)  {
		var currentAttrValue = jQuery(this).attr('href');

		// Show/Hide Tabs
		jQuery('.tabs ' + currentAttrValue).siblings().slideUp(400);
		jQuery('.tabs ' + currentAttrValue).delay(400).slideDown(400);

		// Change/remove current tab to active
		jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

		e.preventDefault();
	});

	// Animated Slide 2
	jQuery('.tabs.animated-slide-2 .tab-links a').on('click', function(e)  {
		var currentAttrValue = jQuery(this).attr('href');

		// Show/Hide Tabs
		jQuery('.tabs ' + currentAttrValue).slideDown(400).siblings().slideUp(400);

		// Change/remove current tab to active
		jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

		e.preventDefault();
	});

	$('#tab1_link').click(function() {
		fetchUserRoster(email);
	});

	$('#tab2_link').click(function() {
		fetchConsultantRoster(email);
	});

	$('#tab3_link').click(function() {
		fetchClerkRoster(email);
	});

	$('#tab1_link').trigger('click');


});

function fetchUserRoster(email) {
	$('#tab1').empty();
	$.ajax({
		type: 'POST',
		url: 'api/roster_user.php',
		async: false,
		data: {email: email},
	}).done(function(r) {
		var rs = eval('(' + r + ')');
		if(rs.status == "OK") {
			var users = rs.result;
			console.log(users);
			var $parent_div = $('<div>');
			for (var i = 0; i < users.length; i++) {

				$username = $('<div>').html(users[i].username);
				$email = $('<div>').html(users[i].email);
				$div = $('<div>')
				.addClass('roster_row')
				.attr('data-from_id', user_id)
				.attr('data-to_id', users[i].user_id)
				.append($username)
				.append($email);
				$parent_div.append($div);
				$('#tab1').append($parent_div);
			}

			$('.roster_row').click(function() {
				var $contact = $(this);
				var to_id = $contact.data('to_id');
				var from_id = $contact.data('from_id');
				window.location.href = 'chat.php?to_id=' + to_id + '&from_id=' + from_id;
			});
		} else {
			alert(rs.error);
		}

	});
}
function fetchConsultantRoster() {
	$('#tab2').empty();
	$.ajax({
		type: 'POST',
		url: 'api/roster_consultant.php',
		async: false,
		data: {email: email},
	}).done(function(r) {
		var rs = eval('(' + r + ')');
		if(rs.status == "OK") {
			var users = rs.result;
			console.log(users);
			var $parent_div = $('<div>');
			for (var i = 0; i < users.length; i++) {

				$username = $('<div>').html(users[i].username);
				$email = $('<div>').html(users[i].email);
				$div = $('<div>')
				.addClass('roster_row')
				.attr('data-from_id', user_id)
				.attr('data-to_id', users[i].user_id)
				.append($username)
				.append($email);
				$parent_div.append($div);
				$('#tab2').append($parent_div);
			}
			$('.roster_row').click(function() {
				var $contact = $(this);
				var to_id = $contact.data('to_id');
				var from_id = $contact.data('from_id');
				window.location.href = 'chat.php?to_id=' + to_id + '&from_id=' + from_id;
			});
		} else {
			alert(rs.error);
		}

	});

}
function fetchClerkRoster() {
	$('#tab3').empty();
	$.ajax({
		type: 'POST',
		url: 'api/roster_clerk.php',
		async: false,
		data: {email: email},
	}).done(function(r) {
		var rs = eval('(' + r + ')');
		if(rs.status == "OK") {
			var users = rs.result;
			console.log(users);
			var $parent_div = $('<div>');
			for (var i = 0; i < users.length; i++) {

				$username = $('<div>').html(users[i].username);
				$email = $('<div>').html(users[i].email);
				$div = $('<div>')
				.addClass('roster_row')
				.attr('data-from_id', user_id)
				.attr('data-to_id', users[i].user_id)
				.append($username)
				.append($email);
				$parent_div.append($div);
				$('#tab3').append($parent_div);
			}
			$('.roster_row').click(function() {
				var $contact = $(this);
				var to_id = $contact.data('to_id');
				var from_id = $contact.data('from_id');
				window.location.href = 'chat.php?to_id=' + to_id + '&from_id=' + from_id;
			});
		} else {
			alert(rs.error);
		}
	});
}

</script>
</body>
</html>

 
