<?php
include_once("db_access.php");

function mapSimulationTest($value_acronym,$arrayQueue,$index, $mysqli) {
	// sql to get the json info for node connections
	if ($index == count($arrayQueue)) //checks if arraylength is we reached end of array 
	{	
		return;
	}
	/****************************************************/
	echo "Inside array queue: *".$arrayQueue[$index]."*";
	$sql="SELECT `node_acronym`, `node_connect`,`pow_produce`,`pow_demand`,`node_totalInflow`,`node_totalOutflow`,`node_statusPerc`, `node_active` from `node_info` WHERE `node_acronym` = '$arrayQueue[$index]'";
	$resultTmp = $mysqli->query($sql) or
		die("Something went wrong with $sql".$mysqli->error);
	$result = $resultTmp->fetch_assoc();
	//echo "Inside array result: ".$result."<br>";
	var_dump($result);
	
	/*****************/
	$currNodeAcronym = $result['node_acronym']; // $nodeAcronym = $result['node_acronym'];
	$currNodeConnect = $result['node_connect']; //$nodeConnectGridList = $result['node_connect'];
	$currNodeProduction = intval($result['pow_produce']); //$nodeProduction = intval($result['pow_produce']);
	
	$currNodeDemand = intval($result['pow_demand']); //nodeDemand
	$currNodeTotalInflow = intval($result['node_totalInflow']); //nodeTotalInflow
	$currNodeTotalOutflow = intval($result['node_totalOutflow']); //nodeTotalOutflow   //woried about this one
	$currNodePercent = intval($result['node_statusPerc']); //nodePercentage //not used
	$currNodeActive = $result['node_active']; //nodeActive  //Not used
	
	/*******************/
	if (($currNodeProduction + $currNodeTotalInflow) >= ($currNodeDemand + $currNodeTotalOutflow)) {
		$index++;
		mapSimulationTest($value_acronym,$arrayQueue,$index, $mysqli);
	}
	else if (($currNodeProduction + $currNodeTotalInflow) < ($currNodeDemand + $currNodeTotalOutflow)) {
		$sql = "SELECT `node_totalInflow` FROM `node_simulation_static` WHERE `node_acronym` = '$arrayQueue[$index]'";  // we select totalInflow and then name it $resultStaticOut
		$resultTmp = $mysqli->query($sql) or
				die("Something went wrong with $sql".$mysqli->error);
		$currStaticOut = $resultTmp->fetch_assoc();// we select totalInflow and then name it $resultStaticOut
		$newPercentage = ((($currNodeProduction + $currNodeTotalInflow) / ($currNodeDemand + intval($currStaticOut)) * 100));
		$newProduction = $currNodeProduction *($newPercentage/100);
		
		$num = 0;
		/***********************ERROR IS HERE****************************/
		$currNodeConnectArray = json_decode($currNodeConnect, true); //nodeConnectArray
		/**************************************************/
		//	echo "<br>";
		//	echo "<br>";
		//echo 'NODECONNECTARRAY';
		//var_dump($currNodeConnectArray);
		//echo "<br>";
		//echo 'FILE: ' . $currNodeConnectArray ;
		while ($num < count($currNodeConnectArray['gridList'])) {
			//LOOP INSIDE gridlist connection and do stuff
			$currNeighborName = $currNodeConnectArray['gridList'][$num]['name']; //$name
			$currNeighborValue = $currNodeConnectArray['gridList'][$num]['value']; //$value
			
			if ($currNeighborValue > 0) {
				$currNeighborValue = $currNeighborValue * ($newPercentage/100);
				$currNodeConnectArray['gridList'][$num]['value'] = $currNeighborValue;
				array_push($arrayQueue, $currNeighborName);
				$currNodeTotalOutflow = $currNodeTotalOutflow + $currNeighborValue;
				
				//SHOULD BE NODE CONNECT
				$sql2 = "SELECT `node_connect` FROM `node_info` WHERE `node_acronym` = '$currNeighborName'";
				$resultTmp2 = $mysqli->query($sql2) or
					die("Something went wrong with $sql2".$mysqli->error);
				$result2 = $resultTmp2->fetch_assoc();
				$neighborNodeConnect = $result2['node_connect']; //nodeConnectGridList2
				
				/*******************************************/
				$neighborNodeConnectArray = json_decode($neighborNodeConnect, true); //nodeConnectArray2
				/*******************************************/
				$neighborgridListContent = $neighborNodeConnectArray['gridList']; //gridListContent
				//$gridListContent = json_decode($gridListContent, true);
				for ($i = 0; $i < count($neighborgridListContent); $i++) {
					echo "HELLO!!";
					
					if ($currNodeAcronym == $neighborgridListContent[$i]['name']) {
						//$neighborgridListContent[$i]['value'] = round($currNeighborValue,0);
						//$updatedValue = $neighborgridListContent[$i]['value'];
						$updatedValue = round($currNeighborValue,0); //probably use this line instead^
						
						// convert to negative number
						$updatedValue = $updatedValue * (-1);
						
						echo "<br>";
						echo "<br>";
						echo '$node_acronym: '.$currNodeAcronym;
						echo "<br>";
						echo '$VALUE: '.round($currNeighborValue,0);
						echo "<br>";
						echo '$newPercentage: '.round($newPercentage,0);
						echo "<br>";
						echo "<br>";
						echo "<br>";
						echo 'BEFORE UPDATEJSON NAME CONTENTS: '. $currNeighborName;
						echo "<br>";
						//$insideJson = $neighborgridListContent; // not needed
						//$insideJson = json_encode($insideJson); // not needed
						//$insideJson = json_decode($insideJson, true); DO NOT DECODE INSIDEJSON
						
						
						/*************IMPORTANT*********************************/
						$path = '$.gridList['.$i.'].value';
						$sql3 = "UPDATE `node_info` 
								SET `node_connect`=JSON_REPLACE(`node_connect`, '$path', '$updatedValue') 
								WHERE `node_acronym` = '$currNeighborName'";
						
						$result3 = $mysqli->query($sql3) or
							die("Something went wrong with $sql3".$mysqli->error);
						/*************IMPORTANT*********************************/			
					}
				}
				$currNodeTotalOutflow = $currNodeTotalOutflow + $currNeighborValue;
				//$sql = "UPDATE `node_info` SET `node_active`='0' WHERE `node_acronym` = '$value_acronym'";
			}
			$num++;
		}//END OF WHILE LOOP
		
		$sql = "UPDATE `node_info` SET `node_connect` = '$currNodeConnect',`pow_produce` = '$newProduction',
					`node_statusPerc` = '$newPercentage', from `node_info` WHERE `node_acronym` = '$arrayQueue[$index]'";
	}	 
}
//$value_acronym = $_GET['name'];
$arrayQueue = [];
$index = 0;
$arrayQueue[$index] = 'AECI';
//$sql="TRUNCATE `node_info`; INSERT INTO `node_info` SELECT * FROM `node_simulation_static`;";
//$mysqli=db_connect("senior_design_db");

//$result = $mysqli->multi_query($sql) or
//	die("Something went wrong with $sql".$mysqli->error);

echo "We good over here!<br>";
$mysql2=db_connect("senior_design_db");
mapSimulationTest('AECI', $arrayQueue, 0, $mysql2);










?>