<?php
/***
 * 	FROM: JDP
 ***/
/******************************DB ACCESS*********************************************/
include_once("db_access.php");
include("inflowOutflowCalculation.php");
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

$sum =  getNodeData($mysqli);//getNodeData(db_connect("senior_design_db"));
?>

<script type="text/javascript">
    //------------------------------- CREATES AND CALLS MAP API --------------------------------------------//
    const map = L.map('map', {
            center: [41.1667, -100.1667],
            zoom: 3.5
        });
    let baseMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' });

    baseMap.addTo(map);
    let arraySum = <?php echo json_encode($sum); ?>; //echos out 'Array's contents maybe for loop to get all of data? maybe?
    let summary = [];
    let markers = L.markerClusterGroup();
    let produce, totInflow, demand, outflow;

    let mapPins = makeMapPinTemplate();
    colorTest();
    createMarkerDisplay();

    /****
     * 	FROM: JDP
     * 	FUNCTION: makeMapPinTemplate();
     *	HANDLES COLOR OUTPUTS FOR ARRAYS, LATER USED IN MAP DISPLAY
     * 	arrayColors[] , 0-6, BLUE -> GREEN -> YELLOW -> ORANGE -> RED ; SILVER, BLACK
     ***/
    function makeMapPinTemplate() {
        let pinColorNames = ['blue','green','yellow','orange','red', 'grey', 'black']; // error checking can be with grey or black.
        let mapPinTypes = [];
        for (let i = 0; i < pinColorNames.length; i++) {
            mapPinTypes[i] = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-'+ pinColorNames[i] +'.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        }
        return mapPinTypes;
    }

    //********************COLOR TEST*************************//
    function colorTest() {
        let arrNum = [];
        let j = 110;
        for (let i = 0; i < 7; i++){
            if (i == 6) {
                arrNum[i] = L.marker([15, -80], {icon: mapPins[i]}).bindPopup("FOR ERROR CHECKING").addTo(map);
            }
            else if (i == 5) {
                arrNum[i] = L.marker([15, -85], {icon: mapPins[i]}).bindPopup("FOR MANUALLY AFFECTED MARKER").addTo(map);
            }
            else {
                arrNum[i] = L.marker([15, -j], {icon: mapPins[i]}).addTo(map);
            }
            j = j-5;
        }
    }
    /****
     * 	FROM: JDP
     *   FUNCTION: createMarkerDisplay();
     *	HANDLES MATH EQUATION NEEDED TO SHOW DATA OUTPUT, WILL REFLECT MARKER COLOR DEPENDING ON ITS ENERGY STATUS
     *	**MATH ERROR FOUND HERE**
     ***/
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
        setData(i);
        return (produce - totInflow) / (demand - outflow) * 100;
    }

    function setData(i){
        produce = parseInt(arraySum[i].pow_produce);
        totInflow = parseInt(arraySum[i].node_totalInflow);
        demand = parseInt(arraySum[i].pow_demand);
        outflow = parseInt(arraySum[i].node_totalOutflow);
    }

    function getEnergyTotal(i) {
        setData(i);
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
        marker.on('click', function(e) {
            //remove map pin layer
            if(arraySum[i].node_active !== 0) {
                console.log("Turn off")
                arraySum[i].node_active = 0;
                arraySum[i].node_statusPerc = 0;
                arraySum[i].node_totalOutflow = 0;
                arraySum[i].pow_produce = 0;
            }
            else {
                console.log("Turn on")
                arraySum[i].node_active = 1;
                arraySum[i].node_statusPerc = 1;
                arraySum[i].node_totalOutflow = 0; // pull from local
                arraySum[i].pow_produce = 0;// pull from local
            }
            //create new map pin layer
            updateMarkerDisplay();
        });
        marker.on('mouseover', function(e) {this.openPopup();});
        marker.on('mouseout', function(e) {this.closePopup();});
        markers.addLayer(marker);
    }

    function setSummary(i, energyTotalEquation) {
        setData(i);
        summary[i] = arraySum[i].node_name + '<br> Energy Produced: ' + produce + '<br> Energy Demand:  ' + demand + '<br> Outflow: ' + outflow  + '<br> Inflow: ' + totInflow +'<br> Energy Total: ' + energyTotalEquation + '<br> Population Served:  ' + arraySum[i].node_popServe;
    }

    /***
     *	MADE BY: JOSE -JAYDEN HELPED IN EQUATION
     *	FUNC: updateMarkerDisplay()
     *	PARAMETERS: i, arraySum, markers, summary
     *	HANDLES INITIAL MARKER COLORS DEPENDING ON DATA RETRIEVED FROM DB
     ***/
    function updateMarkerDisplay() {
        for (let i = 0; i < arraySum.length; i++) {
            if (arraySum[i].node_active != 0) {
                let energyTotalEquation = getEnergyTotal(i);
                setSummary(i, energyTotalEquation);
                if (arraySum[i].node_statusPerc == null)  {
                    let marker = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: mapPins[6]}).bindPopup( "FOR ERROR CHECKING <br>"+ summary[i] ).addTo(map);
                    markers.addLayer(marker);
                }
                else {setPinStatus(i);}
            }
            else {
                let energyTotalEquation = getEnergyTotal(i);
                setSummary(i, energyTotalEquation);
                let marker = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: mapPins[5]}).bindPopup( "FOR ERROR CHECKING <br>"+ summary[i] ).addTo(map);
                markers.addLayer(marker);
            }
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
    console.log("markersDisplayedClicked is ran");

    console.log("MarkerMouseHover is ran");

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
					

					let latlngs = [[nodeLat, nodeLng], [connectedNodeLat, connectedNodeLng]];
      				let polyline = L.polyline(latlngs, { color: 'blue' }).addTo(map);
					console.log(latlngs);

      			}		
    		}
  		}
	}
	getNodeConnections(map, nodeList);
</script>