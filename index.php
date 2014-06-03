<?php

/**
 * @defgroup plugins_importexport_ojs2scielo
 */
 
/**
 * @file plugins/importexport/ojs2scielo/index.php
 *
 * Copyright (c) 2013-2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_importexport_ojs2scielo
 * @brief Wrapper for native XML import/export plugin.
 * Edited by Vicerrectoría de investigación - Universidad de Costa Rica.
 */

//Se busca en el directorio del plugin al archivo ojs2scieloImportExportPlugin.inc.php
require_once('ojs2scieloImportExportPlugin.inc.php');

//La funcionalidad del plugin inicia con la invocaci�n al constructor de la clase Ojs2ScieloImportExportPlugin
return new Ojs2ScieloImportExportPlugin();



?>
