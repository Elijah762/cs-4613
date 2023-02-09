// JavaScript Document
const map = L.map('map', {
  			center: [38.1667, -100.1667],
  			zoom: 4.0
	  });
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' }).addTo(map);
// create function to handle data and add to map popup
//Grab data from database populates the exact lat and long then add it to marker
let produced = 100; //get from database
let consumed = 25; //get from database
let population = 100000;


const marker1 = L.marker([31.000000, -97.7333]).bindPopup('Region 1 Texas, ERCO' + '<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);
const marker2 = L.marker([39.3016 , -120.5469]).bindPopup('Region 2 California, CISO' + '<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);
const marker3 = L.marker([40.0990, -75.4691]).bindPopup('Region 3 Mid-Atlantic, PJM' + '<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);
const marker4 = L.marker([42.6426, -73.7429]).bindPopup('Region 4 ,New York Independent System Operator (NYIS)' + '<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);
const marker5 = L.marker([42.2043, -72.6162]).bindPopup('Region 5 ,ISO New England (ISNE)' + '<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);
const marker6 = L.marker([33.7537, -84.3863]).bindPopup('Region 6 Southeast, Southern Company Services, Inc. - Trans (SOCO) ' + '<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);


const marker7_BPAT = L.marker([45.5230, -122.6764]).bindPopup('Region 7, Bonneville Power Administration (BPAT)' + '<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);
//marker7 pace is fairly incorrect
const marker7_PACE = L.marker([41.5875, -109.2029]).bindPopup('Region 7, PacifiCorp East (PACE)' + '<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);


const marker8 = L.marker([35.9662227, -83.9206]).bindPopup('Region 8 Tennessee, Tennessee Valley Authority (TVA)'+'<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);
const marker9 = L.marker([39.9700, -86.1700]).bindPopup('Region 9 Midwest MIDW, Midcontinent Independent System Operator, Inc. (MISO)' +'<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);
const marker10 = L.marker([26.8930, -80.0533]).bindPopup('Region 10 Florida, Florida Power & Light Co. (FPL)'+'<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);
const marker11 = L.marker([35.2271, -80.8431]).bindPopup('Region 11 Carolinas, Duke Energy Carolinas (DUK)'+'<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);


const marker12 = L.marker([34.7464, -92.2895]).bindPopup('Region 12 Central, Southwest Power Pool (SWPP)'+'<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);

//Missing Last Major node/Maker? We still need to include the smaller ones maybe and color code the makers at least by region
const marker13 = L.marker([-37.6703, 176.2120]).bindPopup('Region 13, Whitsunday Island'+'<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);