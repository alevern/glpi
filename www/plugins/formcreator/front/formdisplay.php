<?php
/**
 * ---------------------------------------------------------------------
 * Formcreator is a plugin which allows creation of custom forms of
 * easy access.
 * ---------------------------------------------------------------------
 * LICENSE
 *
 * This file is part of Formcreator.
 *
 * Formcreator is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Formcreator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Formcreator. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 * @copyright Copyright © 2011 - 2021 Teclib'
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @link      https://github.com/pluginsGLPI/formcreator/
 * @link      https://pluginsglpi.github.io/formcreator/
 * @link      http://plugins.glpi-project.org/#/plugin/formcreator
 * ---------------------------------------------------------------------
 */

global $CFG_GLPI, $DB;
include ('../../../inc/includes.php');

// Check if plugin is activated...
if (!(new Plugin())->isActivated('formcreator')) {
   PluginFormcreatorForm::header();
   Html::displayNotFoundError();
}

##FEMTO-ST 2020-11-20 L STECK ## DEBUT ## Prise en compte de l'entité SCEI

## LS 26112020 prise en compte de l'entite
// Manage entity change
if (isset($_GET["active_entity"])) {
   $_GET["active_entity"] = rtrim($_GET["active_entity"], 'r');
   if (!isset($_GET["is_recursive"])) {
      $_GET["is_recursive"] = 0;
   }


   //FEMTO-ST 2018-11-07 Tristan Cardot ## INLINE ## modification du comportement de la redirection.
   if (!Session::changeActiveEntities($_GET["active_entity"], $_GET["is_recursive"])) {
      if ($_GET["active_entity"]!== $_SESSION["glpiactive_entity"]
         && isset($_SERVER['HTTP_REFERER'])) {
// Spécifique FEMTO-ST
// LS 23102017 on ne passe pas à la page parent

            Html::redirect(preg_replace("/(\?|&|".urlencode('?')."|".urlencode('&').")?(entities_id|active_entity).*/", "", $_SERVER['HTTP_REFERER']));
// Fin Spécifique FEMTO-ST
      }
   }
}

##FEMTO-ST 2020-11-20 L STECK ## FIN ## Prise en compte de l'entité SCEI



PluginFormcreatorForm::header();

if (isset($_REQUEST['id'])
   && is_numeric($_REQUEST['id'])) {

   $criteria = [
      'id'        => (int) $_REQUEST['id'],
      'is_active' => '1',
      'is_deleted'=> '0',
   ];
   $form = new PluginFormcreatorForm();
   if (!$form->getFromDBByCrit($criteria)) {
      Html::displayNotFoundError();
   }

   if ($form->fields['access_rights'] != PluginFormcreatorForm::ACCESS_PUBLIC) {
      Session::checkLoginUser();
      if (!$form->checkEntity(true)) {
         Html::displayRightError();
         exit();
      }
   }

   if ($form->fields['access_rights'] == PluginFormcreatorForm::ACCESS_RESTRICTED) {
      $iterator = $DB->request(PluginFormcreatorForm_Profile::getTable(), [
         'WHERE' => [
            'profiles_id'                 => $_SESSION['glpiactiveprofile']['id'],
            'plugin_formcreator_forms_id' => $form->getID()
         ],
         'LIMIT' => 1
      ]);
      if (count($iterator) == 0) {
         Html::displayRightError();
         exit();
      }
   }
   if (($form->fields['access_rights'] == PluginFormcreatorForm::ACCESS_PUBLIC) && (!isset($_SESSION['glpiID']))) {
      // If user is not authenticated, create temporary user
      if (!isset($_SESSION['glpiname'])) {
         $_SESSION['formcreator_forms_id'] = $form->getID();
         $_SESSION['glpiname'] = 'formcreator_temp_user';
         $_SESSION['valid_id'] = session_id();
         $_SESSION['glpiactiveentities'] = [$form->fields['entities_id']];
         $subentities = getSonsOf('glpi_entities', $form->fields['entities_id']);
         $_SESSION['glpiactiveentities_string'] = (!empty($subentities))
                                                ? "'" . implode("', '", $subentities) . "'"
                                                : "'" . $form->fields['entities_id'] . "'";
         $_SESSION['glpilanguage'] = $form->getBestLanguage();
      }
   }

   $form->displayUserForm();

   // If user was not authenticated, remove temporary user
   if ($_SESSION['glpiname'] == 'formcreator_temp_user') {
      session_write_close();
      unset($_SESSION['glpiname']);
   }
} else if (isset($_GET['answer_saved'])) {
   $message = __("The form has been successfully saved!");
   Html::displayTitle($CFG_GLPI['root_doc']."/pics/ok.png", $message, $message);
}

PluginFormcreatorForm::footer();
