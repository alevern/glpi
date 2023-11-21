<?php

include("../../../inc/includes.php");

if ($_SERVER['HTTP_API_KEY'] !== "46f1680b-2826-4511-a8d7-00d3f760c1bd")
	die('"Api-Key invalide."');

global $DB;

$query = "
	SELECT id,
		name AS nom
	FROM glpi_entites
	ORDER BY name";

$list = array();
$res  = $DB->query($query);
while ($data = $DB->fetch_array($res))
	$list[] = (object)["id" => (int)$data[0], "nom" => $data[1]];

$json = json_encode($list);
echo $json;

?>
