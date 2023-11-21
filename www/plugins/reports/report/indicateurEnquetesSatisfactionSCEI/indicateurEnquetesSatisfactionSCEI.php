<?php

$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;

include ("../../../../inc/includes.php");

global $DB;
//TRANS: The name of the report = Financial information
$report = new PluginReportsAutoReport(__('indicateurEnquetesSatisfactionSCEI_report_title', 'reports'));

$date = new PluginReportsDateIntervalCriteria($report, '`glpi_tickets`.`date`',
                                              __('Date'));

//Display criterias form is needed
$report->displayCriteriasForm();

//If criterias have been validated
if ($report->criteriasValidated()) {


   echo "<table class='tab_cadrehov' cellpadding='5'>";
   echo "<tr><td><th> Rapport sur le nombre d'enquêtes de satisfaction envoyées</th></td></tr>";
   echo "</table>";
   echo "<br><br>";


//   $report->setColumnsNames(array( 'nbTickets' => "Nombre de tickets") );

   // SÃ©lection du nombre de tikets sur la pÃ©riode
   $query = "select count(*) as 'nbTickets' from glpi_tickets ".
            $report->addSqlCriteriasRestriction("WHERE").
            " and entities_id = 9 ".
            " and status = 6 " ;


   $res  = $DB->query($query);
   if($DB->numrows($res) > 0) {
      while ($data = $DB->fetch_array($res)) {
         $nbTickets =  $data[0];
         }


      echo "<table class='tab_cadrehov' cellpadding='5'>";
      echo "<tr><td><th> Nombre d'enquêtes envoyées sur la période</th></td>".
        " <td>$nbTickets</td></tr>";
      echo "</table>";
      }
    else {
      echo "<table class='tab_cadrehov' cellpadding='5'>";
      echo "<tr><td><th> Nombre d'Ã©nquÃªtes envoyÃ©es sur la pÃ©riode</th></td>".
        " <td>0</td></tr>";
      echo "</table>";

      }



} else {
   Html::footer();
}
