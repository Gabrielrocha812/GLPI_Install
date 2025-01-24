<?php

define('PLUGIN_JC_CUSTOM_LOGIN_VERSION', '1.0.3');

$folder = basename(dirname(__FILE__));

if ($folder !== "glpijccustomlogin") {
   $msg = sprintf("Por favor, renomeie a pasta dso plugin de \"%s\" para \"glpijccustomlogin\"", $folder);
   Session::addMessageAfterRedirect($msg, true, ERROR);
}

function plugin_init_glpijccustomlogin() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['glpijccustomlogin'] = true;
   $PLUGIN_HOOKS['display_login']['glpijccustomlogin'] = "plugin_glpijccustomlogin_display_login";

   Plugin::registerClass(
      'PluginGlpijccustomloginConfig', [
         'addtabon' => [
            'Config'
         ]
      ]
   );
}

function plugin_version_glpijccustomlogin() {
   return [
      'name'           => 'JC Custom Login',
      'version'        =>  PLUGIN_JC_CUSTOM_LOGIN_VERSION,
      'author'         => 'Service TIC <a href="https://www.servicetic.com.br"></a>',
      'license'        => 'GLPv3',
      'homepage'       => 'https://www.servicetic.com.br',
      'requirements'   => [
        'glpi'   => [
           'min' => '9.5.11',
        ],
        'php'    => [
           'min' => '7.0'
        ]
     ]
   ];
}

function plugin_glpijccustomlogin_check_prerequisites() {
   return true;
}

function plugin_glpijccustomlogin_check_config($verbose = false) {
   return true;
}

function plugin_glpijccustomlogin_options() {
   return [
      Plugin::OPTION_AUTOINSTALL_DISABLED => true,
   ];
}