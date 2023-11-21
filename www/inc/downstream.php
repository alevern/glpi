<?php
define('GLPI_CONFIG_DIR', '/etc/tickets/');

if (file_exists(GLPI_CONFIG_DIR . '/local_define.php')) {
   require_once GLPI_CONFIG_DIR . '/local_define.php';
}
?>
