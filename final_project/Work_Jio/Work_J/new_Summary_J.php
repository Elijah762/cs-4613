<?php

	/*
	* Likely a faster way at doing this, will look into later
	* Meant to get data from DB, Display data into Simulation Summary in 'mapDev.php'
	*
	*/
	/******************************DB ACCESS*********************************************/
	$hostname="localhost";
	$username="DBuser";
	$password="vqttDqsE*cBI_8(B";
	$db="senior_design_db";
	$mysqli= new mysqli($hostname, $username, $password, $db);
	if (mysqli_connect_errno())
	{
		die("Error connecting to database: ".mysqli_connect_error());
	}
	
	/************************FIRST LINE IN DISPLAYED SUMMARY********************************************/	
//verify sql statement
								
	//**************************************THIRD LINE IN DISPLAYED SUMMARY*******************************************//
    echo '<br>';
	echo '<p><b><center>Total Energy Produced in Simulation: </center></b></p>'; //maybe useful
	$result_Gained_SQL= "SELECT SUM(`pow_produce`) AS value_sum FROM `node_info`;";
	$result_Gained = $mysqli->query($result_Gained_SQL) or
		die("Something went wrong with $result_Gained_SQL".$mysqli->error);

			$row = mysqli_fetch_assoc($result_Gained); 
			$sum = $row['value_sum'];
			echo '<p><li><center>' .number_format($sum). ' MW/h</center></li></p>';


	//************************************FOURTH LINE IN DISPLAYED SUMMARY******************************************//		
	echo '<p><b>Total Energy Demand in Simulation: </b></p>';					
	$result_Lost_SQL= "SELECT SUM(`pow_demand`) AS value_sum FROM `node_info`;";
	$result_Lost = $mysqli->query($result_Lost_SQL) or
		die("Something went wrong with $result_Lost_SQL".$mysqli->error);

			$row = mysqli_fetch_assoc($result_Lost); 
			$sum = $row['value_sum'];
			echo '<p><li> -'.number_format($sum).' MW/h</li></p>';  //maybe remove -   ?
    //*********************************************DO LATER***********************************************//
	echo '<p><b>Total Dependent Population:  </b></p>';
	$result_Population_SQL= "SELECT SUM(`node_popServe`) AS value_sum FROM `node_info`;";
	$result_Population = $mysqli->query($result_Population_SQL) or
		die("Something went wrong with $result_Population_SQL".$mysqli->error);

			$row = mysqli_fetch_assoc($result_Population); 
			$sum = $row['value_sum'];
			echo '<p><li>' .number_format($sum). '</li></p>';
		/**********************************SECOND LINE IN DISPLAYED SUMMARY**********************************/
		echo '<p><hr><b>Simulated Affected Nodes: </b></p>';  //VERIFY
		$sql="SELECT `node_id` from `node_info` order by `node_region`;";
		$result = $mysqli->query($sql) or
				die("Something went wrong with $sql".$mysqli->error);
		$result_sim_Check = mysqli_num_rows($result);
			//$output = array();
			//var_dump($result);

		if ($result_sim_Check > 0) {

			//echo 'Values:';
			//echo "<p>\nEntry $data[node_id]: $data[node_acronym] + $data[node_name] + $data[node_region]</p>";
			echo '<p><li>Found: ' . $result_sim_Check  .'</li></p>';
		}

		else {
			echo '<p>Found: 0 </p>';
		}	
			/****************************************************************************************************/
	echo '<p><hr><b>Manually Affected Nodes: </b></p>';
		$sql="SELECT `node_id`, `node_acronym`, `node_name`,`node_active`, `node_region` from `node_info` where `node_active` = '0' order by `node_region`;";
		$result = $mysqli->query($sql) or
				die("Something went wrong with $sql".$mysqli->error);
		$result_man_Check= mysqli_num_rows($result);
		$total = 0;
		//echo 'HERE:' . $result_man_Check;
		if (intval($result_man_Check) > 0) {
			while ($data=$result->fetch_array(MYSQLI_ASSOC))
			{
				//echo 'Values:';
				echo "<p><li>$data[node_id] $data[node_acronym] $data[node_name] $data[node_region]</li></p>";			
			}
		}
		else {
			echo '<p>Found: 0 </p>';
		}
/***
*	ALSO MAYBE JUST NUMBER INSTEAD OF NODE INFO OR JUST NODE ACRONYM
*	FOR FUTURE MAYBE BUTTON TO POP UP AND LIST ALL MANUALLY AFFECTED NODES FROM MAP IN SUMMARY
*****/
   //*********************************************It works just try in map ***********************************************//
/*
	$arrayMarker = array();
	function getNodeRegion($mysqli,$myPhpMap) {    
		$sql="SELECT `node_id`, `node_acronym`, `node_name`, `node_region`, `pow_produce`, `pow_consume`, `node_popServe`, `node_lat`, `node_lon` from `node_info` order by `node_region`;";
		$result = $mysqli->query($sql) or
			die("Something went wrong with $sql".$mysqli->error);
		$result_Check = mysqli_num_rows($result);
		//$output = array();
		//var_dump($result);
		if ($result_Check > 0) {
			$i = 0;
		   	while($rowData = mysqli_fetch_assoc($result))
			{
			   //var_dump($rowData);
					if ($rowData['node_region'] == 'Texas') {
						$lat = $rowData['node_lat'];
						$lon = $rowData['node_lon'];
						$name = $rowData['node_name'];
						$region = $rowData['node_region'];
						$acronym = $rowData['node_acronym'];
						$id = $rowData['node_id'];
						$produce = $rowData['pow_produce'];
						$consume = $rowData['pow_consume'];
						$population = $rowData['node_popServe'];
						
						$node = 'marker' . $i;
						
						$arrayMarker = $rowData;

						//echo $myPhpMap;
						
						
						//echo  $node . '= L.marker([31.0000, -97.7333]).bindPopup(' . $name . ',' . $region . '<br> Energy Produced: ' . $produce .  '<br> Energy Consumed:  ' . $consume . '<br> Population Affected:  ' . $population . ').addTo(' . $myPhpMap .');';
                     	//echo  $node . '= L.marker(['  . $lat . ',' . $lon .']).bindPopup(' . $name . ',' . $region . '<br> Energy Produced: ' . $produce .  '<br> Energy Consumed:  ' . $consume . '<br> Population Affected:  ' . $population . ').addTo(' . $myPhpMap .');';
						$i++;
			 	}
			}
			return $arrayMarker;
		}
		return "N/A";	
	}

$sum =  getNodeRegion($mysqli,$arrayMarker);
//shows its an array
echo $sum;
//displays contents
//var_dump($sum);
for ($i = 0; $i < count($sum); $i++)
{
	echo $sum["node_region"];
	echo $sum["node_name"];
}*/
?>



		
