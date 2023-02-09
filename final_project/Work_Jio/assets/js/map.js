// JavaScript Document
const map = L.map('map', {
	center: [38.1667, -100.1667],
	zoom: 4.0
});

var baseMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' });

baseMap.addTo(map);

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
const marker7 = L.marker([45.5230, -122.6764]).bindPopup('Region 7, Bonneville Power Administration (BPAT)' + '<br> Energy Produced: ' + produced + '<br> Energy Consumed: ' + consumed + '<br> Population Affected: ' + population).addTo(map);
const marker8 = L.marker([-37.9770, 177.0570]).bindPopup('Region 8, Whitsunday Island').addTo(map);
const marker9 = L.marker([-41.0376, 173.0170]).bindPopup('Region 9, Whitsunday Island').addTo(map);
const marker10 = L.marker([-37.6703, 176.2120]).bindPopup('Region 10, Whitsunday Island').addTo(map);
const marker11 = L.marker([-37.9770, 177.0570]).bindPopup('Region 11, Whitsunday Island').addTo(map);
const marker12 = L.marker([-41.0376, 173.0170]).bindPopup('Region 12, Whitsunday Island').addTo(map);
const marker13 = L.marker([-37.6703, 176.2120]).bindPopup('Region 13, Whitsunday Island').addTo(map);
