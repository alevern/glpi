<?php
define("GLPI_ROOT", "../../..");
include(GLPI_ROOT."/inc/includes.php");

if ($_SERVER['HTTP_API_KEY'] !== "46f1680b-2826-4511-a8d7-00d3f760c1bd")
	die('"Api-Key invalide."');

$param = array();
parse_str($_SERVER["QUERY_STRING"], $param);

if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $param["from"])
|| !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $param["to"]))
	die('"Les parametres from et to doivent etre renseignes au format AAAA-MM-JJ"');

global $DB;

$query = "
	SELECT U.name AS login,
	T.entities_id AS entity,
	SUBSTR(T.date, 1, 10) AS date,
	SUBSTR(T.solvedate, 1, 10) AS resolution

	FROM glpi_tickets AS T
	LEFT JOIN glpi_tickets_users AS TU ON TU.tickets_id = T.id
	LEFT JOIN glpi_users AS U ON U.id = TU.users_id

	WHERE T.date BETWEEN '".$param["from"]."' AND '".$param["to"]."'
		AND TU.type = 1
		AND T.is_deleted = 0

	ORDER BY T.date";

$list = array();
$res  = $DB->query($query);
while ($data = $DB->fetch_array($res))
	$list[] = array($data[0], (int)$data[1], $data[2], $data[3]);

echo json_encode($list);
?>
