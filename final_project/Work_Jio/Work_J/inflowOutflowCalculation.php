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
    //{[{}],[{}]}
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

?>
    <script type="text/javascript">
        //take array sum
        //for each array sum read gridlist
        //for each gridlist value manage out and in flow
        //for each array sum update array sum total inflow and outflow
        function FlowCalc(marker) {
            let neighbor_list = JSON.parse(marker.node_connect);

            const totalFlow = neighbor_list.gridList
                .map(neighbor => calculateNeighbor(neighbor))
                .reduce((acc, cur) => {
                        return {
                            inflow: acc.inflow + cur.inflow,
                            outflow: acc.outflow + cur.outflow
                        };
                    },
                    {inflow: 0, outflow: 0}
                );

            marker.node_totalInflow = totalFlow.inflow;
            marker.node_totalOutflow = totalFlow.outflow;
        }

        function calculateNeighbor(neighbor) {
            const flow = Number(JSON.parse(neighbor).value.replace(',', ''));

            return flow > 0 ?
                { outflow: flow, inflow: 0 } :
                { outflow: 0, inflow: flow };
        }

        function mapSimulation(clickedNode, nodeList) {

        }
    </script>
<?php
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
	$nodeAcronym = $result['node_acronym'];
	$nodeConnectGridList = $result['node_connect'];
	$nodeProduction = intval($result['pow_produce']);
	
	$nodeDemand = intval($result['pow_demand']);
	$nodeTotalInflow = intval($result['node_totalInflow']);
	$nodeTotalOutflow = intval($result['node_totalOutflow']);
	$nodePercentage = intval($result['node_statusPerc']);
	$nodeActive = $result['node_active'];

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
		if(round($newPercentage,3) != round($nodePercentage,3) or $_GET['name'] == $nodeAcronym ){
			while ($num < count($nodeConnectArray['gridList'])) {
				$value = intval($nodeConnectArray['gridList'][$num]['value']);
				$name = $nodeConnectArray['gridList'][$num]['name'];
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

					$nodeConnectArray2 = json_decode($nodeConnectGridList2, true);

					$gridListContent = $nodeConnectArray2['gridList'];
					for ($i = 0; $i < count($gridListContent); $i++) {
						if ($nodeAcronym == $gridListContent[$i]['name']) {
							$gridListContent[$i]['value'] = round($value,0);
							$updatedValue = $gridListContent[$i]['value'];

							$updatedValue = $updatedValue * (-1);
							$insideJson = $gridListContent;
							$insideJson = json_encode($insideJson);

							$updateDBJSON = json_encode($nodeConnectArray2);

							$path = '$.gridList['.$i.'].value';
							$sql3 = "UPDATE `node_info` 
									SET `node_connect`=JSON_REPLACE(`node_connect`, '$path', '$updatedValue') 
									WHERE `node_acronym` = '$name'";

							$result3 = $mysqli->query($sql3) or
								die("Something went wrong with $sql3".$mysqli->error);
						}
					}


					$path = '$.gridList['.$num.'].value';
					$sql4 = "UPDATE `node_info` 
							SET `node_connect`=JSON_REPLACE(`node_connect`, '$path', '$value')
							WHERE `node_acronym` = '$nodeAcronym'";
					$result4 = $mysqli->query($sql4) or
										die("Something went wrong with $sql4".$mysqli->error);

					$nodeTotalOutflow = $nodeTotalOutflow + $value;
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

?>
    <script type="text/javascript">

    </script>
<?php

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