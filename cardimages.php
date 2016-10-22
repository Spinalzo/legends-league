<?php
$filenameArray = array();
$handle = opendir(dirname(realpath(__FILE__)).'/cardimages/');
		while($file = readdir($handle)){
				if($file !== '.' && $file !== '..'){
						array_push($filenameArray, "$file");
				}
		}

echo json_encode($filenameArray);


?>
