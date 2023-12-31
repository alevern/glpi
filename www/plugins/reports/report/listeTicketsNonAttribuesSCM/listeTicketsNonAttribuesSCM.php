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
$DBCONNECTION_REQUIRED = 1;

define('GLPI_ROOT', '../../../..');
include (GLPI_ROOT . "/inc/includes.php");

// initialise le nouveau rapport
$report = new PluginReportsAutoReport('listeTickets');

//d�finit la date de création du ticket comme critère de recherc sous forme d'intervalle de date
$date = new PluginReportsDateIntervalCriteria($report, "date");

$report->displayCriteriasForm();


if ($report->criteriasValidated()) {


   $report->setColumns(array( 'id' => "Ticket", 'categorie' => 'Cat�gorie', 'demandeur' => 'Demandeur', 'technicien' => "Intervenant", 
        'statut' => "Statut", 'ouvert' => "Ouvert", 'echeance' => "Echéance", 'ferme' => "Fermé",
        'delaiPriseEnCompte' => "Délai prise en compte", 'delaiResolution' => "D�lai de r�solution", 'delaifermeture' => "Délai fermeture", 'tempsTraitement' => "Temps traitement" ) ); 

   $query= "select glpi_tickets.id,  glpi_tickets.itilcategories_id, glpi_itilcategories.completename as 'categorie', glpi_tickets.itilcategories_id, CONCAT_WS(' ', realname, firstname) as 'demandeur', 
            status as statut, DATE_FORMAT(glpi_tickets.date, '%d/%m/%Y %H:%i:%s') as ouvert, 
            DATE_FORMAT(glpi_tickets.due_date, '%d/%m/%Y %H:%i:%s') as echeance, DATE_FORMAT(glpi_tickets.closedate, '%d/%m/%Y %H:%i:%s') as ferme,
            SEC_TO_TIME(glpi_tickets.takeintoaccount_delay_stat) as delaiPriseEnCompte, SEC_TO_TIME(glpi_tickets.solve_delay_stat) as delaiResolution, SEC_TO_TIME(glpi_tickets.close_delay_stat) as delaifermeture, SEC_TO_TIME(actiontime) as tempsTraitement  
               from glpi_tickets,  glpi_itilcategories, glpi_users ".
            $report->addSqlCriteriasRestriction("WHERE").
            " ans glpi_tickets.entities_id = 2i ".
            "  and glpi_tickets.id in 
                (select tickets_id from glpi_tickets_users where tickets_id not in 
                    (select tickets_id from glpi_tickets_users where type = 2))  
             and (realname, firstname)  in
              (select realname, firstname from glpi_users where glpi_users.id in 
                 (select users_id from glpi_tickets_users  
                   where tickets_id=glpi_tickets.id and type=1)) 
             and glpi_itilcategories.id = glpi_tickets.itilcategories_id
             ORDER BY glpi_tickets.id ASC ";

//echo $query;

   $report->setSqlRequest($query);
   $report->execute();
   }
?>


