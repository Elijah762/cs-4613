<?php
/***
*	CREATES DB CONNECTION 
****/
function db_connect($db)
{
    $config = require 'config.php';
    $hostname="localhost";
    $username=$config['user'];
    $password=$config['password'];
	$db="senior_design_db";
	$mysqli= new mysqli($hostname, $username, $password, $db);
	if (mysqli_connect_errno())
	{
		die("Error connecting to database: ".mysqli_connect_error());
	}
	return $mysqli;
}

function redirect ( $uri )
{ ?>
	<script type="text/javascript">
	<!--
	document.location.href="<?php echo $uri; ?>";
	-->
	</script>
<?php die;
}
?>
