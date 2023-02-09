<?php
include_once("db_access.php");

function mapSimulationTest($value_acronym,$arrayQueue,$index, $mysqli) {
	// sql to get the json info for node connections
	if ($index == count($arrayQueue)) //checks if arraylength is we reached end of array 
	{	
		return;
	}
	/****************************************************/

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
	
	var_dump($nodeDemand);
	echo "<br>";
	var_dump($nodeTotalInflow);
	echo "<br>";
	var_dump($nodeTotalOutflow);
	echo "<br>";
	var_dump($nodePercentage);
	echo "<br>";
	/*******************/
	if (($nodeProduction + $nodeTotalInflow) >= ($nodeDemand + $nodeTotalOutflow)) {
		$index++;
		mapSimulationTest($value_acronym,$arrayQueue,$index, $mysqli);
	}
	else if (($nodeProduction + $nodeTotalInflow) < ($nodeDemand + $nodeTotalOutflow)) {
		$sql = "SELECT `node_totalInflow` FROM `node_simulation_static` WHERE `node_acronym` = '$arrayQueue[$index]'";
		$resultTmp = $mysqli->query($sql) or
				die("Something went wrong with $sql".$mysqli->error);
		$resultStaticOut = $resultTmp->fetch_assoc();
		$newPercentage = ((($nodeProduction + $nodeTotalInflow) / ($nodeDemand + intval($resultStaticOut)) * 100));
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
		while ($num < count($nodeConnectArray['gridList'])) {
			//LOOP INSIDE gridlist connection and do stuff
			$value = $nodeConnectArray['gridList'][$num]['value'];
			$name = $nodeConnectArray['gridList'][$num]['name'];
			echo "Number: $num";
			echo "<br>";
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
				echo 'GRIDLIST :';
				//var_dump($nodeConnectGridList2);
				echo 'nodeConnectArray2 :';
				var_dump($nodeConnectArray2);
				echo "<br>";
				//echo 'HERE: '. $nodeConnectArray2['gridList'];
				$gridListContent = $nodeConnectArray2['gridList'];
				//$gridListContent = json_decode($gridListContent, true);
				for ($i = 0; $i < count($gridListContent); $i++) {
					echo "HELLO!!";
					
					if ($nodeAcronym == $gridListContent[$i]['name']) {
						$gridListContent[$i]['value'] = round($value,0);
						$updatedValue = $gridListContent[$i]['value'];
						
						$updatedValue = $updatedValue * (-1);
						
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
						echo "<br>";
						$insideJson = $gridListContent;
						$insideJson = json_encode($insideJson);
						//$insideJson = json_decode($insideJson, true); DO NOT DECODE INSIDEJSON
						
						$updateDBJSON = json_encode($nodeConnectArray2);
						//var_dump();
						//echo $updateDBJSON;
						echo "UpdatedDBJSON: ".$updateDBJSON;
						echo "<br>";
						echo "InsideJson: ".$insideJson;
						echo "<br>";
						echo "InsideJson[0]: ".$insideJson[0];
						echo "<br>";
						echo 'AFTER JSON NAME CONTENTS: *'. $name.'*';
						echo "<br>";
						echo "<br>";
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
				$nodeTotalOutflow = $nodeTotalOutflow + $value;
				//$sql = "UPDATE `node_info` SET `node_active`='0' WHERE `node_acronym` = '$value_acronym'";
			}
			$num++;
		}//END OF WHILE LOOP
		
		$sql = "UPDATE `node_info` SET `node_connect` = '$nodeConnectGridList',`pow_produce` = '$newProduction',
					`node_statusPerc` = '$newPercentage', from `node_info` WHERE `node_acronym` = '$arrayQueue[$index]'";
	}	 
}
//$value_acronym = $_GET['name'];
$arrayQueue = [];
$index = 0;
$arrayQueue[$index] = 'AECI';
mapSimulationTest('AECI',$arrayQueue,0,db_connect("senior_design_db"));

?>