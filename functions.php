<?php

function check_classify(photo_id, $conn) {
	//select all distinguish users that made classifications on a selected photo
	$sql = "SELECT DISTINCT `person_id` FROM `animal` WHERE `photo_id` = $photo_id ORDER BY `animal`.`timestamp` ASC";
	$result = $conn->query($sql);
	//analysis all classifications for retirement check
	$classifications = array();
	while ($row = $result->fetch_array()) {
		$person_id = $row[person_id];
		//query all classifications made by certain user on certain photo
		$personal_sql = "SELECT * FROM `animal` WHERE `photo_id` = $photo_id AND `person_id` = $person_id ORDER BY `timestamp` ASC ";
		$personal_result = $conn->query($personal_sql);
		//initialize all variables needed for a combined classification
		$num_animals = 0;
		$species_list = array();
		while ($personal_row = $personal_result->fetch_array()) {
			$species = $personal_row[species];
			//check if the species is nothing or human
			if ($species == "86" or $species == "87") {
				$species_list[] = $species;
				break;
			}
			//if there is/are actual animal(s)
			else {
				//add the number of animals to count
				$num_animals =  $num_animals + $personal_row[number];
				//record the species in the list
				if (!in_array($species, $species_list) and $species != "97") {
					$species_list[] = $species;
				}
			}
		}
		asort($species_list);
		//put the integrated classification into list
		$classifications[] = array(
			"person_id" => $person_id,
			"number_animals" => $num_animals,
			"species_list" => $species_list
		);
	}
	//the size of $classifications
	$cla_len = count($classifications);
	//the flags that marks the progress of retirement check
	$blank_count = 0; #flag up at 5
	$blank_consensus_count = 0; #flag up at 10
	$consensus_count = array(); #flag up at 10
	$complete_count = 0; #flag up at 25
	//the result of the check
	$result = 0;
	//check all criterias
	for ($i = 0; $i < $cla_len; $i++) {
		$species = $classifications[$i];
		if (in_array("86", $species) or in_array("87", $species)) {
			$blank_count++;
			$blank_consensus_count++;
		}
		else if (array_key_exists($species, $consensus_count)) {
			$consensus_count[$species]++;
			$complete_count++;
		}
		else {
			$consensus_count[$species] = 1;
			$complete_count++;
		}
		if ($i ==4 and $blank_count == 5) {
			$result = 1;
			break;
		}
		else if ($blank_consensus_count == 10) {
			$result = 1;
			break;
		}
		else if ($consensus_count == 10) {
			$result = 2;
			break;
		}
		else if ($complete_count == 25) {
			$result = 2;
			break;
		}
		else {
			$result = 0;
			break;
		}
	}
	switch ($result) {
		case 0:
			break;
		
		case 1:
			$sql = "INSERT INTO status VALUE ($photo_id,1,0); INSERT INTO classification (`photo_id`,`species`,`number`,`timestamp`) VALUE ($photo_id,86,0,DEFAULT)";
			$conn->query($sql);			
			break;
			
		case 2:
			break;
	}
}

/**
 * This function get the median number of animals from analysising all classifications on a certain photo
 * @param array $classifications
 * @return int $median
 */
function median($classifications) {
	$nums_animals = array(); // new array to hold all numbers of animals
	for ($i = 0; $i < count($classifications); $i++) {
		$nums_animals[] = $classifications[$i]['number_animals'];
	}
	// calculate the median of $num_animals
    sort($nums_animals);
    $count = count($nums_animals); //total numbers in array
    $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
    if($count % 2) { 
    	// odd number, middle is the median
        $median = $nums_animals[$middleval];
    } else { 
    	// even number, calculate avg of 2 medians
        $low = $nums_animals[$middleval];
        $high = $nums_animals[$middleval+1];
        $median = floor((($low+$high)/2));
    }
    return $median;
}

/**
 * This function counts how many votes there are for each species
 * @param array $classifications
 * @return array $counts
 */
function count_species($classifications) {
	$counts = array(); // array that takes key->value pairs as species->number_of_votes
	for ($i = 0; $i < count($classifications); $i++) {
		$species = $classifications[$i]['species'];
		for ($j = 0; $j < count($species); $j++) {
			if (array_key_exists($species[$j], $counts)) {
				$counts[$species[$j]]++;
			}
			else {
				$counts[$species[$j]] = 1;
			}
		}
	}
	arsort($counts); // sort $counts in descending order by values
	return $counts;
}
?>