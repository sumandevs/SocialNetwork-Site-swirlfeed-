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
