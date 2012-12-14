$(document).ready(function() {
	$("#login_form").submit(function() {
		$("#login_feedback").hide();
		logon_user(
				$("input[name='login_name']")[0].value,
				$("input[name='login_password']")[0].value
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
					main();
			}
	);
});



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
					$("#login").hide('slow');
					main();
				} else {
					$("#login_feedback").show();
					logon();
				}
			}
	);
}

function main() {
	$("body").append('hi');
}