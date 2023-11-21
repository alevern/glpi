<?php
/**
 * @version $Id: columntypelink.class.php 393 2021-03-11 13:36:35Z yllen $
 -------------------------------------------------------------------------
  LICENSE

 This file is part of Reports plugin for GLPI.

 Reports is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Reports is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with Reports. If not, see <http://www.gnu.org/licenses/>.

 @package   reports
 @authors    Nelly Mahu-Lasson, Remi Collet, Alexandre Delaunay
 @copyright Copyright (c) 2009-2021 Reports plugin team
 @license   AGPL License 3.0 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 @link      https://forge.glpi-project.org/projects/reports
 @link      http://www.glpi-project.org/
 @since     2009
 --------------------------------------------------------------------------
 */

/**
 * class PluginReportsColumn to manage output
 */
class PluginReportsColumnTypeLink extends PluginReportsColumn {

   private $obj          = NULL;
   private $with_comment = 0;
   private $nametype     = '';


   function __construct($nameid, $title, $nametype, $options=[]) {

      parent::__construct($nameid, $title, $options);

      $this->nametype = $nametype;

      if (isset($options['with_comment'])) {
         $this->with_comment = $options['with_comment'];
      }
   }


   function displayValue($output_type, $row) {

      $dbu = new DbUtils();

      if (!isset($row[$this->name]) || !$row[$this->name]) {
         return '';
      }
      if (isset($row[$this->nametype])
          && $row[$this->nametype]
          && (is_null($this->obj) || $this->obj->getType()!=$row[$this->nametype])) {

         if (!($this->obj = $dbu->getItemForItemtype($row[$this->nametype]))) {
            $this->obj = NULL;
         }
      }

      if (!$this->obj || !$this->obj->getFromDB($row[$this->name])) {
         return 'ID #'.$row[$this->name];
      }

      if ($output_type == Search::HTML_OUTPUT) {
         return $this->obj->getLink($this->with_comment);
      }

      return $this->obj->getNameID();
   }
}