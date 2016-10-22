<?php

$stmt = array();

$stmt['user'] = array(
	'get_full_list' => 	$db->prepare('SELECT id, name, role FROM tbl_user'),
	'get_single_item' =>$db->prepare('SELECT * FROM tbl_user WHERE id = :id'),
	'add_item' => 		$db->prepare('INSERT INTO tbl_user (name, role) VALUES (:name, :role)'),
	'delete_item' => 	$db->prepare('DELETE FROM tbl_user WHERE id = :id'),
	'update_item' =>	$db->prepare('UPDATE tbl_user SET name = :name, role = :role WHERE id = :id')
);

/*
$stmt['user']['add_item']->execute(array(
	':name' => 'Horn',
	':role' => 'Admin'
));
*/

$stmt['player'] = array(
	'get_full_list' => 	$db->prepare('SELECT id, name FROM tbl_player'),
	'get_single_item' =>$db->prepare('SELECT * FROM tbl_player WHERE id = :id'),
	'add_item' => 		$db->prepare('INSERT INTO tbl_player (name) VALUES (:name)'),
	'delete_item' => 	$db->prepare('DELETE FROM tbl_player WHERE id = :id'),
	'update_item' =>	$db->prepare('UPDATE tbl_player SET name = :name WHERE id = :id')
);

/*
$stmt['player']['add_item']->execute(array(
	':name' => 'Horn'
));
*/

$stmt['card'] = array(
	'get_full_list' => 	$db->prepare('SELECT * FROM tbl_card'),
	'get_single_item' =>$db->prepare('SELECT * FROM tbl_card WHERE id = :id'),
	'add_item' => 		$db->prepare('INSERT INTO tbl_card (name, points, url) VALUES (:name, :points, :url)'),
	'delete_item' => 	$db->prepare('DELETE FROM tbl_card WHERE id = :id'),
	'update_item' =>	$db->prepare('UPDATE tbl_card SET name = :name, points = :points, url = :url WHERE id = :id')
);

/*
$stmt['card']['add_item']->execute(array(
	':name' => 'Superstar',
	':points' => 3,
	':url' => 'www.example.com'
));
*/