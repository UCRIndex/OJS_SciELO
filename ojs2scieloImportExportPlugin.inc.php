<?php

/**
 * @file plugins/importexport/ojs2scielo/Ojs2scieloImportExportPlugin.inc.php
 *
 * Copyright (c) 2013-2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class Ojs2ScieloImportExportPlugin
 * @ingroup plugins_importexport_native,
 *
 * @brief import/export plugin
 * Edited by Vicerrectoría de investigación - Universidad de Costa Rica.
 */

import('classes.plugins.ImportExportPlugin');

class Ojs2ScieloImportExportPlugin extends ImportExportPlugin {

	/* 
	 * Called as a plugin is registered to the registry.
	 * @param $category String Name of category plugin was registered to.
	 * @return boolean True if plugin initialized successfully; if false, the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		$this->addLocaleData();
		return $success;
	}
	
	/*
	 * Displays the command-line usage information.
	 */
	function usage($scriptName) {
		echo __('plugins.importexport.ojs2scielo.cliUsage', array(
			'scriptName' => $scriptName,
			'pluginName' => $this->getName()
		)) . "\n";
	}

	/*
	 * States the name of the plugin. This name must be unique in its category.
	 * @return String name of the plugin.
	 */
	function getName() {
		return 'ojs2scieloImportExportPlugin';
	}

	/*
	 * This method returns the name of the plugin that is shown at the import/export page. This name is found at the 
	 * locale corresponding file.
	 * @return String name of the plugin.
	 */
	function getDisplayName() {
		return __('plugins.importexport.ojs2scielo.displayName');
	}

	/*
	 * This method returns the description of the plugin that is shown at the import/export page. This description is
	 * found at the locale corresponding file.
	 * @return String description of the plugin.
	 */
	function getDescription() {
		return __('plugins.importexport.ojs2scielo.description');
	}
	
	/*
	 * This method parses a specific file with the XMLParser already implemented at OJS. This would be helpful when
	 * the switch structure is completed.
	 * @return XML Parsed document.
	 */
	function &getDocument($fileName) {
		$parser = new XMLParser();
		$returner =& $parser->parse($fileName);
		return $returner;
	}

	/*
	 * Returns the name of the root node (this root refers to the main note at the XML document). This would be
	 * helpful when the switch structure is completed.
	 * @return String name of the main node.
	 */
	function getRootNodeName(&$doc) {
		return $doc->name;
	}
	
	/*
	 * This method contains the functionality of the plugin. For now, the switch structure comprises just the 'export
	 * article' option. Later, it should be added the options for the management of issues ('exportIssues',
	 * 'exportIssue', 'issues') as well as the option for import. At this moment, the issue options should behave
	 * as the 'default' option.
	 */
	function display(&$args) {
		$this->import('ojs2scieloExportDom');
		$templateMgr =& TemplateManager::getManager();
		parent::display($args);
		$issueDao =& DAORegistry::getDAO('IssueDAO');
		$journal =& Request::getJournal();
		switch (array_shift($args)) {
			case 'exportArticle':
				$articleIds = array(array_shift($args));
				$result = array_shift(ArticleSearch::formatResults($articleIds));
				Ojs2ScieloExportDom::exportArticle($journal, $result['issue'], $result['section'], $result['publishedArticle']);
				break;
			case 'articles':
				// This option shows the list of articles of each journal.
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
				// The default option restores the page due to an invalid selection.
				$this->setBreadcrumbs();
				$templateMgr->display($this->getTemplatePath() . 'index.tpl');
		}
	}
}

?>
