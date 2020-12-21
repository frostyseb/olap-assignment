<?php

function createConn() {
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpassword = "";
	
	$conn = new mysqli($dbhost, $dbuser, $dbpassword) or die("Connection failed. Error: %s\n" . $conn->error);
	
	return $conn;
}

function terminateConn() {
	$conn->close();
}

$url = "http://localhost/GitHub/olap-assignment/";

?>