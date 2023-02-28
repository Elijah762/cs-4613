
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	
	<!---REMOVE IF NOT INTERESTED -->
<link rel="stylesheet" href="/cs-4613/final_project/Work_Jio/assets/css/customStyle.css">
	
	
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
<!-------------------------------------->
<title> Project Neuron </title>
</head>
    
<body style="margin: 0; width: 100%;background-color: #384E77;">
    <!-- Setting up the Navigation menu --->
	
	<div>
		<nav class="navbar navbar-expand-lg navbar-light py-2 fixed-top" style="background-color: #18314F;">
        <!-- meant to hide image if screen size is reduced -->
			<div class="container-fluid">
				<!--<img class="img-fluid w-30 ps-4 d-sm-block d-flex justify-content-md-start"src="../img/nsa_img.PNG" alt="" /> -->
				<img class="img-fluid d-flex ps-4 justify-content-md-start"src="../assets/img/nsa_img.PNG" alt="" /> 
				<!--<div class=" navbar-brand text-white mb-0 ps-3 h1 d-flex justify-content-around"> Motto: "Defending Our Nation. Securing the Future."</div> -->
				<div class="navbar-brand text-white mb-0 pe-4 h1 d-flex justify-content-around">Project Neuron</div>
				
				<!--
				<div class="col-sm-2">
					<img class="img-fluid w-30 ps-2 d-sm-block align-items-center" src="../img/nsa_img.PNG" alt="" /> <!-- d-none -- >
				</div>
				<div class="col-sm-2">
					<!--- fix name displayed in changing screen size -- >
					<span class="navbar-brand text-white mb-0 ps-2 h1 align-items-center">"No Such Agency"</span>
				</div>
				<div class="col-sm-2">
					<!--- fix name displayed in changing screen size -- >
					<span class="navbar-brand text-white mb-0 ps-2 h1 align-items-center">Project Neuron</span>
				</div>
				-->
			</div>
		</nav>
	</div> 
			
    <div class="container pt-3 mt-5 align-content-center"> <!--style="background-color: white;"> -->
    	<div class="row no-gutters mr-3" >
			<!-- TRIED TO MAKE IT COMPATIBLE WITH SMALLER SCREENS/ REDUCE MAP SIZE ---->
			<div class="col-xl-2 col-md border d-sm-block overflow-auto" style="height: 890px; background-color: white;" >
            	<div class="row border-bottom"  >
					<h5> <b>Simulation Summary</b> </h5>
				</div>
				<!--FIGURE OUT SCROLL BAR FOR FUTURE USE -->
				<!--<div class="row" id="#scrollSummary" class="overflow-auto">
					<div data-bs-spy="scroll" data-bs-target="#scrollSummary" data-bs-offset="0" class="scrollspy-example" tabindex="0"> -->
				
				<!-- HANDLES SIMULATION SUMMARY -->	
				<?php include('new_Summary_J.php');?>			
				<!-- Needs scroll bar functionality for large amounts of data and nodes -->
				<!--</div> 
				</div>-->
			</div>
			<div class="col-xl-8 col-md border d-sm-block" style="background-color: white;">
				<div class="row text-center border-bottom">
					<h3><b>Simulation Map</b> </h3>
				</div>
				<div class="row">       
					 <div id="map"> 
						 
					<!-- HANDLES SIMULATION MAP -->	
						<?php include_once('new_MapDisplay_J.php');?>
						 
					</div>
				</div>	
			</div>
			<div class="col-xl-2 col-md border d-sm-block" style="background-color: white;">
            	<div class="row border-bottom" >
					<h5><b>Simulation Key</b> </h5> 
				</div>
				<div class="row">
					<ul>
						<p  style="color:red;"><br><b>Red: </b>0-24%</p>
						<p  style="color:orange;"><b>Orange: </b>25-49%</p>
						<p  style="color:#FFD700;"><b>Yellow: </b>50-74%</p>
						<p  style="color:green;"><b>Green: </b>75-99%</p>
						<p  style="color:blue;"><b>Blue: </b>100%</p>
						<p  style="color:darkgrey;"><b>Silver: </b>Manually turned off</p>
					</ul>
				</div>
				<div class="row border-bottom border-top">
					<h5><b>Simulation Hazards</b> </h5>
				</div>
				<div class="row">
					<ul>
						<p><br>Hurricane</p>
						<p>Fire</p>
						<p>Tornado</p>
						<p>Earthquake</p>
						<p>EMP</p>
					</ul>
				</div>
				<div class="container1">
					<!--<button class="btn-1"> RESET SIMULATION </button> -->
					<!-- MAYBE USE THIS INSTEAD FOR PHP USE -->
                      <div id="demo">
                  <!--<h2>Let AJAX change this text</h2> -->
                 <!-- <button type="button" onclick="loadDoc()" class="btn-1">RESET SIMULATION</button> -->
                    </div>
					<input type="submit" class="btn-1" name="Button" value="RESET SIM." /> 
					<!---<button class="btn-2"> RESET2 </button> --->
					<!--<button type="button" id="resetMap" class="btn btn-danger relative; left:80px; top:2px;" onclick="func(event)">RESET</button> -->
				</div>
				<script type="text/javascript">
                    
                 $(document).ready(function(){  
				    $('.btn-1').click(function() {
					$.ajax({
						type: "POST",
						url: "displayFunctions.php",
						data: {'input': "Success" }
						}).done(function() {
							alert('Reset Successful');
						});
					});
                 });
                    
               <?php include_once('displayFunctions.php');?>
             
                   
				</script>
     
			</div>
		</div>
	</div>
	<?php
			$mysqli->close();
	?>
</body> 
</html>