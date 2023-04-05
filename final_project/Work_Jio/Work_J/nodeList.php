
<?php
	function getNodeData($mysqli) {
		$arrayMarker = array();
		$sql="SELECT `node_id`, `node_acronym`, `node_name`, `node_region`, `pow_produce`, `pow_demand`, `node_popServe`, `node_lat`, `node_lon`, `node_connect`, `node_active`, `node_totalInflow`, `node_totalOutflow`, `node_statusPerc`  from `node_info` order by `node_region`;";
		$result = $mysqli->query($sql) or
			die("Something went wrong with $sql".$mysqli->error);
		$result_Check = mysqli_num_rows($result);
		//$output = array();
		//var_dump($result);
		if ($result_Check > 0) {
            echo "HFEORG";
			$i = 0;
		   	while($rowData = mysqli_fetch_assoc($result)) {			
				$arrayMarker[$i] = $rowData;
				$i++;
			}
			return $arrayMarker;
		}
		return "N/A";	
	}

//use case: 
    //$mysqli=db_connect("senior_design_db");
    //$sum =  getNodeData($mysqli);//getNodeData(db_connect("senior_design_db"));
    //returns a list of all nodes in the node_info table
?>

<?php
    //function: get node connections, and create polylines to add to map
    // input: map, nodeList
    //for each node in nodeList
        //get node latitude
        //get node longitude
        //access node_connect, which has a grid list of the names of the nodes it connects to
        //for each node in the grid list:
            //search nodeList and get node with matching name
                // access the node longitutde
                //  access the node latitdue  
                //add a polyline to map with the nodes longitude and latitude
	$sum =  getNodeData($mysqli); 
	
?>
<script type="text/javascript">
	console.log("INSIDE NODE LIST SCRIPT");
	var nodeList = <?php echo json_encode($sum); ?>;
	
	function getNodeConnections(map, nodeList) {
		if (!nodeList) {
    		console.error("nodeList is undefined or null");
    		return;
  		}
  		for (let i = 0; i < nodeList.length; i++) {
    		let node = nodeList[i];
    		let nodeLat = node.node_lat;
    		let nodeLng = node.node_lon;
    		let nodeConnect = nodeList[i].node_connect;
			nodeConnect = JSON.parse(nodeConnect);
			console.log("nodeConnect:", nodeConnect);
			console.log("out of loop");
    		for (let j = 0; j < nodeConnect.gridList.length; j++) {
				console.log("in loop");
				console.log("Connecting to node: " + nodeConnect.gridList[j].name);
      			let connectedNode = nodeList.find((item) => item.node_acronym === nodeConnect.gridList[j].name);
				console.log("found node");

      			if (connectedNode) {
        			let connectedNodeLat = connectedNode.node_lat;
        			let connectedNodeLng = connectedNode.node_lon;
					

        			// add a polyline to map with the nodes longitude and latitude
					let latlngs = [[nodeLat, nodeLng], [connectedNodeLat, connectedNodeLng]];
      				let polyline = L.polyline(latlngs, { color: 'red' }).addTo(map);
					console.log(latlngs);

      			}		
    		}
  		}
	}
	getNodeConnections(map, nodeList);


</script>