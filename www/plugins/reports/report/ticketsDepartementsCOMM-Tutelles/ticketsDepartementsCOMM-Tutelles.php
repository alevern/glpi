<?php

$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;
$DELAIMAXPRISEENCOMPTE = 432000;
$NBSECPARJOUR = 86400;

include ("../../../../inc/includes.php");

global $DB;
//TRANS: The name of the report = Financial information
$report = new PluginReportsAutoReport(__('indicateurDelaiPriseEnCompteCOMM_report_title', 'reports'));

$date = new PluginReportsDateIntervalCriteria($report, '`glpi_tickets`.`date`',
                                              __('Date'));

//Display criterias form is needed
$report->displayCriteriasForm();

//If criterias have been validated
if ($report->criteriasValidated()) {
   echo "<table class='tab_cadrehov' cellpadding='5'>";
   echo "<tr><td><th><center> Rapport sur le nombre de tickets COMMUNICATION des utilisateurs tutelles </center></th></td></tr>";
   echo "</table>";
   echo "<br><br>";


   $tableauTicket = "<table class='tab_cadrehov' cellpadding='5'>".
                    "<tr><td><b>No ticket</b></td><td><b>Demandeur</b></td>".
                    "<td><b>Titre</b></td><td><b>Ouvert</b></td></tr>";

   //$tableauTicket = "<table class='tab_cadrehov' cellpadding='5'>".
   //                 "<tr><td><th>No ticket</th></td><td><th>Demandeur</th></td>".
   //                 "<td><th>Titre</th></td><td><th>Ouvert</th></td></tr>";

   // SÃ©lection du nombre de tikets sur la pÃ©riode
   $query = "select id, name, DATE_FORMAT(date, '%d/%m/%Y %H:%i:%s') ".
            "from glpi_tickets ".
            $report->addSqlCriteriasRestriction("WHERE").
            " and entities_id = 4" ;


   $res  = $DB->query($query);
   while ($data = $DB->fetch_array($res)) {
      $idticket =  $data[0];
      $description =  $data[1];
      $ouvert =  $data[2];

      // Selection id du premier demandeur
      $query1 = "select users_id from glpi_tickets_users ".
              " where tickets_id=$idticket and type=1 ";

      $res1  = $DB->query($query1);
      while ($data1 = $DB->fetch_array($res1)) {
         $iddemandeur =  $data1[0];
         }
	
      // Selection nom du premier demandeur
      $query2 = "select CONCAT_WS(' ', realname, firstname) from glpi_users ".
               " where glpi_users.id = $iddemandeur  ";

      $res2  = $DB->query($query2);
      while ($data2 = $DB->fetch_array($res2)) {
         $nomdemandeur =  $data2[0];
         }
	

     // Selection de la catégorie/département du demandeur
     $query3 = "select usercategories_id from glpi_users where id = $iddemandeur ";

      $res3  = $DB->query($query3);
      while ($data3 = $DB->fetch_array($res3)) {
         $departement =  $data3[0];
         }

     // Departement TF
     if($departement == 105) {
       $ligneTicket = "<tr><td>".
              "<a href=\"https://tickets.femto-st.fr/index.php".
              "?redirect=ticket_$idticket\" target=\"_blank\">$idticket</a></td>".
              "<td>$nomdemandeur</td>".
              "<td>$description</td>".
              "<td>$ouvert</td></tr>";
       
       $tableauTicket = $tableauTicket.$ligneTicket;

       $nbTickets = $nbTickets + 1;
       }
   }

   echo "<table class='tab_cadrehov' cellpadding='5'>";
   echo "<tr><td><th> Nombre de tickets sur la pÃ©riode</th></td>".
        " <td>$nbTickets</td></tr></table>";

   $tableauTicket = $tableauTicket."</table>";
   echo $tableauTicket;

} else {
   Html::footer();
}


