<table id="resultsList" border=1 cellpadding=5></table>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script> 
<script>
	$.ajax({url: "api.php/resultsList"}).done(function(data) {
		var data = JSON.parse(data);
		$.each(data, function(i, result) {
			$('#resultsList').append('<tr><td>' + result.hostTeam + '</td><td>vs</td><td>' + result.guestTeam + 
			'</td><td>' + result.host + '</td><td>:</td><td>' + result.guest + '</td><td>' + result.datum + 
			'</td><td>' + result.zeit + '</td><td>' + result.author + '</td></tr>');
		});
	});
</script>