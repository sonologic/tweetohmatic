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
		loading();
		$.getJSON(
				'json.php?c=st&'+$("#twitter_auth_form").serialize(),
				{},
				function(data) {
					loaded();
				}
		)
		return false;
	});
	
	$("#tweet_form").submit(function() {
		$("#tweet_feedback").hide();
		loading();
		$.getJSON(
				'json.php?c=t&'+$("#tweet_form").serialize(),
				{},
				function(data) {
					loaded();
					if(data.error=='') {
						$("#tweet_feedback").text(data.feedback);
					} else {
						$("#tweet_feedback").text(data.error);
					}
					$("#tweet_feedback").show();
				}
		)
		return false;		
	});
	
	$("textarea[name='status']").keypress(function(event) {
		console.log(event.keyCode);
		len = $("textarea[name='status']").val().length;
		console.log(len);
		console.log($("textarea[name='status']").val());
		if(typeof event.keyCode !== 'undefined') {
			if(event.keyCode==0) {
				len+=1;
				left = 140-len;
				if(len>140)
					return false;
				else
					$("#tweet_left").text(left);
			} else {
				if(event.keyCode==8 || event.keyCode==46) {
					left = 140-len+1;
					$("#tweet_left").text(left);				
				}
			}
		} else {
			left = 140-len;
			$("#tweet_left").text(left);			
		}
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

function loading() {
	$("#loading").fadeIn('fast');
}

function loaded() {
	$("#loading").fadeOut('fast');
}

function moderate() {
	loading();
	$.getJSON(
			'json.php',
			{'c':'gq'},
			function(data) {
				loaded();
				$("#moderate table tbody").empty();
				for(var idx in data.queue) {
					item=data.queue[idx];
					$("#moderate table tbody").append(
							"<tr><td>"+item.ts+"</td>"+
							"<td>"+item.status+"</td>"+
							"<td>"+item.username+"</td>"+
							"<td>"+
							"<span class='moderate_action' id='del_"+item.username+"_"+item.ts+"'>discard</span> "+
							"<span class='moderate_action' id='ack_"+item.username+"_"+item.ts+"'>approve</span>"+
							"</td>"+
							"</tr>"
					);
				}
				
				for(var idx in data.queue) {
					item=data.queue[idx];
					$("#del_"+item.username+"_"+item.ts).data('item',item);
					$("#del_"+item.username+"_"+item.ts).click(function(event) {
						loading();
						item=$(event.currentTarget).data('item');
						$.getJSON(
								'json.php',
								{
									'c':'d',
									'u':item.username,
									'ts':item.ts
								},
								function(data) {
									loaded();
									if(data.error=='') {
										$(event.currentTarget).parent().parent().remove();
									} else {
										alert('Error: '+data.error);
									}
								}
						);
					});
					$("#ack_"+item.username+"_"+item.ts).data('item',item);
					$("#ack_"+item.username+"_"+item.ts).click(function(event) {
						loading();
						item=$(event.currentTarget).data('item');
						$.getJSON(
								'json.php',
								{
									'c':'at',
									'u':item.username,
									'ts':item.ts
								},
								function(data) {
									loaded();
									if(data.error=='') {
										$(event.currentTarget).parent().parent().remove();
									} else {
										alert('Error: '+data.error);
									}
								}
						);
					});					
				}
				
				$(".desktop").hide();
				$("#moderate").show('slow');				
			});
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
	loading();
	$.getJSON(
			'json.php',
			{'c':'ul'},
			function(data) {
				if(data.error=='') {
					$("#user_admin table tbody").empty();
					for(var username in data.users) {
						perm=data.users[username];
						
						perm_types=[
						            'tweet',
						            'twitter_account',
						            'user_admin',
						            'moderate'
						           ];
						
						perms="";
						for(pidx in perm_types) {
							perms += '<input type="checkbox" name="'+perm_types[pidx]+'" value="1"';
							
							if($.inArray(perm_types[pidx],perm)!=-1)
									 perms += ' checked="checked"';
							perms += '/>'+perm_types[pidx]+'<br/>';
						}
						
						$("#user_admin table tbody").append(
								"<tr><td>"+username+"</td>"+
								"<td>..</td>"+
								"<td><form id='user_"+username+"' action='#'>"+perms+
								"<input type='submit' name='save' value='save'>"+
								"</form></td>"+
								"</tr>"
						);
					
						$("#user_"+username).data('user',username);
						$("#user_"+username).submit(function(event) {
							loading();
							user = $(event.currentTarget).data('user');
							$.getJSON(
									'json.php',
									'c=su&u='+user+'&'+$(event.currentTarget).serialize(),
									function(data) {
										loaded();
										if(data.error=='') {
											alert('User permission saved.');
										} else {
											alert('Error: '+data.error);
										}									
									}).error(function() {
										loaded();
										alert('Fatal error!');
									});
							return false;
						});
					}
					loaded();
					$(".desktop").hide();
					$("#user_admin").show();
				} else {
					loaded();
					alert('User list request failed!')
				}
			}).error(function() {
				alert('Fatal error! Please reload.');
			});
	$(".desktop").hide();
	$("#user_admin").show('slow');
}

function tweet() {
	$("textarea[name='status']").keypress();
	$(".desktop").hide();
	$("#tweet").show('slow');
}

var menu_items = [
                  	{ perm:'moderate', label:'moderate', cb:moderate},
                  	{ perm:'user_admin', label:'users', cb:user_admin},
                  	{ perm:'twitter_account', label:'twitter', cb:edit_twitter_account},
                 ];

function populate_menu(perm) {
	$("#menu").empty();
	template = $("#menu_template").html();
	
	itemHtml = template.replace("menu_template_id","menu_tweet").replace("menu_template_text","tweet");
	$("#menu").append(itemHtml);
	$("#menu_tweet").click(tweet);
	
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
