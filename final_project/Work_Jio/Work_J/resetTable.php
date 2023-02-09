<?php
include_once("db_access.php");

$sql="TRUNCATE `node_info`; INSERT INTO `node_info` SELECT * FROM `node_simulation_static`;";
$mysqli=db_connect("senior_design_db");

$result = $mysqli->multi_query($sql) or
	die("Something went wrong with $sql".$mysqli->error);

echo "Table Reseted!<br>";

?>