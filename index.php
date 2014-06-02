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
 * (Tomado del plugin "native". Se realizaron modificaciones para exportar a SciELO)
 */

require_once('ojs2scieloImportExportPlugin.inc.php');

return new Ojs2ScieloImportExportPlugin();



?>
