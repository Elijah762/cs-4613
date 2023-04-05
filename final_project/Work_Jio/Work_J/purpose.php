<!doctype html>
<html>
<head>
<title> Project Neuron </title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="/cs-4613/final_project/Work_Jio/assets/css/styles.css">
	
	
	<!------------------------------>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
     integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
     crossorigin=""/>
<!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
     integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
     crossorigin=""></script>
	
	
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
<!-- JQUERY SCRIPTS -->
    <!----VERIFY IF  BELOW SCRIPT IS CORRECT OR NEEDED ---->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js" type="text/javascript"></script>
<!--<script src="./assets/js/jquery-1.12.4.js"></script> -->
<!--------------------------------------------->
<link rel="stylesheet" href="/cs-4613/final_project/Work_Jio/assets/leaflet.shapefile-gh-pages/gh-pages.css">
<script src="/cs-4613/final_project/Work_Jio/assets/leaflet.shapefile-gh-pages/leaflet.shpfile.js"></script>
<script src="/cs-4613/final_project/Work_Jio/assets/leaflet.shapefile-gh-pages/shp.js"></script>
<!-------------------------------------->

<style>
	body {margin: 0; width: 100%; background-color: #384E77; }
</style>

</head>
<body>
    <!-- Setting up the Navigation menu --->
	<div>
		<nav class="navbar navbar-expand-lg navbar-light py-2 fixed-top"
			style="background-color: #18314F;">
			<div class="container-fluid">
		   
			<!-- If we want the NSA logo to be a link to the sim --->

				<a href="mapDev.php"> <img class="img-fluid d-flex ps-4 justify-content-md-start"
					src="../assets/img/nsa_img2.2.png"
					alt="" >
				</a>
				
				<!-- 
				<img class="img-fluid d-flex ps-4 justify-content-md-start"
					src="../assets/img/nsa_img2.1.png"
					alt="" />
				--->
				<h3 class="navbar-title text-white">PROJECT NUERON / PURPOSE</h3>
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link text-white"
						href="mapDev.php">Simulation</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white"
						href="definitions.php">Definitions</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white"
						href="sources.php">Sources</a>
					</li>
				</ul>
			</div>
		</nav>
	</div> 

	<div class="container pt-3 mt-5 align-content-center">
    	
	</div>
			
    
</body> 
</html>