<?php
	/***
	* 	FROM: JDP
	***/
    /******************************DB ACCESS*********************************************/
	include_once("db_access.php");
	//db_connect("senior_design_db");
	/***********************************************************************************/
?>

<?php
//RESETS MAP TO DEFAULT; MAY OR  MAY NOT WORK DEPENDING ON PERMISSION STATUS, MAY NEED TO UPDATE INSTEAD
	

     function getResetNodeData() {
		$sql="TRUNCATE `node_info`; INSERT INTO `node_info` SELECT * FROM `node_simulation_static`;";
		$mysqli=db_connect("senior_design_db");

		$result = $mysqli->multi_query($sql) or
			die("Something went wrong with $sql".$mysqli->error);
	 	$mysqli->close();
	}

    /******************************************************/
/*
    function redirect ( $uri )
    { ?>
        <script type="text/javascript">
        <!--
        document.location.href="<?php echo $uri; ?>";
        -->
        </script>
    <?php die;
    }
    */
    /******************************************************/
    /*
    function resetDataFunction() { 
           // let javascriptVariable = value;
            //window.location.href = "mapDev.php?name=" + javascriptVariable
            //+ "&turnOff=true"; //returns document of current page

            $resetUrl = "mapDev.php";
            //console.log("myJavaScriptFunction Called");
            window.location.replace(resetURL);
            //window.location.reload(true);

            //createMarkerDisplay();
    }
    */
    /************************************************/

    if(isset($_POST['input'])){
        echo getResetNodeData();
         //header("Location: https://ec2-3-133-148-65.us-east-2.compute.amazonaws.com/myAPI/apiUpload.php?msg=success");
       // header("Location: https://ec2-13-59-220-105.us-east-2.compute.amazonaws.com/Work_Jio/Work_J/mapDev.php");
        //exit();
        //resetDataFunction();
       // echo  redirect ( "mapDev.php?msg=Success" );
    }

?>