<?php

$update_views = true;
$insert_dummy_data = false;

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

if($update_views) $db->exec("DROP VIEW IF EXISTS tbl_resultsList");
$db->exec("CREATE VIEW IF NOT EXISTS tbl_resultsList AS
	SELECT hostTeam, host, guest, guestTeam, date, date(date) AS datum, time(date) AS zeit, gameId, author
	FROM hostTeams
		JOIN guestTeams ON hostTeams.gameId = guestTeams.gameId
		JOIN tbl_game ON tbl_game.id = hostTeams.gameId
	ORDER BY date DESC
");





if($update_views) $db->exec("DROP VIEW IF EXISTS tbl_resultsRanking");
$db->exec("CREATE VIEW IF NOT EXISTS tbl_resultsRanking AS
	SELECT
		id,
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

if($update_views) $db->exec('DROP VIEW IF EXISTS tbl_cardsList');
$db->exec('CREATE VIEW IF NOT EXISTS tbl_cardsList AS
	SELECT 
		tbl_player.id, 
		tbl_player.name, 
		tbl_player.nationality, 
		tbl_player.avatar, 
		points, 
		url,
		category		
	FROM tbl_player JOIN tbl_vote ON tbl_vote.playerId = tbl_player.id 
	JOIN tbl_card ON tbl_vote.cardId = tbl_card.id
	ORDER BY tbl_player.name
');


if($update_views) $db->exec('DROP VIEW IF EXISTS tbl_ratingsList');
$db->exec('CREATE VIEW IF NOT EXISTS tbl_ratingsList AS
	SELECT 
		tbl_player.id, 
		tbl_player.name, 
		tbl_player.nationality, 
		tbl_player.avatar, 
		technique, 
		tactic,
		teamwork,
		CAST((technique + tactic + teamwork)AS FLOAT) / 3 AS overall
	FROM tbl_player JOIN tbl_rating ON tbl_rating.playerId = tbl_player.id 
	ORDER BY tbl_player.name
');




if($update_views) $db->exec('DROP VIEW IF EXISTS tbl_ratingsRanking');
$db->exec('CREATE VIEW IF NOT EXISTS tbl_ratingsRanking AS
	SELECT 
		tbl_player.id, 
		tbl_player.name, 
		tbl_player.nationality, 
		tbl_player.avatar,
		COUNT(tbl_player.id) AS ratingCount, 
		SUM(technique) AS techniqueSum, 
		SUM(tactic) AS tacticSum,
		SUM(teamwork) AS teamworkSum,
		SUM(technique + tactic + teamwork) as overallSum,
		AVG(technique) AS techniqueAvg, 
		AVG(tactic) AS tacticAvg,
		AVG(teamwork) AS teamworkAvg
	FROM tbl_player JOIN tbl_rating ON tbl_rating.playerId = tbl_player.id 
	GROUP BY tbl_player.id
	ORDER BY overallSum DESC, ratingCount ASC
');

if($update_views) $db->exec('DROP VIEW IF EXISTS tbl_cardsRanking');
$db->exec('CREATE VIEW IF NOT EXISTS tbl_cardsRanking AS
	SELECT 
		tbl_player.id, 
		tbl_player.name, 
		tbl_player.nationality, 
		tbl_player.avatar,
		COUNT(tbl_player.id) AS cardCount, 
		SUM(points) as cardPoints, 
		AVG(points) as cardPointsAvg 
	FROM tbl_player JOIN tbl_vote ON tbl_vote.playerId = tbl_player.id 
	JOIN tbl_card ON tbl_vote.cardId = tbl_card.id
	GROUP BY tbl_player.id
	ORDER BY cardPointsAvg DESC, cardPoints DESC, cardCount ASC, tbl_player.name ASC
');


if($update_views) $db->exec('DROP VIEW IF EXISTS tbl_datablob');
$db->exec('CREATE VIEW IF NOT EXISTS tbl_datablob AS
	SELECT 
		tbl_resultsRanking.id,
		tbl_resultsRanking.name, 
		tbl_resultsRanking.nationality, 
		tbl_resultsRanking.avatar, 
		tbl_resultsRanking.games, 
		tbl_resultsRanking.points, 
		tbl_resultsRanking.wins, 
		tbl_resultsRanking.draws, 
		tbl_resultsRanking.losses, 
		tbl_resultsRanking.goals, 
		tbl_resultsRanking.goalsAgainst, 
		tbl_resultsRanking.goalsDiff, 
		tbl_cardsRanking.cardPoints, 
		tbl_cardsRanking.cardCount, 
		tbl_cardsRanking.cardPointsAvg,
		tbl_ratingsRanking.techniqueAvg,
		tbl_ratingsRanking.tacticAvg,
		tbl_ratingsRanking.teamworkAvg,
		tbl_ratingsRanking.techniqueSum,
		tbl_ratingsRanking.tacticSum,
		tbl_ratingsRanking.teamworkSum,		
		tbl_ratingsRanking.overallSum,
		tbl_ratingsTrend.technique AS techniqueTrend,
		tbl_ratingsTrend.tactic AS tacticTrend,
		tbl_ratingsTrend.teamwork AS teamworkTrend
	FROM tbl_resultsRanking 
		LEFT JOIN tbl_cardsRanking ON tbl_resultsRanking.id = tbl_cardsRanking.id
		LEFT JOIN tbl_ratingsRanking ON tbl_resultsRanking.id = tbl_ratingsRanking.id
		LEFT JOIN tbl_ratingsTrend ON tbl_resultsRanking.id = tbl_ratingsTrend.id
');


if($update_views) $db->exec('DROP VIEW IF EXISTS tbl_ratingsWithDate');
$db->exec('CREATE VIEW IF NOT EXISTS tbl_ratingsWithDate AS
	SELECT 
		playerId,
		gameId,
		technique,
		tactic,
		teamwork,
		DATE(date) AS datum, 
		TIME(date) AS zeit
	FROM tbl_rating
		JOIN tbl_game ON tbl_rating.gameId = tbl_game.id
	ORDER BY datum DESC, zeit DESC
');

if($update_views) $db->exec('DROP VIEW IF EXISTS tbl_ratingsTrend');
$db->exec('CREATE VIEW IF NOT EXISTS tbl_ratingsTrend AS
	SELECT 
		id,
		name,
		nationality,
		avatar,
		(SELECT AVG(technique) FROM
			(SELECT technique FROM tbl_ratingsWithDate WHERE playerId = id LIMIT 3
				)) AS technique,
		(SELECT AVG(tactic) FROM
			(SELECT tactic FROM tbl_ratingsWithDate WHERE playerId = id LIMIT 3
				)) AS tactic,
		(SELECT AVG(teamwork) FROM
			(SELECT teamwork FROM tbl_ratingsWithDate WHERE playerId = id LIMIT 3
				)) AS teamwork,
		(SELECT COUNT(playerId) FROM
			(SELECT playerId FROM tbl_ratingsWithDate WHERE playerId = id LIMIT 3
				)) AS trendRatings,
		(SELECT COUNT(playerId) FROM tbl_ratingsWithDate WHERE playerId = id
			) AS allRatings		
	FROM tbl_player
		JOIN tbl_rating ON tbl_rating.playerId = tbl_player.id
	WHERE trendRatings > 2
	GROUP BY name
');






if($update_views) $db->exec('DROP VIEW IF EXISTS tbl_cardsPerPlayer');
$db->exec('CREATE VIEW IF NOT EXISTS tbl_cardsPerPlayer AS
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



if($update_views) $db->exec('DROP VIEW IF EXISTS tbl_ratingsAVG');
$db->exec('CREATE VIEW IF NOT EXISTS tbl_ratingsAVG AS
	SELECT 
		AVG(technique) AS technique,
		AVG(tactic) AS tactic,
		AVG(teamwork) AS teamwork
	FROM tbl_rating
');


//DUMMY-DATA

if($insert_dummy_data) {
	$new_player = $db->prepare('INSERT OR IGNORE INTO tbl_player (name, nationality, avatar) VALUES (:name, :nationality, :avatar)');
	$new_player->execute(array(':name' => 'Zalippi', ':nationality' => 'Italian Stallion', ':avatar' => 'zal.png'));
	$new_player->execute(array(':name' => 'Svennson', ':nationality' => 'Swedish', ':avatar' => 'jahuhuiti.png'));
	$new_player->execute(array(':name' => 'Bobo', ':nationality' => 'Germanisch', ':avatar' => 'glatze.png'));
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
}


} catch (PDOException $e) {
	echo  $e->getMessage();
}
