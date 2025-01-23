<?php

define('GLPI_ROOT', '../../..');
require_once(GLPI_ROOT . '/inc/includes.php');

// Check if plugin is activated...
$plugin = new Plugin();
if (!$plugin->isInstalled('glpijccustomlogin') || !$plugin->isActivated('glpijccustomlogin')) {
   return false;
}

if (!empty($_GET['img_path'])) {
   $pluginPath = (GLPI_PLUGIN_DOC_DIR . DIRECTORY_SEPARATOR . "glpijccustomlogin");
   $name = preg_replace('/[^\w\-.]+/', '', $_GET['img_path']);

   Toolbox::sendFile($pluginPath . DIRECTORY_SEPARATOR . $_GET['img_path'], $name, null, true);

   return;
}

if (!empty($_GET['img_dev'])) {
   $imgPath = GLPI_PLUGIN_DOC_DIR . 
      DIRECTORY_SEPARATOR .
      'glpijccustomlogin' . 
      DIRECTORY_SEPARATOR . 
      'dev_background.png';
   
   $name = preg_replace('/[^\w\-.]+/', '', 'dev_background.png');
   Toolbox::sendFile($imgPath, $name, null, true);

   return;
}

