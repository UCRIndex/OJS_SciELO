<?php

/**
 * @defgroup plugins_importexport_nativem
 */
 
/**
 * @file plugins/importexport/nativem/index.php
 *
 * Copyright (c) 2013-2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_importexport_nativem
 * @brief Wrapper for native XML import/export plugin.
 * (Tomado del plugin "native". Se realizaron modificaciones para exportar a SciELO)
 */

require_once('NativeImportExportPluginM.inc.php');

return new NativeImportExportPluginM();



?>
