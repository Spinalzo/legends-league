<div id="output"></div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script> 
<script>
	$.ajax({url: "api.php/resultsList"}).done(function(data) {
		var data = JSON.parse(data);
		$.each(getMatchdays(data), function(i, day) {
			document.getElementById('output').innerHTML += '<h3>' + day + '<h3>';
			$.each(getMatches(data, day), function(i, game) {
				document.getElementById('output').innerHTML += '<div>' + game.hostTeam + ' vs ' + game.guestTeam + ' ' + game.host + ':' + game.guest + '</div>';
			});
		});
	});
	getMatches = function(data, day) {
		var matches = []; data.filter(function(itm,i,a){if(itm.datum === day) matches.push(itm);});
		return matches;
	}
	getMatchdays = function(data) {
		var days = []; data.filter(function(itm,i,a){days.push(itm.datum);});		
		return days.filter(function(itm,i,a){return i==a.indexOf(itm);});
	}
</script>