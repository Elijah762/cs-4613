

<?php
	/***
	* 	FROM: JDP
	***/
    /******************************DB ACCESS*********************************************/
	include_once("db_access.php");
	include_once("inflowOutflowCalculation.php");
	$mysqli=db_connect("senior_design_db");
	//db_connect("senior_design_db");
	/***********************************************************************************/
?>

<?php
	/***
	* 	FROM: JDP
	***/
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

$sum =  getNodeData($mysqli);//getNodeData(db_connect("senior_design_db"));
?>

<?php
//RESETS MAP TO DEFAULT; MAY OR  MAY NOT WORK DEPENDING ON PERMISSION STATUS, MAY NEED TO UPDATE INSTEAD
	/*function getResetNodeData($mysqli) {
		$sql="TRUNCATE `senior_design_db.node_info` INSERT INTO `senior_design_db.node_info` SELECT * FROM senior_design_db.node_simulation_static;";
		$result = $mysqli->query($sql) or
			die("Something went wrong with $sql".$mysqli->error);
		$result_Check = mysqli_num_rows($result);
	}
	*/	
?>	

<script type="text/javascript">
	/***
	* 	FROM: JDP
	***/
	//------------------------------- CREATES AND CALLS MAP API --------------------------------------------//
	const map = L.map('map', {
			center: [42.1867, -98.1667],
			zoom: 3.5
  	});
	var baseMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' });

	baseMap.addTo(map);

	const popup = L.popup({
		closeButton: false,
		autoClose: false
	})
	.setLatLng([55.1867, -98.1667])
	.setContent('<p>Simulation Map</p>')
	.openOn(map);

	//console.log("HERE:"+ baseMap);
	//*********************************ASSIGNS VALUES FROM PHP TO JAVASCRIPT**********************************************//
	var arraySum = <?php echo json_encode($sum); ?>; //echos out 'Array's contents maybe for loop to get all of data? maybe?
	//var arraySumStatic = </?php echo json_encode($sum); ?>; //echos out 'Array's contents maybe for loop to get all of data? maybe?
	//***********************HANDLES MAP MARKER DATA DISPLAYED ON MAP **********************************//
	var summary = []; 
    var markers = [];
	//var markersTemp = [];
	//*********************************************************//
	/****
	* 	FROM: JDP
	* 	FUNCTION: getArrayColors(); 
	*	HANDLES COLOR OUTPUTS FOR ARRAYS, LATER USED IN MAP DISPLAY
	* 	arrayColors[] , 0-6, BLUE -> GREEN -> YELLOW -> ORANGE -> RED ; SILVER, BLACK
	***/
	// function getArrayColors () {
	// 	let arrayColorNames = ['blue','green','yellow','orange','red', 'grey', 'black']; // error checking can be with grey or black.
	// 	let arrayColors = [];
	// 	for (let i = 0; i < arrayColorNames.length; i++) {
	// 		arrayColors[i] = new L.Icon({
	// 			  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-'+ arrayColorNames[i] +'.png',
	// 			  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
	// 			  iconSize: [25, 41],
	// 			  iconAnchor: [12, 41],
	// 			  popupAnchor: [1, -34],
	// 			  shadowSize: [41, 41]
	// 		});
	// 	}
	// 	return arrayColors;
	// }

	function mapMarkers() {
		var MapIcon = L.Icon.extend({
			options: {
				iconSize: [30, 30],
				iconAnchor: [12, 41],
				popupAnchor: [1, -30]
			}
		});

		var power_plant_icon = new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/2511/2511648.png'}),
			hurricane_icon = new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/6631/6631648.png'}),
			wildfire_icon = new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/785/785116.png'}),
			tornado_icon = new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/3032/3032739.png'}),
			earthquake_icon = new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/6566/6566490.png'}),
			cyberattack_icon = new MapIcon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/3813/3813615.png'});


		let map_markers = [];
		let map_icons = [
			power_plant_icon,
			hurricane_icon,
			wildfire_icon,
			earthquake_icon,
			cyberattack_icon
		];

		for (let i = 0; i < map_icons.length; i++) {
			map_markers[i] = map_icons[i];
			
		}
		return map_markers;

	}

	// function displayPowerConsumption() {
	// 	let power_consumption = [
	// 		{
	// 			"red": Number(24),
	// 			"orange": Number(49),
	// 			"yellow": Number(74),
	// 			"green": Number(99),
	// 			"blue": Number(100)
	// 		}
	// 	];

	// 	for (var key in data.power_constumption) {
	// 		var obj = data.power_consumption[key];
	// 	}

	// 	var circle = L.circle([], {
	// 		color: 'red',
	// 		fillColor: '#f03',
	// 		fillOpacity: 0.5,
	// 		radius: 500
	// 	}).addTo(map);

	// }
	//********************COLOR TEST*************************//
	// function colorTest() {
	// 	let arrNum = [];
	// 	let j = 110;
	// 	for (let i = 0; i < 7; i++){
	// 		if (i == 6) {
	// 			arrNum[i] = L.marker([15, -80], {icon: arrayColors[i]}).bindPopup("FOR ERROR CHECKING").addTo(map); 
	// 		}
	// 		else if (i == 5) {
	// 			arrNum[i] = L.marker([15, -85], {icon: arrayColors[i]}).bindPopup("FOR MANUALLY AFFECTED MARKER").addTo(map);
	// 		}
	// 		else {
	// 				arrNum[i] = L.marker([15, -j], {icon: arrayColors[i]}).addTo(map);
	// 		}
	// 		j = j-5;
	// 	}
	// }
	/************************************************/
	/****
	* 	FROM: JDP
	*   FUNCTION: createMarkerDisplay();
	*	HANDLES MATH EQUATION NEEDED TO SHOW DATA OUTPUT, WILL REFLECT MARKER COLOR DEPENDING ON ITS ENERGY STATUS
	*	**MATH ERROR FOUND HERE** 
	***/
	//DEFAULT AND WILL CREATE NEW MAP WHEN MARKER IS CLICKED.
	//NO NEED TO CHANGE (I THINK) OTHER THAN CREATING SIMULATION
	
	
	
	
	//create if statement where if active_node is off 1, then color will be grey.
	
	
	
	function createMarkerDisplay() {
		for (let i = 0; i < arraySum.length; i++) {//arraySum.length; i++ ) {
			
			/*************************************/ //CAUSING ISSUES WITH DATA DISPLAYED
			let percentEquation = (parseInt(arraySum[i].pow_produce) + (parseInt(arraySum[i].node_totalInflow)* -1));
			percentEquation = (percentEquation/(parseInt(arraySum[i].pow_demand)-(parseInt(arraySum[i].node_totalOutflow)))) * 100; 
	
			let energyTotalEquation = (parseInt(arraySum[i].pow_produce) + (parseInt(arraySum[i].node_totalInflow)* -1)) -(parseInt(arraySum[i].pow_demand)+  (parseInt(arraySum[i].node_totalOutflow))); // pow_demand)+ was pow_demand)-
			//console.log("I: "+ i + " energyTotalEquation: " + (parseInt(energyTotalEquation)));
			if (isNaN(parseInt(percentEquation)))
			{
				console.log("I: "+ i + " percentEquation: " + (parseInt(percentEquation)));
				//percentEquation = 1;
			}
			if (isNaN(parseInt(energyTotalEquation)))
			{
				console.log("I: "+ i + " energyTotalEquation: " + (parseInt(energyTotalEquation)));
				//energyTotalEquation = 0;
			}
				summary[i] = arraySum[i].node_name + '<br> Energy Produced: ' + arraySum[i].pow_produce + '<br> Energy Demand:  ' + arraySum[i].pow_demand + '<br> Outflow: ' + arraySum[i].node_totalOutflow  + '<br> Inflow: ' + arraySum[i].node_totalInflow +'<br> Energy Total: ' + energyTotalEquation + '<br> Population Served:  ' + arraySum[i].node_popServe;
			/*************************************/
			if (Math.sign(percentEquation) == 0 ) {		
				markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[4]}).bindPopup( summary[i] ).addTo(map);
				//markersTemp[i] = markers[i];
				//console.log("ARRAYEP FOUR: " + Number(percentVal));
				
				arraySum[i].node_statusPerc = percentEquation;
				//console.log("I: "+ i + " node_statusPerc: " + arraySum[i].node_statusPerc);	
			}
			if (Math.sign(percentEquation) == -1 ) {		
				markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[0]}).bindPopup( summary[i] ).addTo(map);
				//markers[i] = L.polyline([arraySum[i].node_lat, arraySum[i].node_lon], {color: 'red'}).addTo(map);
				//markersTemp[i] = markers[i];
				//console.log("ARRAYEP FOUR: " + Number(percentVal));
				
				arraySum[i].node_statusPerc = percentEquation;
				//console.log("I: "+ i + " node_statusPerc: " + arraySum[i].node_statusPerc);	
			}
			else if (Math.sign(percentEquation) == 1 ) {
				if (arraySum[i].node_active == 0){
					markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[5]}).bindPopup( summary[i] ).addTo(map);
					//markersTemp[i] = markers[i];

					arraySum[i].node_statusPerc = percentEquation;
					console.log("I: "+ i + " node_statusPerc: " + arraySum[i].node_statusPerc);	
					//console.log("ARRAYEP ELSE FOUR: " + Number(percentVal));
				}
				else if (Number(percentEquation) < Number(25)) {
					
					markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[5]}).bindPopup( summary[i] ).addTo(map);
					//markersTemp[i] = markers[i];

					arraySum[i].node_statusPerc = percentEquation;
					console.log("I: "+ i + " node_statusPerc: " + arraySum[i].node_statusPerc);	
					//console.log("ARRAYEP ELSE FOUR: " + Number(percentVal));
					
				}
				else if (Number(25) <= percentEquation && percentEquation <= Number(49)) {
					markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[3]}).bindPopup( summary[i] ).addTo(map);
					//markersTemp[i] = markers[i];
					//console.log("ARRAYEP ELSE THREE: " + Number(percentVal));
					arraySum[i].node_statusPerc = percentEquation;
					//console.log("I: "+ i + " node_statusPerc: " + arraySum[i].node_statusPerc);	
				}
				else if (Number(50) <= percentEquation && percentEquation <= Number(74)) {
					markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[2]}).bindPopup( summary[i] ).addTo(map);
					//markersTemp[i] = markers[i];
					//console.log("ARRAYEP ELSE TWO: " + Number(percentVal));
					arraySum[i].node_statusPerc = percentEquation;
					//console.log("I: "+ i + " node_statusPerc: " + arraySum[i].node_statusPerc);	
				}
				else if (Number(75) <= percentEquation && percentEquation <= Number(99)) {
					markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: arrayColors[1]}).bindPopup( summary[i] ).addTo(map);
					//markersTemp[i] = markers[i];
					//console.log("ARRAYEP ELSE ONE: " + Number(percentVal));
					arraySum[i].node_statusPerc = percentEquation;
					//console.log("I: "+ i + " node_statusPerc: " + arraySum[i].node_statusPerc);	
				
				}
				else if (Number(100) <= percentEquation ) {
					markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: map_markers[0]}).bindPopup( summary[i] ).addTo(map);
					//markersTemp[i] = markers[i];
					//console.log("ARRAYEP ELSE ZERO: " + Number(percentVal));
					arraySum[i].node_statusPerc = percentEquation;
					//console.log("I: "+ i + " node_statusPerc: " + arraySum[i].node_statusPerc);	
				}
				else  {// || typeof percentVal === "undefined") { 
					console.log("arraySum[i].node_acroynm: " + arraySum[i].node_acronym + " I: "+ i + " SECOND percentEquation: " + (parseInt(percentEquation)));
					markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: arrayColors[6]}).bindPopup( "FOR ERROR CHECKING <br>"+ summary[i] ).addTo(map);
					//markersTemp[i] = markers[i];
				}
			}
			else  {// || typeof percentVal === "undefined") { 
				console.log("arraySum[i].node_acroynm: " + arraySum[i].node_acronym + " I: "+ i + " LAST percentEquation: " + (parseInt(percentEquation)));
				markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: arrayColors[6]}).bindPopup( "FOR ERROR CHECKING <br>"+ summary[i] ).addTo(map);
				//markersTemp[i] = markers[i];
			} 
		}
	}
	/**************************FUNCTION EXECUTION***************************************/
	//getTotalData();
	var map_markers = mapMarkers();
	// var arrayColors = getArrayColors();
	//colorTest();
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
			for (let i = 0; i < arraySum.length; i++) {//arraySum.length; i++ ) {	
					console.log("values percent: " + arraySum[i].node_statusPerc);
				arraySum[i].node_statusPerc
				if (arraySum[i].node_active != 0) {
					//LOOK AT
					console.log("FIRST IF UPDATEMARKERDISPLAY");
					let energyTotalEquation = (parseInt(arraySum[i].pow_produce) + (parseInt(arraySum[i].node_totalInflow)* -1)) -(parseInt(arraySum[i].pow_demand)-(parseInt(arraySum[i].node_totalOutflow)));
				
					if (isNaN(parseInt(arraySum[i].node_statusPerc)))
					{
						console.log("I: "+ i + " percentEquation: " + (arraySum[i].node_statusPerc));
						//percentEquation = 1;
					}
					if (isNaN(parseInt(energyTotalEquation)))
					{
						console.log("I: "+ i + " energyTotalEquation: " + (parseInt(energyTotalEquation)));
						//energyTotalEquation = 0;
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
  		//window.location.href = "mapDev.php?name=" + javascriptVariable
		//+ "&turnOff=true"; //returns document of current page
		
		var newUrl = "mapDev.php?name=" + javascriptVariable + "&turnOff=true";
		console.log("myJavaScriptFunction Called");
		window.location.replace(newUrl);
		//window.location.reload(true);
		
		//createMarkerDisplay();
	}
	
	function revertJavascriptFunction(value) { 
  		let javascriptVariable = value;
  		//window.location.href = "mapDev.php?name=" + javascriptVariable
		//+ "&turnOff=false"; //returns document of current page
		
		var newUrl = "mapDev.php?name=" + javascriptVariable + "&turnOff=false";
		console.log("myJavaScriptFunction Called");
		window.location.replace(newUrl);
		//window.location.reload(true);
		//createMarkerDisplay();
	}
	/***********************************************************************************/
	function markerOnClicked(i,markers, arraySum, summary, clicked) { 
		//markers[i] = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: arrayColors[4]}).bindPopup( "Manually Affected Node <br>" + summary[i] ).addTo(map); //= markersTemp[i];
		console.log("BEFORE IF MARKERONCLICKED");
		//markers[i].on('mouseover', function (e) {this.openPopup();}); //this.openPopup();  ?
		//markers[i].on('mouseout', function (e) {this.closePopup();});
		if (arraySum[i].node_active == 1) {
		//******************JAVA TO PHP DATA FOR SIMULATION************************/
			//console.log("ACRONYM INSIDE ARRAY: " +   arraySum[i].node_acronym );
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
			//echo '<br>'; //DO NOT HAVE THESE OR IN SIMULATION
			//echo '<br>';
			//echo '<br>';
			?>
			
			console.log("IF AFTER MARKERCLICKED");
			/***********************SIMULATION*********************************/	
			//HANDLES SIMULATION
			//< ?php echo json_encode(mapSimulation($value_acronym,$arrayQueue,$index, $mysqli)); ?>; 
			//CALLS AND ASSIGNS ARRAY WITH NEW DATA
			//< ?php $sum =  getNodeData($mysqli); ?>
			
			
			//THIS PREVENTS GOING INTO SECOND ELSE IF 
			//myJavascriptFunction('');
			
			//var arraySum = <?php //echo json_encode($sum); ?>;
			const queryString = window.location.search;
			// splits the parameters up
			const urlParams = new URLSearchParams(queryString);
			
			var arraySum2 = <?php echo json_encode($sum); ?>;
			
			if (urlParams.has('name')) {
				arraySum = arraySum2;
			}
			
			//revertJavascriptFunction();
			//UPDATES MARKERS
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
			
			var arraySum2 = <?php echo json_encode($sum); ?>;
			
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
	//getTotalData();
	//var arrayColors = getArrayColors();
	//colorTest();
	//createMarkerDisplay();
	
	// GETS URL PARAMETERS
	const queryString = window.location.search;
	// splits the parameters up
	const urlParams = new URLSearchParams(queryString);
	
	if (urlParams.has('name')) {
		const name = urlParams.get('name');
		console.log("This is the name: " + name);
	}
	// //else {
	// console.log("markersDisplayedClicked is ran");
	// markersDisplayedClicked();
	// //}
	
	//updateMarkerDisplay(i, arraySum, markers, summary);
	/***********************************************************************************/
	//< ?php
	//$sum =  getNodeData($mysqli);//getNodeData(db_connect("senior_design_db"));
	//? >
	//var arraySum = < ?php echo json_encode($sum); ?>;
	//updateMarkerDisplay(i, arraySum, markers, summary);
	/***********************************************************************************/
	/**********
	*	LOGIC/SYNTAX ERROR HERE; 
	*	MEANT TO CLICK ON MARKER THEN SIMULATE SELECTED AND SURROUNDING MARKER WITH UPDATED DATA 
	*************/
	//maybe make if statement here to call it as many times if clicked.
	
	
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
	
	/*
	for (let i = 0; i < arraySum.length; i++) {
		markers[i].on('mouseover', function (e) {this.openPopup();}); //this.openPopup();  ?
  		markers[i].on('mouseout', function (e) {this.closePopup();}); // this.closePopup(); ?
	}
	*/
	/************************************************************************************/
	
	/**************************FUNCTION EXECUTION***************************************/
	//getTotalData();
	//var arrayColors = getArrayColors();
	//colorTest();
	//createMarkerDisplay();
	//markersDisplayedClicked(); 
	//updateMarkerDisplay(i, arraySum, markers, summary);
	/***********************************************************************************/
	//*********************************************************//
		/****
	* 	FROM: JDP
	*   FUNCTION: getTotalData();
	*	SUMS POSITIVE,NEGATIVE, ZERO VALUES INTO ITS OWN UNIQUE ARRAYS FOR MAP DATA DISPLAY
	***/
	//will use later for simulation connection
	/*
    function getTotalData() {
		for (let j = 0; j < arraySum.length; j++) {
			let row = JSON.parse(arraySum[j].node_connect); //updates row
			let totalP = 0; //resets it
			let totalN = 0;
			let totalZ = 0;
			for (var k = 0; k < row.gridList.length;k++) { //second gets node_connect's array elements
				if (Math.sign(parseInt(row.gridList[k].value)) == 1) { //checks if its a positive, neg, or 0 also adds to 'total' for receive, given
					totalP = parseInt(totalP) + parseInt(row.gridList[k].value);
				}
				else if (Math.sign(parseInt(row.gridList[k].value)) == -1) {
					totalN = parseInt(totalN) + parseInt(row.gridList[k].value);
				}
				else if (Math.sign(parseInt(row.gridList[k].value)) == 0) {
					totalZ = parseInt(totalZ) + parseInt(row.gridList[k].value);
				}  
				else {      
					console.log("ERROR: "+ row.gridList[k].value + " Line J: " + j);
					console.log("");
				}
				arrayP[j] = parseInt(totalP);
				arrayN[j] = parseInt(totalN);
				arrayZ[j] = parseInt(totalZ);
			}
		}
	}
	*/
	
	//*********************************************************//
	L.shapefile('/cs-4613/final_project/Work_Jio/assets/shapefiles/NERC_Regions_EIA.zip', {
    style: function(feature) {
        return {
            color: 'red',
            weight: 2
        };
    }
}).addTo(map);
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
      				let polyline = L.polyline(latlngs, { color: 'blue' }).addTo(map);
					console.log(latlngs);

      			}		
    		}
  		}
	}
	getNodeConnections(map, nodeList);
</script>

	
	
	