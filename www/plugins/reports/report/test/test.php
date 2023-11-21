<?php

$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;

include ("../../../../inc/includes.php");

global $DB;
//TRANS: The name of the report = Financial information
$report = new PluginReportsAutoReport(__('test', 'reports'));

$date = new PluginReportsDateIntervalCriteria($report, '`glpi_infocoms`.`buy_date`',
                                              __('Date of purchase'));

//Display criterias form is needed
$report->displayCriteriasForm();

//If criterias have been validated
if ($report->criteriasValidated()) {

} else {
   Html::footer();
}
