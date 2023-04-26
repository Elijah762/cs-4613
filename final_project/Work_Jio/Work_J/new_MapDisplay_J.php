

<?php
    /******************************DB ACCESS*********************************************/
	include_once("db_access.php");
	include_once("inflowOutflowCalculation.php");
	$mysqli=db_connect("senior_design_db");
?>

<?php
	function getNodeData($mysqli) {
		$arrayMarker = array();
		$sql="SELECT `node_id`, `node_acronym`, `node_name`, `node_region`, `pow_produce`, `pow_demand`, `node_popServe`, `node_lat`, `node_lon`, `node_connect`, `node_active`, `node_totalInflow`, `node_totalOutflow`, `node_statusPerc`  from `node_info` order by `node_region`;";
		$result = $mysqli->query($sql) or
			die("Something went wrong with $sql".$mysqli->error);
		$result_Check = mysqli_num_rows($result);
		if ($result_Check > 0) {
			$i = 0;
		   	while($rowData = mysqli_fetch_assoc($result)) {			
				$arrayMarker[$i] = $rowData;
				$i++;
			}
			return $arrayMarker;
		}
		return "N/A";	
	}

$sum =  getNodeData($mysqli);
?>

<script type="text/javascript">
	//------------------------------- CREATES AND CALLS MAP API --------------------------------------------//
	const map = L.map('map', {
			center: [42.1867, -98.1667],
			zoom: 3.5
  	});
	let baseMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' });
    let produce, totInflow, demand, outflow;//globals
	baseMap.addTo(map);

	const popup = L.popup({
		closeButton: false,
		autoClose: false
	})
	.setLatLng([55.1867, -98.1667])
	.setContent('<p>Simulation Map</p>')
	.openOn(map);

	let arraySum = <?php echo json_encode($sum); ?>; //echos out 'Array's contents maybe for loop to get all of data? maybe?
	let summary = [];
    let markers = L.markerClusterGroup();
    let mapPins = mapMarkers();
	function mapMarkers() {
		let MapIcon = L.Icon.extend({
			options: {
				iconSize: [30, 30],
				iconAnchor: [12, 41],
				popupAnchor: [1, -30]
			}
		});
        let map_icons = [
            new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/2511/2511648.png'}),//power plant icon
            new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/6631/6631648.png'}),//hurricane icon
            new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/785/785116.png'}),//wildfire icon
            new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/3032/3032739.png'}),//tornado icon
            new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/6566/6566490.png'}),//earthquake icon
            new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/3813/3813615.png'}),//cyberattack icon
        ]
		return map_icons;
	}


	function createMarkerDisplay() {
        for (let i = 0; i < arraySum.length; i++) {
            let percentEquation = getPercentTotal(i)
            let energyTotalEquation = getEnergyTotal(i);

            setSummary(i, energyTotalEquation);
            arraySum[i].node_statusPerc = percentEquation;
            setPinStatus(i);
        }
	}

    function getPercentTotal(i) {
        setData(i)
        return (produce - totInflow) / (demand - outflow) * 100;
    }

    function setData(i){
        produce = parseInt(arraySum[i].pow_produce);
        totInflow = parseInt(arraySum[i].node_totalInflow);
        demand = parseInt(arraySum[i].pow_demand);
        outflow = parseInt(arraySum[i].node_totalOutflow);
    }

    function getEnergyTotal(i) {
        setData(i)
        return produce  - totInflow  - demand +  outflow;
    }

    function setPinStatus(i) {
        let pinNum;
        if (Math.sign(arraySum[i].node_statusPerc) == 0 || Math.sign(arraySum[i].node_statusPerc) == -1 )       {pinNum = 4;}
        else {
            if (arraySum[i].node_active == 0)                                                                   {pinNum = 5;}
            else if (Number(arraySum[i].node_statusPerc) < Number(25))                                          {pinNum = 4;}
            else if (Number(25) <= arraySum[i].node_statusPerc && arraySum[i].node_statusPerc <= Number(49))    {pinNum = 3;}
            else if (Number(50) <= arraySum[i].node_statusPerc && arraySum[i].node_statusPerc <= Number(74))    {pinNum = 2;}
            else if (Number(75) <= arraySum[i].node_statusPerc && arraySum[i].node_statusPerc <= Number(99))    {pinNum = 1;}
            else if (Number(100) <= arraySum[i].node_statusPerc )                                               {pinNum = 0;}
        }
        let marker = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: mapPins[pinNum]}).bindPopup( summary[i] ).addTo(map);
        marker.on('mouseover', function(e) {this.openPopup();});
        marker.on('mouseout', function(e) {this.closePopup();});
        markers.addLayer(marker);
    }

    function setSummary(i, energyTotalEquation) {
        setData(i);
        summary[i] = arraySum[i].node_name + '<br> Energy Produced: ' + produce + '<br> Energy Demand:  ' + demand + '<br> Outflow: ' + outflow  + '<br> Inflow: ' + totInflow +'<br> Energy Total: ' + energyTotalEquation + '<br> Population Served:  ' + arraySum[i].node_popServe;
    }

	/**************************FUNCTION EXECUTION***************************************/
	let map_markers = mapMarkers();
	createMarkerDisplay();
	/*************************************************************************************/
	/***
	*	MADE BY: JOSE -JAYDEN HELPED IN EQUATION
	*	FUNC: updateMarkerDisplay()
	*	PARAMETERS: i, arraySum, markers, summary
	*	HANDLES INITIAL MARKER COLORS DEPENDING ON DATA RETRIEVED FROM DB
	***/
		function updateMarkerDisplay(i, arraySum, markers, summary) {
			console.log("INSIDE UPDATEMARKERDISPLAY");
			console.log("INSIDE UPDATEMARKERDISPLAY");
			console.log("INSIDE UPDATEMARKERDISPLAY");
			//ERROR IS HERE
			for (let i = 0; i < arraySum.length; i++) {
					console.log("values percent: " + arraySum[i].node_statusPerc);
				arraySum[i].node_statusPerc
				if (arraySum[i].node_active != 0) {
					//LOOK AT
					console.log("FIRST IF UPDATEMARKERDISPLAY");
					let energyTotalEquation = (parseInt(arraySum[i].pow_produce) + (parseInt(arraySum[i].node_totalInflow)* -1)) -(parseInt(arraySum[i].pow_demand)-(parseInt(arraySum[i].node_totalOutflow)));
				
					if (isNaN(parseInt(arraySum[i].node_statusPerc)))
					{
						console.log("I: "+ i + " percentEquation: " + (arraySum[i].node_statusPerc));
					}
					if (isNaN(parseInt(energyTotalEquation)))
					{
						console.log("I: "+ i + " energyTotalEquation: " + (parseInt(energyTotalEquation)));
					}
					/*************************************/
						console.log("OUTSIDE IF UPDATEMARKERDISPLAY");
						summary[i] = arraySum[i].node_name + '<br> Energy Produced: ' + arraySum[i].pow_produce + '<br> Energy Demand:  ' + arraySum[i].pow_demand + '<br> Outflow: ' + arraySum[i].node_totalOutflow  + '<br> Inflow: ' + arraySum[i].node_totalInflow +'<br> Energy Total: ' + energyTotalEquation + '<br> Population Served:  ' + arraySum[i].node_popServe;
					/*************************************/
					if (Math.sign(arraySum[i].node_statusPerc) == 0 ) {		
						markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[4]}).bindPopup( summary[i] ).addTo(map);
						console.log("SECOND IF UPDATEMARKERDISPLAY");
						console.log("SECOND IF UPDATEMARKERDISPLAY");
						
					
					}
					if (Math.sign(arraySum[i].node_statusPerc) == -1 ) {		
						markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[4]}).bindPopup( summary[i] ).addTo(map);
						console.log("THIRD IF UPDATEMARKERDISPLAY");
						console.log("THIRD IF UPDATEMARKERDISPLAY");
					
			
					}
					else if (Math.sign(arraySum[i].node_statusPerc) == 1 ) {
						console.log("ELSE IF UPDATEMARKERDISPLAY");
						//LAST PLACE IT ENTERED
						
						
						if (Number(arraySum[i].node_statusPerc) < Number(25)) {
							markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[4]}).bindPopup( summary[i] ).addTo(map);
					console.log("FIRST: IF ELSE IF UPDATEMARKERDISPLAY");
							
						
						}
						else if (Number(25) <= arraySum[i].node_statusPerc && arraySum[i].node_statusPerc <= Number(49)) {
							markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[3]}).bindPopup( summary[i] ).addTo(map);
						console.log("SECOND: IF ELSE IF UPDATEMARKERDISPLAY");
						}
						else if (Number(50) <= arraySum[i].node_statusPerc && arraySum[i].node_statusPerc <= Number(74)) {
							markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[2]}).bindPopup( summary[i] ).addTo(map);
							console.log("50+");
						}
						else if (Number(75) <= arraySum[i].node_statusPerc && arraySum[i].node_statusPerc <= Number(99)) {
							markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[1]}).bindPopup( summary[i] ).addTo(map);
							console.log("75+");

						}
						else if (Number(100) <= arraySum[i].node_statusPerc ) {
							markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[0]}).bindPopup( summary[i] ).addTo(map);
							console.log("100+");
							
						}
						else  {// || typeof percentVal === "undefined") { 
							console.log("LAST ELSE POSITIVES");
							//markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: arrayColors[6]}).bindPopup( "FOR ERROR CHECKING <br>"+ summary[i] ).addTo(map);
							
						}
					}
					else  {// || typeof percentVal === "undefined") { 
						console.log("arraySum[i].node_acroynm: " + arraySum[i].node_acronym + " I: "+ i + " LAST percentEquation: " + arraySum[i].node_statusPerc);
						markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[6]}).bindPopup( "FOR ERROR CHECKING <br>"+ summary[i] ).addTo(map);
					
					} 
				}
				else {
					console.log("FIRST ELSE UPDATEMARKERDISPLAY");
					let energyTotalEquation = (parseInt(arraySum[i].pow_produce) + (parseInt(arraySum[i].node_totalInflow)* -1)) -(parseInt(arraySum[i].pow_demand)-(parseInt(arraySum[i].node_totalOutflow)));
					
					/*************************************/
						summary[i] = arraySum[i].node_name + '<br> Energy Produced: ' + arraySum[i].pow_produce + '<br> Energy Demand:  ' + arraySum[i].pow_demand + '<br> Received: ' + arraySum[i].node_totalOutflow  + '<br> Given: ' + arraySum[i].node_totalInflow +'<br> Energy Total: ' + energyTotalEquation + '<br> Population Served:  ' + arraySum[i].node_popServe;
					/*************************************/
				
					markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[5]}).bindPopup( "FOR ERROR CHECKING <br>"+ summary[i] ).addTo(map);
					
				}
			}
			console.log("END OF UPDATEMARKERDISPLAY");
		}
			
		
	function myJavascriptFunction(value) { 
  		let javascriptVariable = value;

		let newUrl = "mapDev.php?name=" + javascriptVariable + "&turnOff=true";
		console.log("myJavaScriptFunction Called");
		window.location.replace(newUrl);
	}
	
	function revertJavascriptFunction(value) { 
  		let javascriptVariable = value;
		let newUrl = "mapDev.php?name=" + javascriptVariable + "&turnOff=false";
		console.log("myJavaScriptFunction Called");
		window.location.replace(newUrl);
	}

	function markerOnClicked(i,markers, arraySum, summary, clicked) {
		console.log("BEFORE IF MARKERONCLICKED");
		if (arraySum[i].node_active == 1) {
			let value = arraySum[i].node_acronym;
			myJavascriptFunction(value);

			<?php //MEANT TO PASS VARIABLE NAME FOR DB LATER ON
			if (!empty($_GET['name'] && $_GET['turnOff'] == "true")) {
				
				$value_acronym = $_GET['name'];
				$arrayQueue = [];
				$index = 0;
				$arrayQueue[$index] = $value_acronym;
				greyMarkerStatus($value_acronym, $mysqli);
				//HANDLES SIMULATION
				mapSimulation($value_acronym,$arrayQueue,$index, $mysqli);
				//CALLS AND ASSIGNS ARRAY WITH NEW DATA
				$sum =  getNodeData($mysqli); 
			}
			?>
			
			console.log("IF AFTER MARKERCLICKED");
			/***********************SIMULATION*********************************/	
			const queryString = window.location.search;
			// splits the parameters up
			const urlParams = new URLSearchParams(queryString);
			
			let arraySum2 = <?php echo json_encode($sum); ?>;
			
			if (urlParams.has('name')) {
				arraySum = arraySum2;
			}
			
			console.log("BEFORE UPDATE MARKER IN == 1");
			updateMarkerDisplay(i, arraySum, markers, summary);
			//HANDLES MOUSE HOVERING
			MarkerMouseHover(markers, arraySum);
			/****************************/
			return;
		}
		else if (arraySum[i].node_active == 0) {
		//******************DATA FROM JAVASCRIPT TO PHP SIMULATION************************/
			console.log("ELSE IF");
			let value = arraySum[i].node_acronym;
			//myJavascriptFunction(value);

			<?php //MEANT TO PASS VARIABLE NAME FOR DB LATER ON
			if (!empty($_GET['name'] && $_GET['turnOff'] == "false")) {
				$value_acronym = $_GET['name'];
				$arrayQueue = [];
				$index = 0;
				$arrayQueue[$index] = $value_acronym;
				revertMarkerStatus($value_acronym, $mysqli);
				//HANDLES SIMULATION
				//mapSimulation($value_acronym,$arrayQueue,$index, $mysqli);
				//CALLS AND ASSIGNS ARRAY WITH NEW DATA
				$sum =  getNodeData($mysqli); 
			}	
			?>
			console.log("ELSE IF AFTER MARKERCLICKED");
			//HANDLES SIMULATION
			//< ?php echo json_encode(mapSimulation($value_acronym,$arrayQueue,$index, $mysqli)); ?>; 
			//CALLS AND ASSIGNS ARRAY WITH NEW DATA
			<?php $sum =  getNodeData($mysqli); ?>
			//THIS PREVENTS GOING INTO IF 
			//myJavascriptFunction('');
			
			//var arraySum = <?php //echo json_encode($sum); ?>;
			
			const queryString = window.location.search;
			// splits the parameters up
			const urlParams = new URLSearchParams(queryString);
			
			let arraySum2 = <?php echo json_encode($sum); ?>;
			
			if (urlParams.has('name')) {
				arraySum = arraySum2;
			}
			
			revertJavascriptFunction(value);
			//UPDATES MARKERS
			console.log("BEFORE UPDATE MARKER IN == 0");
			updateMarkerDisplay(i, arraySum, markers, summary);
			//HANDLES MOUSE HOVERING
			MarkerMouseHover(markers, arraySum);
			return;
		}
	} //END OF FUNCTION: markerOnClicked();
	
	function markersDisplayedClicked() {
		for (let i = 0; i < arraySum.length; i++) { 
			console.log("FOR LOOP MARKERONCLICKED");
			markers[i] = markers[i].on('click', function(){markerOnClicked(i,markers, arraySum, summary, arraySum[i].node_active);}).addTo(map);
		}	
	}
	/**************************FUNCTION EXECUTION***************************************/
	const queryString = window.location.search;
	// splits the parameters up
	const urlParams = new URLSearchParams(queryString);
	
	if (urlParams.has('name')) {
		const name = urlParams.get('name');
		console.log("This is the name: " + name);
	}

	
	/********************************
	* HANDLES DEFAULT MOUSE HOVER OVER FUNCTIONALITY
	**********************************/
	function MarkerMouseHover(markers, arraySum) {
		for (let i = 0; i < arraySum.length; i++) {
			markers[i].on('mouseover', function (e) {this.openPopup();}); //this.openPopup();  ?
			markers[i].on('mouseout', function (e) {this.closePopup();}); // this.closePopup(); ?
		}
	}
	
	console.log("MarkerMouseHover is ran");
	MarkerMouseHover(markers, arraySum);

	L.shapefile('/cs-4613/final_project/Work_Jio/assets/shapefiles/NERC_Regions_EIA.zip', {
        style: function(feature) {
            return {
                color: 'red',
                weight: 2
            };
        }
    }).addTo(map);

	let nodeList = <?php echo json_encode($sum); ?>;
	
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
      				let polyline = L.polyline(latlngs, { color: 'blue' }).addTo(map);
					console.log(latlngs);

      			}		
    		}
  		}
	}
	getNodeConnections(map, nodeList);
</script>

	
	
	