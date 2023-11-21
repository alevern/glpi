<?php
/*
 * listeTickets.php 2013-03 L STECK SCI
 -------------------------------------------------------------------------
 reports - Additional reports plugin for GLPI
 Copyright (C) 2003-2011 by the reports Development Team.

 https://forge.indepnet.net/projects/reports
 -------------------------------------------------------------------------
 --------------------------------------------------------------------------
 */


$NEEDED_ITEMS = array("search");
$USEDBREPLICATE = 1;
$DBCONNECTION_REQUIRED = 0;

define('GLPI_ROOT', '../../../..');
include (GLPI_ROOT . "/inc/includes.php");

// initialise le nouveau rapport
$report = new PluginReportsAutoReport('listeTickets');

//echo "<br><br><b>Statuts</> : 1: Nouveau - 2 En cours attribu&eacute; - 3 En cours planifi&eacute; - 4 En attente - 5 R&eacute;solu - 6 Clos </b><br><br>";
//dÃfinit la date de crÃ©ation du ticket comme critÃ¨re de recherc sous forme d'intervalle de date
$date = new PluginReportsDateIntervalCriteria($report, "date");

$report->displayCriteriasForm();



if ($report->criteriasValidated()) {

   $report->setColumns(array( 'no' => "No", 'categorie' => 'Catégorie', 'demandeur' => 'Demandeur', 'technicien' => 'Technicien', 'statut' => 'Statut', 'ouvert' => 'Ouvert', 'echeance' => 'Date &eacute;ch&eacute;ance', 'ferme' => 'Ferm&eacute;', 'delaiPriseEnCompte' => 'D&eacute;lai prise en compte', 'delaifermeture' => 'D&eacute;lai fermeture')); 

   $query= "select glpi_tickets.id as 'no', glpi_tickets.itilcategories_id, glpi_itilcategories.completename as 'categorie', glpi_tickets.itilcategories_id, CONCAT_WS(' ', A.realname, A.firstname) as 'demandeur', 
           CONCAT_WS(' ', B.realname, B.firstname) as technicien, status as statut, DATE_FORMAT(glpi_tickets.date, '%d/%m/%Y %H:%i:%s') as ouvert, 
            DATE_FORMAT(glpi_tickets.time_to_resolve, '%d/%m/%Y %H:%i:%s') as echeance, DATE_FORMAT(glpi_tickets.closedate, '%d/%m/%Y %H:%i:%s') as ferme,
            SEC_TO_TIME(glpi_tickets.takeintoaccount_delay_stat) as delaiPriseEnCompte, SEC_TO_TIME(glpi_tickets.solve_delay_stat) as delaiResolution, SEC_TO_TIME(glpi_tickets.close_delay_stat) as delaifermeture, SEC_TO_TIME(actiontime) as tempsTraitement  
               from glpi_tickets, glpi_itilcategories, glpi_users A, glpi_users B ".
            $report->addSqlCriteriasRestriction("WHERE").
	     "and glpi_tickets.entities_id = 2 ".
             " and (A.realname, A.firstname)  in
              (select realname, firstname from glpi_users where glpi_users.id in 
                 (select users_id from glpi_tickets_users  
                   where tickets_id=glpi_tickets.id and type=1)) 
           and (B.realname, B.firstname) in 
                (select realname,firstname from glpi_users where id in 
                     (SELECT users_id FROM `glpi_tickets_users` WHERE tickets_id = glpi_tickets.id AND TYPE =2))
           and glpi_itilcategories.id = glpi_tickets.itilcategories_id
               ORDER BY glpi_tickets.id ";


#echo $query;

   $report->setSqlRequest($query);
   $report->execute();
echo "<br><br><b>Statuts</> : 1: Nouveau - 2 En cours attribu&eacute; - 3 En cours planifi&eacute; - 4 En attente - 5 R&eacute;solu - 6 Clos </b><br><br>";
   }
?>
