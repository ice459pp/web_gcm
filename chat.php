<?php
require_once('db/db_config.php');
$to_id = isset($_GET['to_id'])? $_GET['to_id']: '';
$from_id = isset($_GET['from_id'])? $_GET['from_id']: '';
$to_email = '';
$from_email = '';
$s = $db->Query("SELECT email FROM users WHERE user_id = '" . $to_id . "' LIMIT 1");
if ($db->No($s) > 0) {
	$r = $db->fetch($s);
	$to_email = $r['email'];
}
$s = $db->Query("SELECT email FROM users WHERE user_id = '" . $from_id . "' LIMIT 1");
if ($db->No($s) > 0) {
	$r = $db->fetch($s);
	$from_email = $r['email'];
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <link href="css/chat.css" rel="stylesheet">
    <style type="text/css">
	
	}
    </style>
    <title>業務測試</title>	
</head>
<body>
<div class="main">
	<div class="container">
	    <div class="row chat-window col-xs-5 col-md-3" id="chat_window_1" style="margin-left:10px;">
	        <div class="col-xs-12 col-md-12">
	        	<div class="panel panel-default">
	                <div class="panel-body msg_container_base">
	                    
	                    <!-- <div class="row msg_container base_sent">
	                        <div class="col-xs-10 col-md-10">
	                            <div class="messages msg_sent">
	                                <p>that mongodb thing looks good, huh?
	                                tiny master db, and huge document store</p>
	                                <time datetime="2009-11-13T20:00">Timothy • 51 min</time>
	                            </div>
	                        </div>
	             
	                    </div>
	                    <div class="row msg_container base_receive">
	             
	                        <div class="col-xs-10 col-md-10">
	                            <div class="messages msg_receive">
	                                <p>that mongodb thing looks good, huh?
	                                tiny master db, and huge document store</p>
	                                <time datetime="2009-11-13T20:00">Timothy • 51 min</time>
	                            </div>
	                        </div>
	                    </div> -->
	                    
	                </div>
	                <div class="panel-footer">
	                    <div class="input-group">
	                        <input id="btn-input" type="text" class="form-control input-sm chat_input" placeholder="Write your message here..." />
	                        <span class="input-group-btn">
	                        <button class="btn btn-primary btn-sm" id="btn-chat">Send</button>
	                        </span>
	                    </div>
	                </div>
	    		</div>
	        </div>
	    </div>
	    
	</div>
	
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src='//cdn.bootcss.com/socket.io/1.3.7/socket.io.js'></script>
<script type="text/javascript"> 
$(document).ready(function() {
	// uid 可以为网站用户的uid，作为例子这里用session_id代替
	var uid = '<?=$from_id?>';
	var to_email = '<?=$to_email?>';
	var from_email = '<?=$from_email?>';
	console.log(uid);
	console.log(to_email);

	$('#btn-chat').click(function() {
		var input_txt = $('#btn-input').val();
		$('#btn-input').val('');
		$.ajax({
			type: 'POST',
			url: 'api/message.php',
			async: false,
			data: {to_email: to_email, from_email: from_email, content: input_txt},
		}).done(function(r) {
			var rs = eval('(' + r + ')');
			if(rs.status == "OK") {
				addSendBubble(input_txt);
			} else {
				alert("發送失敗");
			}
		});

	});

	// 初始化io对象
	var socket = io('http://' + document.domain + ':2120');
	// 当socket连接后发送登录请求
	socket.on('connect', function(){socket.emit('login', uid);});
	// 当服务端推送来消息时触发，这里简单的aler出来，用户可做成自己的展示效果
	socket.on('new_msg', function(content){
		addReceiveBubble(content);
	});
});

function addSendBubble(msg) {
	$div1 = $('<div>').addClass('row msg_container base_sent');
	$div2 = $('<div>').addClass('col-xs-10 col-md-10');
	$div3 = $('<div>').addClass('messages msg_sent');
	$p = $('<p>').html(msg);
	$time = $('<time>').attr('datetime', '2009-11-13T20:00').html(fetchCurrentTime());
	
	$('.msg_container_base').append($div1.append($div2.append($div3.append($p).append($time))));
}

function addReceiveBubble(msg) {
	$div1 = $('<div>').addClass('row msg_container base_receive');
	$div2 = $('<div>').addClass('col-xs-10 col-md-10');
	$div3 = $('<div>').addClass('messages msg_receive');
	$p = $('<p>').html(msg);
	$time = $('<time>').attr('datetime', '2009-11-13T20:00').html(fetchCurrentTime());
	
	$('.msg_container_base').append($div1.append($div2.append($div3.append($p).append($time))));
}

function fetchCurrentTime() {
	var currentdate = new Date(); 
	var datetime = currentdate.getHours() + ":"  
                + currentdate.getMinutes() + ":" 
                + currentdate.getSeconds();
    return datetime;            
}

</script>