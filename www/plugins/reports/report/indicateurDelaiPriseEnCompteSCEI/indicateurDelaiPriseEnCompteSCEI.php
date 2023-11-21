<?php

$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;
#$DELAIMAXPRISEENCOMPTE = 345600;
$DELAIMAXPRISEENCOMPTE = 432000;

include ("../../../../inc/includes.php");

global $DB;
//TRANS: The name of the report = Financial information
$report = new PluginReportsAutoReport(__('indicateurDelaiPriseEnCompteSCEI_report_tile', 'reports'));

$date = new PluginReportsDateIntervalCriteria($report, '`glpi_tickets`.`date`',
                                              __('Date'));

//Display criterias form is needed
$report->displayCriteriasForm();

//If criterias have been validated
if ($report->criteriasValidated()) {
   echo "<table class='tab_cadrehov' cellpadding='5'>";
   echo "<tr><td><th> Rapport sur le nombre de tickets pris en compte aprÃ¨s le dÃ©lai de 4 jours</th></td></tr>";
   echo "</table>";
   echo "<br><br>";


//   $report->setColumnsNames(array( 'nbTickets' => "Nombre de tickets") );

   // SÃ©lection du nombre de tikets sur la pÃ©riode
   $query = "select count(*) as 'nbTickets' from glpi_tickets ".
            $report->addSqlCriteriasRestriction("WHERE").
            "and entities_id = 9 ";

// echo "** $query <br><br>";
   $res  = $DB->query($query);
   while ($data = $DB->fetch_array($res)) {
      $nbTickets =  $data[0];
      }

   echo "<table class='tab_cadrehov' cellpadding='5'>";
   echo "<tr><td><th> Nombre de tickets sur la pÃ©riode</th></td>".
        " <td>$nbTickets</td></tr>";

   // SÃ©lection du nombre de tikets dÃ©passant le dÃ©lai maximum de prise en compte
   $query1= "select count(*) as 'nbTickets' from glpi_tickets ".
            $report->addSqlCriteriasRestriction("WHERE").
            " and takeintoaccount_delay_stat > ".$DELAIMAXPRISEENCOMPTE.
            " and entities_id = 9 " ;


  $res1  = $DB->query($query1);
   if($DB->numrows($res1) > 0) {
      while ($data1 = $DB->fetch_array($res1)) {
         $nbTicketsPrisEncompteApresDelai = $data1[0];
         }
      $pourcent = ($nbTicketsPrisEncompteApresDelai / $nbTickets ) * 100;
      $pourcentFormate = number_format($pourcent, 2);

      echo "<tr><td><th> Nombre de tickets pris en compte aprÃ¨s 4 jours</th></td>".
           "<td>$nbTicketsPrisEncompteApresDelai ($pourcentFormate %)</td></tr>";
      echo "</table>";


      // SÃ©lection du informations sur tikets dÃ©passant le dÃ©lai maximum de prise en comp$
      $query2=  "select id, ".
             " DATE_FORMAT(date, '%d/%m/%Y %H:%i:%s'), status, name, ".
             " SEC_TO_TIME(glpi_tickets.takeintoaccount_delay_stat) ".
             " from glpi_tickets ".
            $report->addSqlCriteriasRestriction("WHERE").
            " and takeintoaccount_delay_stat > ".$DELAIMAXPRISEENCOMPTE.
            " and entities_id = 9 ".
            " order by id "  ;
//echo $query2;

      $res2  = $DB->query($query2);
      if($DB->numrows($res2) > 0) {
         echo "<table class='tab_cadrehov' cellpadding='5' border=0>";

         echo "<tr><td><th>No ticket</th></td><td><th>Ouvert</th></td>".
              "<td><th>Statut</th></td><td><th>Titre</th></td>".
              "<td><th>DÃ©lai prise en compte</th></td></tr>";
      while ($data2 = $DB->fetch_array($res2)) {
         $id = $data2[0];
         $dateOuverture = $data2[1];
         switch($data2[2]) {
                case 1:
                   $statut = "Nouveau";
                   break;
                case 2:
                   $statut = "En cours attribu&eacute;";
                   break;
                case 3:
                   $statut = "En cours planifi&eacute;";
                   break;
                case 4:
                   $statut = "En attente";
                   break;
                case 5:
                   $statut = "Résolu";
                   break;
                case 6:
                   $statut = "Ferm&eacute;";
                   break;
            }

         $titre = $data2[3];
         $delaiPriseEnCompte = $data2[4];

         echo "<tr><td></td><td>".
              "<a href=\"https://tickets.femto-st.fr/index.php".
              "?redirect=ticket_$id\" target=\"_blank\">$id</a></td>".
              "<td></td><td>$dateOuverture</td>".
              "<td></td><td>$statut</td><td></td><td>$titre</td>".
              "<td></td><td>$delaiPriseEnCompte</td></tr>";
                  }
         echo "</table>";

    }
   }
   else {
        echo "<tr><td><th> Nombre de ticketspris en compte aprÃ¨s 5 jours</th></td>".
           "<td>0</td></tr>";
        echo "</table>";
      }


} else {
   Html::footer();
}
