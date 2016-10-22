<!DOCTYPE html>
<html ng-app="myapp">
	<head>
		<meta charset="utf-8">
		<title>Murka Legend League</title>
		<link type="text/css" rel="stylesheet" href="css/default.css">
		<script src="js/angular.js"></script>
	</head>
	<body>
		<header>
			<h1>Murka League Legend Online</h1>
			<nav ng-controller="navController">
			<ul>
				<li id="usersNav" ng-click="toggleView('users')">Users
				<li id="playersNav" ng-click="toggleView('players')">Players
				<li id="cardsNav" ng-click="toggleView('cards')">Cards
				<li id="resultsNav" ng-click="toggleView('results')">Results
				<li id="votesNav" ng-click="toggleView('votes')">Votes
				<li id="ratingsNav" ng-click="toggleView('ratings')">Ratings
			</ul>
		</nav>
		<hr>
		</header>
		<main>
			<section id="users" ng-include="'html/userCtrl.html'"></section>
			<section id="players" ng-include="'html/playerCtrl.html'"></section>
			<section id="cards" ng-include="'html/cardCtrl.html'"></section>
			<section id="results" ng-include="'html/resultCtrl.html'"></section>
		</main>


	</body>
	<script src="js/murka.js"></script>
</html>
