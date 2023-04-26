<?php
//not needed since its now part of main file
// not official file, proof of concept
//$hostname="localhost";
//$username="root";
//$password="projneu2022";
//$db="senior_design_db";
//$mysqli= new mysqli($hostname, $username, $password, $db);
//if (mysqli_connect_errno())
//{
 //   die("Error connecting to database: ".mysqli_connect_error());
//}


//CALCULATES INFLOW AND OUT FLOW, ADDS IT ALL UP
/***************CHANGING IT A LITTLE (TURNING INTO A FUNC.) JUST TO MAKE IT WORK WITH PREEXISTING CODE *******/
function InOutFlowCal($mysqli) { //gets acronym name, and mysqli data.
	// sql to get the json info for node connections
	$mysqli2=db_connect("senior_design_db");
	
	$sql="SELECT `node_acronym`, `node_connect` from `node_info`";
	$result = $mysqli2->query($sql) or
		die("Something went wrong with $sql".$mysqli2->error);
	//$data the sql result ($data will be each node) iterates through each node
	while ($data=$result->fetch_array(MYSQLI_NUM))
	{
		$jsonInfo = $data[1];
		$jsonDecoded = json_decode($jsonInfo, true);
		$nameFirstConnection = $jsonDecoded["gridList"][0]["name"];
		$lengthOfJson = count($jsonDecoded["gridList"]);
		$jsonInfo = $jsonDecoded["gridList"];
		$outflowCount = 0;
		$inflowCount = 0;
		foreach($jsonInfo as $value){
			$number = $value["value"];
			$number= str_replace(',', '', $number);
			$number = intval($number);
			if($number > 0) {
				$outflowCount = $outflowCount + $number;
			}
			else {
				$inflowCount = $inflowCount + $number;
			}
		}
		
		$sql2="UPDATE `node_info` SET `node_totalInflow`='$inflowCount' , 
		`node_totalOutflow`='$outflowCount' WHERE `node_acronym`='$data[0]'";
			$result2 = $mysqli2->query($sql2) or
		die("Something went wrong with $sql2".$mysqli2->error);
	}
}
//HANDLES THE ACTUAL MATH AND DATA SIMULATION
//RECURSION HAPPENS HERE
//THIS GOES THROUGH THE DB AND CHECKS THE NODE CONNECTIONS AND HOW THEY ARE AFFECTED
function mapSimulation($value_acronym,$arrayQueue,$index, $mysqli) {
	if ($index == count($arrayQueue)) //checks if arraylength is we reached end of array
	{	
		return;
	}
	
	if ($index == 50) //checks if arraylength is we reached end of array 
	{	
		return;
	}

	$sql="SELECT `node_acronym`, `node_connect`,`pow_produce`,`pow_demand`,`node_totalInflow`,`node_totalOutflow`,`node_statusPerc`, `node_active` from `node_info` WHERE `node_acronym` = '$arrayQueue[$index]'";
	$resultTmp = $mysqli->query($sql) or
		die("Something went wrong with $sql".$mysqli->error);
	$result = $resultTmp->fetch_assoc();
	/*****************/
	$nodeAcronym = $result['node_acronym'];
	$nodeConnectGridList = $result['node_connect'];
	$nodeProduction = intval($result['pow_produce']);
	
	$nodeDemand = intval($result['pow_demand']);
	$nodeTotalInflow = intval($result['node_totalInflow']);
	$nodeTotalOutflow = intval($result['node_totalOutflow']);
	$nodePercentage = intval($result['node_statusPerc']);
	$nodeActive = $result['node_active'];
/*	
	*/
	
	//END SIMILATION IF PERCENTAGE DOES NOT CHANGE
	
	
	/*******************/
	if (($nodeProduction + $nodeTotalInflow) >= ($nodeDemand + $nodeTotalOutflow)) {
		$index++;
		InOutFlowCal($mysqli);
		mapSimulation($value_acronym,$arrayQueue,$index, $mysqli);
	}
	else if (($nodeProduction + $nodeTotalInflow) < ($nodeDemand + $nodeTotalOutflow)) {
		$sql = "SELECT `node_totalInflow` FROM `node_simulation_static` WHERE `node_acronym` = '$arrayQueue[$index]'";
		$resultTmp = $mysqli->query($sql) or
				die("Something went wrong with $sql".$mysqli->error);
		$resultStaticOut = $resultTmp->fetch_assoc();
		if($nodeActive == 0){
			$newPercentage = 0;
		}
		else{
			$newPercentage = ($nodeProduction + $nodeTotalInflow) / ($nodeDemand + intval($resultStaticOut));
			$newPercentage = $newPercentage * 100;
		}
		
		$newProduction = $nodeProduction *($newPercentage/100);
		
		$num = 0;
		/***********************ERROR IS HERE****************************/
		$nodeConnectArray = json_decode($nodeConnectGridList, true);
		/**************************************************/
		//	echo "<br>";
		//	echo "<br>";
		//echo 'NODECONNECTARRAY';
		//var_dump($nodeConnectArray);
		//echo "<br>";
		//echo 'FILE: ' . $nodeConnectArray ;
		
		if(round($newPercentage,3) != round($nodePercentage,3) or $_GET['name'] == $nodeAcronym ){
			while ($num < count($nodeConnectArray['gridList'])) {
				//LOOP INSIDE gridlist connection and do stuff
				$value = intval($nodeConnectArray['gridList'][$num]['value']);
				$name = $nodeConnectArray['gridList'][$num]['name'];
				//echo "Number: $num";
				//echo "<br>";
				//echo 'NAME CONTENTS: '. var_dump($name);
				//var_dump($name);
				//echo "<br>";
				//echo "VALUE BELOW";
				//var_dump($value);
				//echo "<br>";
				//echo '  HERE NAME: '.$name;
				if ($value > 0) {
					$value = $value * ($newPercentage/100);
					$nodeConnectArray['gridList'][$num]['value'] = $value;
					array_push($arrayQueue, $name);
					$nodeTotalOutflow = $nodeTotalOutflow + $value;

					//SHOULD BE NODE CONNECT
					$sql2 = "SELECT `node_connect` FROM `node_info` WHERE `node_acronym` = '$name'";
					$resultTmp2 = $mysqli->query($sql2) or
						die("Something went wrong with $sql2".$mysqli->error);
					$result2 = $resultTmp2->fetch_assoc();
					$nodeConnectGridList2 = $result2['node_connect'];

					/*******************************************/
					$nodeConnectArray2 = json_decode($nodeConnectGridList2, true);
					/*******************************************/
					//echo 'GRIDLIST :';
					//var_dump($nodeConnectGridList2);
					//echo 'nodeConnectArray2 :';
					//var_dump($nodeConnectArray2);
					//echo "<br>";
					//echo 'HERE: '. $nodeConnectArray2['gridList'];
					$gridListContent = $nodeConnectArray2['gridList'];
					//$gridListContent = json_decode($gridListContent, true);
					for ($i = 0; $i < count($gridListContent); $i++) {
						//echo "HELLO!!";

						if ($nodeAcronym == $gridListContent[$i]['name']) {
							$gridListContent[$i]['value'] = round($value,0);
							$updatedValue = $gridListContent[$i]['value'];

							$updatedValue = $updatedValue * (-1);
							/*
							echo "<br>";
							echo "<br>";
							echo '$node_acronym: '.$nodeAcronym;
							echo "<br>";
							echo '$VALUE: '.round($value,0);
							echo "<br>";
							echo '$newPercentage: '.round($newPercentage,0);
							echo "<br>";
							echo "<br>";
							echo "<br>";
							echo 'BEFORE UPDATEJSON NAME CONTENTS: '. $name;
							echo "<br>";*/
							$insideJson = $gridListContent;
							$insideJson = json_encode($insideJson);
							//$insideJson = json_decode($insideJson, true); DO NOT DECODE INSIDEJSON

							$updateDBJSON = json_encode($nodeConnectArray2);
							//var_dump();
							//echo $updateDBJSON;
						/*	echo "UpdatedDBJSON: ".$updateDBJSON;
							echo "<br>";
							echo "InsideJson: ".$insideJson;
							echo "<br>";
							echo "InsideJson[0]: ".$insideJson[0];
							echo "<br>";
							echo 'AFTER JSON NAME CONTENTS: *'. $name.'*';
							echo "<br>";
							echo "<br>";*/
							$path = '$.gridList['.$i.'].value';
							//JSON_REPLACE(`node_connect`, "$.gridList", $insideJson)
							$sql3 = "UPDATE `node_info` 
									SET `node_connect`=JSON_REPLACE(`node_connect`, '$path', '$updatedValue') 
									WHERE `node_acronym` = '$name'";

							//$updateSQL = JSON_REPLACE(`node_connect`, "$") //$updateDBJSON['gridList'];
							//$sql = "UPDATE `node_info` SET `node_connect`='$updateDBJSON' WHERE `node_acronym` = '$name'";
							$result3 = $mysqli->query($sql3) or
								die("Something went wrong with $sql3".$mysqli->error);

							//$sql3 = "UPDATE `node_info` 
							//		SET `node_connect`=JSON_REPLACE(`node_connect`, '$.gridList', '$insideJson') 
							//		WHERE `node_acronym` = '$name'";


						}
					}


					$path = '$.gridList['.$num.'].value';
					//JSON_REPLACE(`node_connect`, "$.gridList", $insideJson)
					$sql4 = "UPDATE `node_info` 
							SET `node_connect`=JSON_REPLACE(`node_connect`, '$path', '$value')
							WHERE `node_acronym` = '$nodeAcronym'";
					$result4 = $mysqli->query($sql4) or
										die("Something went wrong with $sql4".$mysqli->error);

					$nodeTotalOutflow = $nodeTotalOutflow + $value;
					//$sql = "UPDATE `node_info` SET `node_active`='0' WHERE `node_acronym` = '$value_acronym'";
				}
				$num++;
			}//END OF WHILE LOOP

		}
	
		$sql5 = "UPDATE `node_info` 
				SET `pow_produce`='$newProduction', `node_statusPerc` = '$newPercentage' 
				WHERE `node_acronym` = '$nodeAcronym'";
		$result5 = $mysqli->query($sql5) or
							die("Something went wrong with $sql5".$mysqli->error);
		
		InOutFlowCal($mysqli);
		$index++;
		mapSimulation($value_acronym,$arrayQueue,$index, $mysqli);
	}

	
	//$data the sql result ($data will be each node) iterates through each node
	/*while ($data=$result->fetch_array(MYSQLI_NUM)) {
		if ($data['node_acronym'] == $value_acronym) {
			for ($j = 0; $j < count($data['node_connect']); $j++) {
				//select data, read data, modify it into new values, then update it back into DB
			}
		}
	}
	*/
	/****************************************************/
	//	RECURSION HAPPENS HERE
	/****************************************************/
}
//CREATE ERROR CHECK LATER
function greyMarkerStatus($value_acronym, $mysqli) {
	//SETS ACTIVE STATUS TO OFF '0'
	$sql="UPDATE `node_info` SET `node_active`='0' WHERE `node_acronym` = '$value_acronym'";
	$result = $mysqli->query($sql) or
		die("Something went wrong with $sql".$mysqli->error);
	
	//SETS NODE TO 0% PERCENT
	$sql="UPDATE `node_info` SET `node_statusPerc`= '0' WHERE `node_acronym` = '$value_acronym'";
	$result = $mysqli->query($sql) or
		die("Something went wrong with $sql".$mysqli->error);
	
	//SETS NODE TO 0% PERCENT
	$sql="UPDATE `node_info` SET `node_totalOutflow`= '0' WHERE `node_acronym` = '$value_acronym'";
	$result = $mysqli->query($sql) or
		die("Something went wrong with $sql".$mysqli->error);	
	//HERE
	$sql="UPDATE `node_info` SET `pow_produce`= '0' WHERE `node_acronym` = '$value_acronym'";
	$result = $mysqli->query($sql) or
		die("Something went wrong with $sql".$mysqli->error);	
}


function revertMarkerStatus($value_acronym, $mysqli) {
	//SELECTS EVERYTHING FROM STATIC TABLE
	$sql= "SELECT `node_statusPerc`,`node_totalInflow`,`node_totalOutflow`,`pow_produce`,`node_connect` FROM `node_simulation_static` WHERE `node_acronym` = '$value_acronym'";
	$resultTmp = $mysqli->query($sql) or
		die("Something went wrong with $sql".$mysqli->error);
	$result = $resultTmp->fetch_assoc();
	/*************************************/
	$production = $result['pow_produce'];
	$percent = $result['node_statusPerc'];
	$totalInFlow = $result['node_totalInflow'];
	$totalOutFlow = $result['node_totalOutflow'];
	$connectGrid = $result['node_connect'];
	/*************************************/
	//UPDATES EVERYTHING ELSE
	$sql="UPDATE `node_info` SET `node_active`='1',
		`pow_produce`= '$production',
		`node_totalInflow`= '$totalInFlow',
		`node_totalOutflow`= '$totalOutFlow',
		`node_statusPerc`= '$percent'
		WHERE `node_acronym` = '$value_acronym'";
	$result = $mysqli->query($sql) or
		die("Something went wrong with $sql".$mysqli->error);
	/***********************************************************************/
	
				
	/*******************************************/
	$revertConnectGridArray = json_decode($connectGrid, true);
	/*******************************************/
	
	$gridListContent = $revertConnectGridArray['gridList'];
				//$gridListContent = json_decode($gridListContent, true);
	for ($i = 0; $i < count($gridListContent); $i++) {
		$currentValue = $gridListContent[$i]['value'];
		$path = '$.gridList['.$i.'].value';
		$sql3 = "UPDATE `node_info` 
				SET `node_connect`=JSON_REPLACE(`node_connect`, '$path', '$currentValue') 
				WHERE `node_acronym` = '$value_acronym'";
		$result3 = $mysqli->query($sql3) or
			die("Something went wrong with $sql3".$mysqli->error);
	}
	
}
//Turn it on
//function 
//RECURSION HAPPENS HERE
//THIS GOES THROUGH THE DB AND CHECKS THE NODE CONNECTIONS AND HOW THEY ARE AFFECTED



?>