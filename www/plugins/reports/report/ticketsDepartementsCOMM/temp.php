
   echo "<table class='tab_cadrehov' cellpadding='5'>";
   echo "<tr><td><th> Rapport sur le nombre de tickets par d&eacute;partement</th></td></tr>";
   echo "</table>";
   echo "<br><br>";


//  $report->setColumnsNames(array( 'nbTickets' => "Nombre de tickets") );

   // SÃ©lection du nombre de tikets sur la pÃ©riode
   $query = "select count(*) as 'nbTickets' from glpi_tickets ".
            $report->addSqlCriteriasRestriction("WHERE").
            " and entities_id = 4" ;

   $res  = $DB->query($query);
   while ($data = $DB->fetch_array($res)) {
      $nbTickets =  $data[0];
      }

   echo "<table class='tab_cadrehov' cellpadding='5'>";
   echo "<tr><td><th> Nombre de tickets sur la pÃ©riode</th></td>".
        " <td>$nbTickets</td></tr></table>";

  // Variables pour compter le nombre de tickets par groupe d'utilisateurs
  $NbTF = 0;
  $NbAS2M = 0;
  $NbMEC = 0;
  $NbMN2S = 0;
  $NbOPT = 0;
  $NbDISC = 0;
  $NbCOMMUN = 0;
  $NbENERGIE = 0;
  $NbDMA = 0;
  $NbMECAPPLI = 0;
  $NbCOMMInt = 0;
  $NbCOMMExt = 0;
  $NbCOMMTut = 0;

   // SÃ©lection de l'id du ticket sur la période
   $tickets = array();

   $query1= "select id from glpi_tickets ".
            $report->addSqlCriteriasRestriction("WHERE").
            " and entities_id = 4" ;


   $res1  = $DB->query($query1);


$noT = 0;

   echo "<table class='tab_cadrehov' cellpadding='5' border=0>";

// Affichage ticket par ticket
//   echo "<tr><td><th>No ticket</th></td><td><th>Id</th></td>".
//        "<td><th>User</th></td><td><th>Dep</th></td></tr>";

   echo "<tr><td><th>D&eacute;partement</th></td><td><th>Nombre tickets</th></td>".
     "<td><th>Pourcentage</th></td></tr>";

   if($DB->numrows($res1) > 0) {
      while ($data1 = $DB->fetch_array($res1)) {
         $idTicket = $data1[0];

          // SÃ©lection du premier demandeur du ticket
          $query2= "select users_id from glpi_tickets_users ".
            "where type = 1 and tickets_id = $idTicket" ;

          $res2  = $DB->query($query2);

          if($DB->numrows($res2) > 0) {
             while ($data2 = $DB->fetch_array($res2)) {

                // ticket non encote traité
                if (in_array($idTicket, $tickets) == FALSE) {
                   $idUser = $data2[0];
                   array_push($tickets, $idTicket);
                   }
                }
             }


          // SÃ©lection de la catégorie de l'utilisateur
          $query3= "select usercategories_id from glpi_users ".
                        "where id = $idUser ";

          $res3  = $DB->query($query3);

          if($DB->numrows($res3) > 0) {
            while ($data3 = $DB->fetch_array($res3)) {
                $idDep = $data3[0];
               }
            }

          switch($idDep) {
                case 1:
                    $Dep =  "TF";
                    $NbTF = $NbTF + 1;
                   break;
                case 2:
                   $Dep =  "AS2M";
                    $NbAS2M = $NbAS2M + 1;
                   break;
                case 3:
                   $Dep =  "M&eacute;canique Appliqu&eacute;e ";
                    $NbMEC = $NbMEC + 1;
                   break;
                case 5:
                   $Dep =  "MN2S";
                    $NbMN2S = $NbMN2S + 1;
                   break;
                case 6:
                   $Dep =  "OPTIQUE";
                    $NbOPT = $NbOPT + 1;
                   break;
                case 7:
                   $Dep =  "DISC";
                    $NbDISC = $NbDISC + 1;
                   break;
                case 8:
                   $Dep =  "COMMUN";
                    $NbCOMMUN = $NbCOMMUN + 1;
                   break;
                case 10:
                   $Dep =  "ENERGIE";
                    $NbENERGIE = $NbENERGIE + 1;
                   break;
                case 11:
                   $Dep =  "DMA";
                    $NbDMA = $NbDMA + 1;
                   break;
                case 12:
                   $Dep =  "MECAPPLI";
                    $NbMECAPPLI = $NbMECAPPLI + 1;
                   break;
                case 103:
                   $Dep =  "Utilisateurs internes";
                    $NbCOMMInt = $NbCOMMInt + 1;
                   break;
                case 104:
                   $Dep =  "Utilisateurs externes";
                    $NbCOMMExt = $NbCOMMExt + 1;
                   break;
                case 105:
                   $Dep =  "Utilisateurs tutelles";
                    $NbCOMMTut = $NbCOMMTut + 1;
                   break;
                   }



$noT = $noT + 1;

     // Affichage détail ticket par ticket
     //echo "<tr><td><th>$noT</th></td><td><th>$idTicket</th></td>".
     //     "<td><th>$idUser</th></td><td><th>$Dep</th></td></tr>";



         }
       }

//   echo "TF $NbTF - AS2M $NbAS2M - MEC $NbMEC - MN2S $NbMN2S - OPT $NbOPT - DISC $NbDISC - COMMUN $NbCOMMUN - ENERGIE $NbENERGIE - DMA $NbDMA - MECAPPLI $NbMECAPPLI - INTERNES $NbCOMMInt - Externes $NbCOMMExt - Tutelles $NbCOMMTut <br>";


   $NbTotal = $NbTF + $NbAS2M + $NbMEC + $NbMN2S + $NbOPT + $NbDISC + $NbCOMMUN + $NbENERGIE + $NbCOMMExt + $NbCOMMTut ;

     $PourCentTF = number_format(($NbTF / $nbTickets ) * 100, 2);
     $PourCentAS2M = number_format(($NbAS2M / $nbTickets ) * 100, 2);
     $PourCentMEC = number_format(($NbMEC / $nbTickets ) * 100, 2);
     $PourCentMN2S = number_format(($NbMN2S / $nbTickets ) * 100, 2);
     $PourCentOPT = number_format(($NbOPT / $nbTickets ) * 100, 2);
     $PourCentDISC = number_format(($NbDISC / $nbTickets ) * 100, 2);
     $PourCentCOMMUN = number_format(($NbCOMMUN / $nbTickets ) * 100, 2);
     $PourCentENERGIE = number_format(($NbENERGIE / $nbTickets ) * 100, 2);
     $PourCentDMA = number_format(($NbDMA / $nbTickets ) * 100, 2);
     $PourCentMECAPPLI = number_format(($NbMECAPPLI / $nbTickets ) * 100, 2);
     $PourCentCOMMInt = number_format(($NbCOMMInt / $nbTickets ) * 100, 2);
     $PourCentCOMMExt = number_format(($NbCOMMExt / $nbTickets ) * 100, 2);
     $PourCentCOMMTut = number_format(($NbCOMMTut / $nbTickets ) * 100, 2);


     $PourCentTotal = $PourCentTF + $PourCentAS2M + $PourCentMEC + $PourCentMN2S + $PourCentOPT + $PourCentDISC + $PourCentCOMMUN + $PourCentENERGIE + $PourCentMECAPPLI + $PourCentCOMMExt + $PourCentCOMMTut ;

     echo "<tr><td><th>TF</th></td><td><th>$NbTF</th></td>".
          "<td><th>$PourCentTF</th></td></tr>".
          "<tr><td><th>AS2M</th></td><td><th>$NbAS2M</th></td>".
          "<td><th>$PourCentAS2M</th></td></tr>".
          "<tr><td><th>M&eacute;canique Appliqu&eacute;e</th></td><td><th>$NbMEC</th></td>".
          "<td><th>$PourCentMEC</th></td></tr>".
          "<tr><td><th>MN2S</th></td><td><th>$NbMN2S</th></td>".
          "<td><th>$PourCentMN2S</th></td></tr>".
          "<tr><td><th>OPTIQUE</th></td><td><th>$NbOPT</th></td>".
          "<td><th>$PourCentOPT</th></td></tr>".
          "<tr><td><th>DISC</th></td><td><th>$NbDISC</th></td>".
          "<td><th>$PourCentDISC</th></td></tr>".
          "<tr><td><th>COMMUN</th></td><td><th>$NbCOMMUN</th></td>".
          "<td><th>$PourCentCOMMUN</th></td></tr>".
          "<tr><td><th>ENERGIE</th></td><td><th>$NbENERGIE</th></td>".
          "<td><th>$PourCentENERGIE</th></td></tr>".
          "<tr><td><th>Utilisateurs externes</th></td><td><th>$NbCOMMExt</th></td>".
          "<td><th>$PourCentCOMMExt</th></td></tr>".
          "<tr><td><th>Utilisateurs tutelles</th></td><td><th>$NbCOMMTut</th></td>".
          "<td><th>$PourCentCOMMTut</th>></td></tr>".
          "<tr><td><th>TOTAL</th></td><td><th>$NbTotal</th></td>".
          "<td><th>$PourCentTotal</th>></td></tr>";

      echo "</table>";

