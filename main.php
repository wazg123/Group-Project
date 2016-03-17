<?php

include 'functions.php';

// receive a classification from Mammal Web
$classification = $_POST['data'];

// update the classification received
$servername = "hostname";
$username = "username";
$password = "password";
$dbname = "mammaweb";

// create connection
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// check connection
if ($conn->connect_error) {
	// do something here
}
// if connection succeeds, update classification in database
else {
	$cla_len = count($classification);
	for ($i = 0; $i < $cla_len; $i++) {
		$animal_id = $classification[$i]['animal_id'];
		$photo_id = $classification[$i]['photo_id'];
		$species = $classification[$i]['species'];
		$gender = $classification[$i]['gender'];
		$age = $classification[$i]['age'];
		$number = $classification[$i]['number'];
		$query = "INSERT INTO animal VALUE($animal_id,$photo_id,$species,$gender,$age,$number)";
		$conn->query($query);
	}
}

// check criteria on the photo, if criteria met, do final classification
$photo_id = $classification[0]['photo_id'];
check_classify($photo_id,$conn);
?>