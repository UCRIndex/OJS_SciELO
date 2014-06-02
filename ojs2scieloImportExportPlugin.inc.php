<?php

/**
 * @file plugins/importexport/ojs2scielo/ojs2scieloImportExportPlugin.inc.php
 *
 * Copyright (c) 2013-2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class Ojs2ScieloImportExportPlugin
 * @ingroup plugins_importexport_native,
 *
 * @brief import/export plugin
 * (Tomado del plugin "native". Se realizaron modificaciones para exportar a SciELO)
 */

class Ojs2ScieloImportExportPlugin extends ImportExportPlugin {

	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		$this->addLocaleData();
		return $success;
	}
	
	/**
	 * Display the command-line usage information
	 */
	function usage($scriptName) {
		echo __('plugins.importexport.ojs2scielo.cliUsage', array(
			'scriptName' => $scriptName,
			'pluginName' => $this->getName()
		)) . "\n";
	}

	/**
	 * Obtiene el nombre del plugin.
	 * Este nombre debe ser único dentro de su categoría
	 * @return String nombre del plugin
	 */
	function getName() {
		return 'ojs2scieloImportExportPlugin';
	}

	/**
	 * Obtiene el nombre que se despliega en la página de Importar/exportar datos
	 * Dicho nombre se obtiene del archivo locale correspondiente, buscado con la clave entre paréntesis
	 * @return String nombre del plugin
	 * */
	function getDisplayName() {
		return __('plugins.importexport.ojs2scielo.displayName');
	}

	/**
	 * Obtiene la descripción que se despliega en la página de Importar/exportar datos
	 * Dicha descripción se obtiene del archivo locale correspondiente, buscado con la clave entre paréntesis
	 * @return String nombre del plugin
	 * */
	function getDescription() {
		return __('plugins.importexport.ojs2scielo.description');
	}
	
	function &getDocument($fileName) {
		$parser = new XMLParser();
		$returner =& $parser->parse($fileName);
		return $returner;
	}

	function getRootNodeName(&$doc) {
		return $doc->name;
	}
	
	/*
	 * Función encargada de mostrar el listado de opciones del plugin.
	 * Desde acá se pueden exportar números completos y artículos mediante la clase Ojs2ScieloExportDom
	 */
	function display(&$args) {

		$this->import('Ojs2scieloExportDom');
		$templateMgr =& TemplateManager::getManager();
		parent::display($args);
		$issueDao =& DAORegistry::getDAO('IssueDAO');
		$journal =& Request::getJournal();
		// Opciones para exportar.
		switch (array_shift($args)) {
			case 'exportArticle':
				$articleIds = array(array_shift($args));
				$result = array_shift(ArticleSearch::formatResults($articleIds));
				Ojs2ScieloExportDom::exportArticle($journal, $result['issue'], $result['section'], $result['publishedArticle']);
				break;
			case 'articles':
				// Muestra la lista de los artículos de cada revista
				$this->setBreadcrumbs(array(), true);
				$publishedArticleDao =& DAORegistry::getDAO('PublishedArticleDAO');
				$rangeInfo = Handler::getRangeInfo('articles');
				$articleIds = $publishedArticleDao->getPublishedArticleIdsAlphabetizedByJournal($journal->getId(), false);
				$totalArticles = count($articleIds);
				if ($rangeInfo->isValid()) $articleIds = array_slice($articleIds, $rangeInfo->getCount() * ($rangeInfo->getPage()-1), $rangeInfo->getCount());
				import('lib.pkp.classes.core.VirtualArrayIterator');
				$iterator = new VirtualArrayIterator(ArticleSearch::formatResults($articleIds), $totalArticles, $rangeInfo->getPage(), $rangeInfo->getCount());
				$templateMgr->assign_by_ref('articles', $iterator);
				$templateMgr->display($this->getTemplatePath() . 'articles.tpl');
				break;	
			default:
				$this->setBreadcrumbs();
				$templateMgr->display($this->getTemplatePath() . 'index.tpl');
		}
	}	
}

?>
