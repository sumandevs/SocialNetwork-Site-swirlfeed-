$(document).ready(function() {
	$('#submit_profile_post').click(function() {
		$.ajax({
			type: 'POST',
			url: 'includes/handlers/ajax_submit_profile_post.php',
			data: $('form.profile_post').serialize(),
			success: function(msg) {
				$('#post_form').hide();
				location.reload();
			},
			error: function() {
				alert('Failure!');
			}
		});
	});
});

function getUsers(value, user) {
	console.log('Function was called');

	$.post('includes/handlers/ajax_friend_search.php', { query: value, userLoggedIn: user }, function(data) {
		console.log('Ajax success handler was called');
		console.log('Data returned:' + data);

		$('.results').html(data);
	});
}
