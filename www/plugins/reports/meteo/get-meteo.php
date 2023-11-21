<?php
define("GLPI_ROOT", "../../..");
include(GLPI_ROOT."/inc/includes.php");

if ($_SERVER['HTTP_API_KEY'] !== "db1c067f3d00-7d8a-1154-6282-b0861f64")
        die('"Api-Key invalide."');

global $DB;

$query1 = "
SELECT E.name as NOM, COUNT(T.id) as TOTAL
FROM glpi_entities E, glpi_tickets T
WHERE T.entities_id = E.id
AND E.id <> 0
AND T.is_deleted = 0
AND T.status IN(5,6)
GROUP BY E.name";
$query2 = "
SELECT E.name as NOM, COUNT(T.id) as TOTAL
FROM glpi_entities E, glpi_tickets T
WHERE T.entities_id = E.id
AND E.id <> 0
AND T.is_deleted = 0
AND T.status IN(2,3,4)
GROUP BY E.name";
$query3 = "
SELECT E.name as NOM, COUNT(T.id) as TOTAL
FROM glpi_entities E, glpi_tickets T
WHERE T.entities_id = E.id
AND E.id <> 0
AND T.is_deleted = 0
AND T.status IN(1)
GROUP BY E.name";

$list1 = array();
$list2 = array();
$list3 = array();
$res1 = $DB->query($query1);
$res2 = $DB->query($query2);
$res3 = $DB->query($query3);

while ($data = $DB->fetch_array($res1))
    $list1[] = array($data[0], (int)$data[1]);

while ($data = $DB->fetch_array($res2))
    $list2[] = array($data[0], (int)$data[1]);

while ($data = $DB->fetch_array($res3))
    $list3[] = array($data[0], (int)$data[1]);

// $list1 = [
//     ["Communication", 3351],
//     ["Gestion", 3010],
//     ["Infrastructure", 738],
//     ["Mimento-facilities", 111],
//     ["Qualité", 119],
//     ["SCEI", 1335],
//     ["SCI", 25969],
//     ["SCM", 3405]
// ];

// $list2 = [
//     ["Communication", 94],
//     ["Gestion", 1030],
//     ["Qualité", 7],
//     ["SCI", 255],
//     ["SCM", 43]
// ];

// $list3 = [
//     ["Gestion", 32],
//     ["SCEI", 3]
// ];

$pushed = false;
for ($i = 0; $i < count($list1); $i++){
    for ($y = 0; $y < count($list2); $y++)
        if($list1[$i][0] == $list2[$y][0] && !$pushed){
            array_push($list1[$i], $list2[$y][1]);
            $pushed = true;
        }
    if(!$pushed) array_push($list1[$i], 0); else $pushed = false;
    for ($y = 0; $y < count($list3); $y++)
        if($list1[$i][0] == $list3[$y][0] && !$pushed){
            array_push($list1[$i], $list3[$y][1]);
            $pushed = true;
        }
    if(!$pushed) array_push($list1[$i], 0); else $pushed = false;
    if($list1[$i][0] == "Qualité") $list1[$i][0] = "Qualite";
}

header('Content-Type: application/json;charset=utf-8');  
echo json_encode($list1);
?>
