

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
    baseMap.addTo(map);

	const popup = L.popup({
		closeButton: false,
		autoClose: false
	})
	.setLatLng([55.1867, -98.1667])
	.setContent('<p>Simulation Map</p>')
	.openOn(map);

    //Define Globals
	let arraySum = <?php echo json_encode($sum); ?>; //echos out 'Array's contents maybe for loop to get all of data? maybe?
    let nodeList = <?php echo json_encode($sum); ?>;
    let summary = [];
    let mapPins = mapMarkers();
    let markers = [];
    let produce, totInflow, demand, outflow;//globals


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
            //NEED OFFLINE PIN
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
        let percent = arraySum[i].node_statusPerc;
        if (Math.sign(percent) == 0 || Math.sign(percent) == -1 )       {pinNum = 4;}
        else {
            if (arraySum[i].node_active == 0)                           {pinNum = 5;}//deactivate
            else if (Number(percent) < Number(25))                      {pinNum = 4;}
            else if (Number(25) <= percent && percent <= Number(49))    {pinNum = 3;}
            else if (Number(50) <= percent && percent <= Number(74))    {pinNum = 2;}
            else if (Number(75) <= percent && percent <= Number(99))    {pinNum = 1;}
            else if (Number(100) <= percent)                            {pinNum = 0;}
        }
        let marker = L.marker([arraySum[i].node_lat, arraySum[i].node_lon], {icon: mapPins[pinNum]}).bindPopup(summary[i]);
        marker.on('click', function(e) {
            console.log('Curr = ' + arraySum[i].node_active)
            arraySum[i].node_active == 1 ? arraySum[i].node_active = 0 : arraySum[i].node_active = 1
            setPinStatus(i);
        });
        marker.on('mouseover', function(e) {this.openPopup();});
        marker.on('mouseout', function(e) {this.closePopup();});

        let clusterMarker = L.markerClusterGroup();
        clusterMarker.addLayer(marker);

        markers[i] = marker;
        marker.addTo(map);
    }


    function setSummary(i, energyTotalEquation) {
        setData(i);
        summary[i] = arraySum[i].node_name + '<br> Energy Produced: ' + produce + '<br> Energy Demand:  ' + demand + '<br> Outflow: ' + outflow  + '<br> Inflow: ' + totInflow +'<br> Energy Total: ' + energyTotalEquation + '<br> Population Served:  ' + arraySum[i].node_popServe;
    }


    function updateMarkerDisplay() {
        for (let i = 0; i < arraySum.length; i++) {
            if (arraySum[i].node_active != 1) {
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
            //console.log("nodeConnect:", nodeConnect);
            //console.log("out of loop");
            for (let j = 0; j < nodeConnect.gridList.length; j++) {
                // console.log("in loop");
                // console.log("Connecting to node: " + nodeConnect.gridList[j].name);
                let connectedNode = nodeList.find((item) => item.node_acronym === nodeConnect.gridList[j].name);
                //console.log("found node");

                if (connectedNode) {
                    let connectedNodeLat = connectedNode.node_lat;
                    let connectedNodeLng = connectedNode.node_lon;


                    // add a polyline to map with the nodes longitude and latitude
                    let latlngs = [[nodeLat, nodeLng], [connectedNodeLat, connectedNodeLng]];
                    let polyline = L.polyline(latlngs, { color: 'blue' }).addTo(map);
                    //console.log(latlngs);

                }
            }
        }
    }


	/**************************FUNCTION EXECUTION***************************************/
    createMarkerDisplay();

	L.shapefile('/cs-4613/final_project/Work_Jio/assets/shapefiles/NERC_Regions_EIA.zip', {
        style: function(feature) {
            return {
                color: 'red',
                weight: 2
            };
        }
    }).addTo(map);

	getNodeConnections(map, nodeList);
</script>