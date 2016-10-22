<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header('Content-Type: text/html; charset=utf-8'); 
if(!isset($_SERVER['PATH_INFO'])) throw new Exception('Invalid Params');
 
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// connect to the sqlite database
$db = new \PDO('sqlite:db/dbfile.db'); $db->exec('PRAGMA encoding = "UTF-8"'); $db->exec('PRAGMA foreign_keys = ON'); 

// prepare tables and statements
require_once 'php/createTables.php';

// retrieve the table and key from the path
$table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;

switch ($method) {	
	case 'GET':
		switch(true) {
			case ($key == 0):
				$stmt = $db->prepare("SELECT * FROM tbl_$table"); $stmt->execute();
				echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC), JSON_NUMERIC_CHECK); break;
			case ($key > 0):
				$stmt = $db->prepare("SELECT * FROM tbl_$table WHERE id = :id"); $stmt->execute(array(':id' => $key));
				echo json_encode($stmt->fetch(\PDO::FETCH_ASSOC), JSON_NUMERIC_CHECK); break;	
		} break;
	case 'PUT':
		$set = ''; foreach($input as $col => $val) {
			if($col != 'id') $set .= (($set=='')?'':', ') . $col . ' = :' . $col;
		}
		$stmt = $db->prepare("UPDATE tbl_$table SET $set WHERE id = :id");
		
		$stmt->execute($input);
		$err = $stmt->errorInfo();
		echo ($err[0] == '23000') ? $err[2] : $stmt->rowCount();	
		break;
	case 'POST':
		if(!is_array($input)) die('Invalid JSON');
		$set = '(' . implode(', ', array_keys($input)) . ') VALUES ';
		$set.= '(:' . implode(', :', array_keys($input)) . ') ';		
		$stmt = $db->prepare("INSERT INTO tbl_$table $set");
		$stmt->execute($input);
		$err = $stmt->errorInfo();
		echo ($err[0] == '23000') ? $err[2] : $db->lastInsertId(); break;
	case 'DELETE':
		$stmt = $db->prepare("DELETE FROM tbl_$table WHERE id = :id");
		$stmt->execute(array(':id' => $key));
		$err = $stmt->errorInfo();
		//var_dump($err);
		echo ($err[0] == '23000') ? $err[2] : $stmt->rowCount();		
		break;
}












/*
switch ($method) {	
	case 'GET':
		switch(true) {
			case ($key == 0):
				$stmt = $db->prepare("SELECT * FROM tbl_$table"); $stmt->execute();
				echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC), JSON_NUMERIC_CHECK); break;
			case ($key > 0):
				$stmt = $db->prepare("SELECT * FROM tbl_$table WHERE id = :id"); $stmt->execute(array(':id' => $key));
				echo json_encode($stmt->fetch(\PDO::FETCH_ASSOC), JSON_NUMERIC_CHECK); break;
		} break;
	case 'PUT':
		$stmt[$table]['update_item']->execute($input);
		$err = $stmt[$table]['update_item']->errorInfo();
		echo ($err[0] == '23000') ? $err[2] : $stmt[$table]['update_item']->rowCount();	
		break;
	case 'POST':
		$stmt[$table]['add_item']->execute($input);
		$err = $stmt[$table]['add_item']->errorInfo();
		echo ($err[0] == '23000') ? $err[2] : $db->lastInsertId();
		break;
	case 'DELETE':
		$stmt = $db->prepare("DELETE FROM tbl_$table WHERE id = :id");
		$stmt->execute(array(':id' => $key));
		$err = $stmt->errorInfo();
		echo ($err[0] == '23000') ? $err[2] : $stmt->rowCount();		
		break;
}
*/