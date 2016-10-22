<?php
try {


// $db = new \PDO('sqlite:../db/dbfile.db');  $db->exec('PRAGMA encoding = "UTF-8"'); $db->exec('PRAGMA foreign_keys = ON');
$db->exec('CREATE TABLE IF NOT EXISTS tbl_user (
	id		INTEGER PRIMARY KEY,
	name	TEXT UNIQUE NOT NULL COLLATE NOCASE CHECK(TRIM(name) <> ""),
	role	TEXT NOT NULL CHECK(TRIM(role) <> ""),
	pass	TEXT NOT NULL DEFAULT "xxx"
)');
$db->exec('CREATE TABLE IF NOT EXISTS tbl_player (
	id		INTEGER PRIMARY KEY,
	name	TEXT UNIQUE NOT NULL COLLATE NOCASE CHECK(TRIM(name) <> ""),
	nationality	TEXT NOT NULL CHECK(TRIM(nationality) <> ""),
	avatar	TEXT NOT NULL CHECK(TRIM(avatar) <> "")
)');
$db->exec('CREATE TABLE IF NOT EXISTS tbl_card (
	id		INTEGER PRIMARY KEY,
	name	TEXT UNIQUE NOT NULL COLLATE NOCASE CHECK(TRIM(name) <> ""),
	points	INTEGER NOT NULL,
	url		TEXT NOT NULL CHECK(TRIM(url) <> ""),
	category TEXT NOT NULL CHECK(TRIM(category) <> "")
)');
$db->exec('CREATE TABLE IF NOT EXISTS tbl_game (
	id		INTEGER PRIMARY KEY,
	host	INTEGER NOT NULL,
	guest	INTEGER NOT NULL,
	date	DATETIME UNIQUE DEFAULT CURRENT_TIMESTAMP,
	author	TEXT DEFAULT "John Doe"
)');
$db->exec('CREATE TABLE IF NOT EXISTS tbl_matchup (
	playerId	INTEGER REFERENCES tbl_player(id) ON DELETE CASCADE,
	gameId		INTEGER REFERENCES tbl_game(id) ON DELETE CASCADE,
	team		TEXT NOT NULL CHECK(team="host" OR team="guest"),
	PRIMARY KEY (playerId, gameId)
)');
$db->exec('CREATE TABLE IF NOT EXISTS tbl_rating (
	playerId	INTEGER REFERENCES tbl_player(id) ON DELETE CASCADE,
	gameId		INTEGER REFERENCES tbl_game(id) ON DELETE CASCADE,
	technique	INTEGER NOT NULL,
	tactic		INTEGER NOT NULL,
	teamwork	INTEGER NOT NULL,
	PRIMARY KEY (playerId, gameId)
)');
$db->exec('CREATE TABLE IF NOT EXISTS tbl_vote (
	playerId	INTEGER REFERENCES tbl_player(id) ON DELETE CASCADE,
	gameId		INTEGER REFERENCES tbl_game(id) ON DELETE CASCADE,
	cardId		INTEGER REFERENCES tbl_card(id) ON DELETE CASCADE
	PRIMARY KEY (playerId, gameId)
)');



$db->exec("CREATE VIEW IF NOT EXISTS hostTeams AS
	SELECT GROUP_CONCAT(name, ', ') AS hostTeam, gameId from tbl_player
		JOIN tbl_matchup ON tbl_player.id = tbl_matchup.playerId
		JOIN tbl_game ON tbl_game.id = tbl_matchup.gameId
		WHERE team = 'host'
		GROUP BY gameId
");

$db->exec("CREATE VIEW IF NOT EXISTS guestTeams AS
	SELECT GROUP_CONCAT(name, ', ') AS guestTeam, gameId from tbl_player
		JOIN tbl_matchup ON tbl_player.id = tbl_matchup.playerId
		JOIN tbl_game ON tbl_game.id = tbl_matchup.gameId
		WHERE team = 'guest'
		GROUP BY gameId
");

// $db->exec("DROP VIEW IF EXISTS tbl_resultsList");
$db->exec("CREATE VIEW IF NOT EXISTS tbl_resultsList AS
	SELECT hostTeam, host, guest, guestTeam, date, date(date) AS datum, time(date) AS zeit, author
	FROM hostTeams
		JOIN guestTeams ON hostTeams.gameId = guestTeams.gameId
		JOIN tbl_game ON tbl_game.id = hostTeams.gameId
	ORDER BY date DESC
");





// $db->exec("DROP VIEW IF EXISTS tbl_resultsRanking");
$db->exec("CREATE VIEW IF NOT EXISTS tbl_resultsRanking AS
	SELECT
		name,
		nationality,
		avatar,
		SUM(goals) AS goals,
		SUM(goalsAgainst) AS goalsAgainst,
		SUM(goals - goalsAgainst) as goalsDiff,
		SUM(CASE WHEN goals > goalsAgainst then 1 else 0 end) as wins,
		SUM(CASE WHEN goals < goalsAgainst then 1 else 0 end) as losses,
		SUM(CASE WHEN goals = goalsAgainst then 1 else 0 end) as draws,
		SUM(CASE WHEN goals > goalsAgainst then 3 else 0 end) +
			SUM(CASE WHEN goals = goalsAgainst then 1 else 0 end) AS points,
		COUNT(name) AS games
	FROM
		(
			SELECT *, host AS goals, guest AS goalsAgainst
			FROM tbl_player
				JOIN tbl_matchup ON tbl_player.id = tbl_matchup.playerId
				JOIN tbl_game ON tbl_game.id = tbl_matchup.gameId
			WHERE team = 'host'
			UNION ALL
			SELECT *, guest AS goals, host AS goalsAgainst
			FROM tbl_player
				JOIN tbl_matchup ON tbl_player.id = tbl_matchup.playerId
				JOIN tbl_game ON tbl_game.id = tbl_matchup.gameId
			WHERE team = 'guest'
		)

	GROUP BY name
	ORDER BY points DESC

");


$db->exec('DROP VIEW tbl_cardsPerPlayer');
$db->exec('CREATE VIEW tbl_cardsPerPlayer AS
	SELECT 
		tbl_player.id, 
		tbl_player.name, 
		COUNT(tbl_player.id) AS cardCount, 
		SUM(points) as cardPoints, 
		AVG(points) as cardPointsAvg 
	FROM tbl_player JOIN tbl_vote ON tbl_vote.playerId = tbl_player.id 
	JOIN tbl_card ON tbl_vote.cardId = tbl_card.id
	GROUP BY tbl_player.id
');

$db->exec('DROP VIEW tbl_datablob');
$db->exec('CREATE VIEW tbl_datablob AS
	SELECT 
		* 
	FROM tbl_resultsRanking 
		LEFT JOIN tbl_cardsPerPlayer ON tbl_resultsRanking.id = tbl_cardsPerPlayer.id
');


// $db->exec('DROP VIEW tbl_ratingsAVG');
$db->exec('CREATE VIEW tbl_ratingsAVG AS
	SELECT 
		AVG(technique) AS technique,
		AVG(tactic) AS tactic,
		AVG(teamwork) AS teamwork
	FROM tbl_rating
');


//DUMMY-DATA

$new_player = $db->prepare('INSERT OR IGNORE INTO tbl_player (name, nationality, avatar) VALUES (:name, :nationality, :avatar)');
$new_player->execute(array(':name' => 'Zalippi', ':nationality' => 'Italian Stallion', ':avatar' => 'zal.png'));
$new_player->execute(array(':name' => 'Svennson', ':nationality' => 'Swedish', ':avatar' => 'jahuhuiti.png'));
$new_player->execute(array(':name' => 'Bobo', ':nationality' => 'Aarisch', ':avatar' => 'glatze.png'));
$new_player->execute(array(':name' => 'El Horn', ':nationality' => 'Unknown', ':avatar' => 'steppenzausel.png'));

$new_game = $db->prepare('INSERT OR IGNORE INTO tbl_game (host, guest, date, author) VALUES (:host, :guest, :date, :author)');
$new_game->execute(array(':host' => 2, ':guest' => 0, ':date' => '2001-01-01T11:00:00.000Z', ':author' => 'System'));
$new_game->execute(array(':host' => 3, ':guest' => 3, ':date' => '2001-01-01T11:30:00.000Z', ':author' => 'System'));

$new_matchup = $db->prepare('INSERT OR IGNORE INTO tbl_matchup (playerId, gameId, team) VALUES (:playerId, :gameId, :team)');
$new_matchup->execute(array(':playerId' => 2, ':gameId' => 1, ':team' => 'host'));
$new_matchup->execute(array(':playerId' => 1, ':gameId' => 1, ':team' => 'guest'));
$new_matchup->execute(array(':playerId' => 3, ':gameId' => 1, ':team' => 'guest'));
$new_matchup->execute(array(':playerId' => 4, ':gameId' => 2, ':team' => 'host'));
$new_matchup->execute(array(':playerId' => 1, ':gameId' => 2, ':team' => 'host'));
$new_matchup->execute(array(':playerId' => 2, ':gameId' => 2, ':team' => 'guest'));



} catch (PDOException $e) {
	echo  $e->getMessage();
}
