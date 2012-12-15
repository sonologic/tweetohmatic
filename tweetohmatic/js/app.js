$(document).ready(function() {
	$("#login_form").submit(function() {
		$("#login_feedback").hide();
		logon_user(
				$("input[name='login_name']")[0].value,
				$("input[name='login_password']")[0].value
		)
		return false;
	});
	
	$("#twitter_auth_form").submit(function() {
		$.getJSON(
				'json.php?c=st&'+$("#twitter_auth_form").serialize(),
				{},
				function(data) {
					
				}
		)
		return false;
	});
	
	$("#tweet_form").submit(function() {
		$.getJSON(
				'json.php?c=t&'+$("#tweet_form").serialize(),
				{},
				function(data) {
					
				}
		)
		return false;		
	});
	
	$.getJSON(
			'json.php',
			{'c':'ia'},
			function(data) {
				if(data.auth==0)
					logon();
				else
					main(data.perm);
			}
	);
});

function moderate() {
	$(".desktop").hide();
	$("#moderate").show('slow');
}

function edit_twitter_account() {
	$.getJSON(
			'json.php',
			{'c':'gt'},
			function(data) {
				for(var d in data) {
					$("input[name='"+d+"']").val(data[d]);
				}
				$(".desktop").hide();
				$("#twitter_account").show('slow');
			});
}

function user_admin() {
	$(".desktop").hide();
	$("#user_admin").show('slow');
}

function tweet() {
	$(".desktop").hide();
	$("#tweet").show('slow');
}

var menu_items = [
                  	{ perm:'tweet', label:'tweet', cb:tweet},
                  	{ perm:'queue', label:'tweet', cb:tweet},
                  	{ perm:'moderate', label:'moderate', cb:moderate},
                  	{ perm:'user_admin', label:'users', cb:user_admin},
                  	{ perm:'twitter_account', label:'twitter', cb:edit_twitter_account},
                 ];

function populate_menu(perm) {
	$("#menu").empty();
	template = $("#menu_template").html();
	for(var item_index in menu_items) {
		item=menu_items[item_index];
		renderItem=false;
		for(var perm_index in perm) {
			if(perm[perm_index] == item.perm)
				renderItem=true;
		}
		if(renderItem) {
			itemHtml = template.replace("menu_template_id","menu_"+item.perm).replace("menu_template_text",item.label);
			$("#menu").append(itemHtml);
			$("#menu_"+item.perm).click(item.cb);
		}
	}
}


function logon() {
	$("#login").show('slow');
}

function logon_user(user, password) {
	// @todo validate input
	$.getJSON(
			'json.php',
			{'c':'a','u':user,'p':password},
			function(data) {
				if(data.auth==1) {
					main(data.perm);
				} else {
					$("#login_feedback").show();
					logon();
				}
			}
	);
}

function main(perm) {
	$("#login").hide('slow');
	populate_menu(perm);

}