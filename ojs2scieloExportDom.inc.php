<?php

/**
 * @file plugins/importexport/ojs2scielo/Ojs2scieloExportDom.inc.php
 *
 * Copyright (c) 2013-2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class Ojs2ScieloExportDom
 * @ingroup plugins_importexport_native
 *
 * @brief import/export plugin DOM functions for export
 * Edited by Vicerrectoría de investigación - Universidad de Costa Rica.
 */

import('lib.pkp.classes.xml.XMLCustomWriter');

define('NATIVE_DTD_URL', 'http://pkp.sfu.ca/ojs/dtds/2.3/native.dtd');
define('NATIVE_DTD_ID', '-//PKP//OJS Articles and Issues XML//EN');

/*
 * This class exports an article to a XML document using the XMLCustomWriter class already implemented (for more information about
 * the creation of nodes and attributes: http://pkp.sfu.ca/ojs/doxygen/stable/html/XMLCustomWriter_8inc_8php_source.html).
 * Note: each string that contains 'add' plus the attibute (node name) must be replace with the corresponding information.
 */
class Ojs2ScieloExportDom extends ImportExportPlugin {
	/*
	 * exportArticle creates the XML document, adds the front node to the document (with its children), adds the body
	 * node to the document (with its children), adds the back node to the document (with its children) and finally
	 * exports the XML.
	 * This method gets five parameters that contain the corresponding journal, issue, section, and article (objects) chosen
	 * by the user.
	 * $journal refers to the journal that contains the selected article (object).
	 * $issue refers to the journal issue (object).
	 * $section refers to the section of the journal (object).
	 * $article refers to the selected article (object),
	 * $outputFile refers to the output type.
	 */
function exportArticle(&$journal, &$issue, &$section, &$article, $outputFile = null) {	
		// For the creation of the document the function needs the DTD and its URL (root).
		$doc =& XMLCustomWriter::createDocument('article', NATIVE_DTD_ID, NATIVE_DTD_URL);

		// This is the url that the header should have in its attributes (Request::url(null, 'issue', 'view', $issue->getBestIssueId($journal))).
		// However, there is a compilation error when this function is loaded from here. Stil unfixed.
		
		// Header node and its attributes (first node of the document).
		$header =& XMLCustomWriter::createElement($doc, 'article');
		XMLCustomWriter::setAttribute($header, 'xmlns:xlink', 'addurl');
		XMLCustomWriter::setAttribute($header, 'xmlns:mml', 'addmml');
		XMLCustomWriter::setAttribute($header, 'dtd-version', '3.0');
		XMLCustomWriter::setAttribute($header, 'article-type', 'addtype');
		XMLCustomWriter::setAttribute($header, 'xml:lang', $article->getLanguage());
		XMLCustomWriter::appendChild($doc, $header);
		
		// Adds the front node to the document (with its children).
		Ojs2ScieloExportDom::addFrontNode($journal, $article, $doc, $header, $issue);
		
		// Adds the body node to the document (with its children).
		Ojs2ScieloExportDom::addBodyNode($article, $doc, $header, $issue);
		
		// Adds the back node to the document (with its children).
		Ojs2ScieloExportDom::addBackNode($article, $doc, $header);
		
		// Exports the final file ready for download.
		Ojs2ScieloExportDom::exportXML($article, $doc, $outputFile);
	}

	/*
	 * Prepares the structure for the "front" node. Each node is created and the attributes are attached to its
	 * corresponding node.
	 * @$journal contains the journal data of the article (object).
	 * @$article contains the article chosen by the user (object).
	 * @$doc contains the XML document created by the XMLCustomWriter class (XML document).
	 * @$header contains the first node of the document (XML node).
	 */
	private function addFrontNode(&$journal, &$article, &$doc, &$header, &$issue) {
		$frontNode =& XMLCustomWriter::createElement($doc, 'front'); // Front node.
		XMLCustomWriter::appendChild($header, $frontNode);
		
		Ojs2ScieloExportDom::addJournalMeta($doc, $frontNode, $journal, $article); // Attaches the Journal-meta group of nodes to the XML document.
		
		$articleMetaNode =& XMLCustomWriter::createElement($doc, 'article-meta'); // Article-meta node.
		XMLCustomWriter::appendChild($frontNode, $articleMetaNode);
		
		$publisherIDNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'article-id', $article->getPublishedArticleId(), false); // Article-id (publisher-id) node.
		XMLCustomWriter::setAttribute($publisherIDNode, 'pub-id-type', 'publisher-id');
		
		$publisherIDNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'article-id', 'adddoi', false); // Article-id (doi) node.
		XMLCustomWriter::setAttribute($publisherIDNode, 'pub-id-type', 'doi');
		
		$articleCategoriesNode =& XMLCustomWriter::createElement($doc, 'article-categories'); // Article-categories node.
		XMLCustomWriter::appendChild($articleMetaNode, $articleCategoriesNode);
		
		$subjectGroupNode =& XMLCustomWriter::createElement($doc, 'subj-group'); // Subj-group node.
		XMLCustomWriter::setAttribute($subjectGroupNode, 'subj-group-type', 'heading');
		XMLCustomWriter::appendChild($articleCategoriesNode, $subjectGroupNode);
		
		$subjectNode =& XMLCustomWriter::createChildWithText($doc, $subjectGroupNode, 'subject', 'addsubject', false); // Subject node.
		
		$titleGroupNode =& XMLCustomWriter::createElement($doc, 'title-group'); // Title-group node.
		XMLCustomWriter::appendChild($articleMetaNode, $titleGroupNode);
		
		Ojs2ScieloExportDom::getArticleTitle($doc, $article, $titleGroupNode); // Attaches the Article-title group of nodes to the XML document.
		
		$contribGroupNode =& XMLCustomWriter::createElement($doc, 'contrib-group'); // Contrib-group node(s).
		XMLCustomWriter::appendChild($articleMetaNode, $contribGroupNode);
		
		Ojs2ScieloExportDom::getArticleAuthors($doc, $article, $contribGroupNode); // Attaches the Authors group of nodes to the XML document.
		
		Ojs2ScieloExportDom::getAuthorsAffiliation($doc, $article, $articleMetaNode); // Attaches the Affiliation group of nodes to the XML document.
		
		$authorNotesNode =& XMLCustomWriter::createElement($doc, 'author-notes'); // Author-notes node.
		XMLCustomWriter::appendChild($articleMetaNode, $authorNotesNode);
		
		$correspNode =& XMLCustomWriter::createElement($doc, 'corresp'); // Corresp node.
		XMLCustomWriter::appendChild($authorNotesNode, $correspNode);
		
		$boldNode =& XMLCustomWriter::createChildWithText($doc, $correspNode, 'bold', 'Correspondence', false); // Bold node.
		
		$personalEmailNode =& XMLCustomWriter::createChildWithText($doc, $correspNode, 'email', 'addemail', false); // Personal email node.
		
		Ojs2ScieloExportDom::addPubDate($doc, $articleMetaNode, $article); // Attaches the Pub-date group of nodes to the XML document.
		
		$volumeNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'volume', $issue->getVolume(), false); // Volume node.
		
		$issueNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'issue', $issue->getNumber(), false); // Issue node.
		
		$fPageNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'fpage', 'addfpage', false); // Fpage node.
		
		$lPageNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'lpage', 'addlpage', false); // Lpage node.
		
		Ojs2ScieloExportDom::addHistory($doc, $articleMetaNode, $issue); // Attaches the History group of nodes to the XML document.
		
		Ojs2ScieloExportDom::addPermissions($doc, $articleMetaNode); // Attaches the Permissions group of nodes to the XML document.

		Ojs2ScieloExportDom::addAbstract($doc, $article, $articleMetaNode); // Attaches the Abstract group of nodes to the XML document.
		
		// Inside the abstract node, there are the "title" and "paragraph" nodes. These nodes do not appear in
		// this schema because they depend on the content of the abstact. However, they must be attached in
		// the next release. Likewise, there is a "translation" node that must be taken into account.
		
		$kwdGroupNode =& XMLCustomWriter::createElement($doc, 'kwd-group'); // Kwd-group node.
		XMLCustomWriter::setAttribute($kwdGroupNode, 'xml:lang', 'addlanguage');
		XMLCustomWriter::appendChild($articleMetaNode, $kwdGroupNode);
		
		// For each key word, the document must create a new node. There is also an optional "translation key word" group of
		// group of nodes. This group is similar to the "kwd-group", with the exception that it contains the translation language initials
		// as an attribute of the node.
		
		Ojs2ScieloExportDom::addFundingGroup($doc, $articleMetaNode, $article); // Attaches the Funding-group group of nodes to the XML document.
		
		Ojs2ScieloExportDom::addCounts($doc, $articleMetaNode); // Attaches the Counts group of nodes to the XML document.
	}
	
	/*
	 * This method adds the body node to the XML document. For now, it just contains the basic node.
	 * Here, the nodes are tied to the content of the document, so there must be a strategy to extract the body from the document and
	 * transform these sections into nodes (paragraphs, titles, tables, etc.).
	 */
	private function addBodyNode(&$article, &$doc, &$header, &$issue) {
		$bodyNode =& XMLCustomWriter::createElement($doc, 'body'); // Body node.
		XMLCustomWriter::appendChild($header, $bodyNode);
		$articlesExtrasDao =& new ArticlesExtrasDAO();
		$body = $articlesExtrasDao->getArticleBody($article->getArticleId());
		$textNode =& XMLCustomWriter::createChildWithText($doc, $bodyNode, 'body', $body, false);
		//$zip = $articlesExtrasDao->getMetadataByArticleId($article->getArticleId(), 'aff_zipcode');
		//$textNode =& XMLCustomWriter::createChildWithText($doc, $bodyNode, 'zip', '$zip', false);
	}
	
	/*
	 * addBackNode creates the structure for the "back" node. For now, it just has the basic requirements, but later some of these
	 * groups of nodes must be replicated; for instance: "ref-list". This is directly related to the content of the documents.
	 */
	private function addBackNode(&$article, &$doc, &$header) {
		$backNode =& XMLCustomWriter::createElement($doc, 'back'); // Back node.
		XMLCustomWriter::appendChild($header, $backNode);
		
		$ackNode =& XMLCustomWriter::createElement($doc, 'ack'); // Ack node.
		XMLCustomWriter::appendChild($backNode, $ackNode);
		
		$secAckNode =& XMLCustomWriter::createElement($doc, 'sec'); // Sec node.
		XMLCustomWriter::appendChild($ackNode, $secAckNode);
		
		$titleSecNode =& XMLCustomWriter::createChildWithText($doc, $secAckNode, 'title', 'ACKNOWLEDGMENTS', false); // Title node.
		
		$pNode =& XMLCustomWriter::createChildWithText($doc, $secAckNode, 'p', 'addacknowledgments', false); // P (acknowledgments paragraph) node.
		
		$refListNode =& XMLCustomWriter::createElement($doc, 'ref-list'); // Ref-list node (repeat this group for each reference).
		XMLCustomWriter::appendChild($backNode, $refListNode);
		
		$titleSecNode =& XMLCustomWriter::createChildWithText($doc, $refListNode, 'title', 'REFERENCES', false); // Title node.
		
		$refNode =& XMLCustomWriter::createElement($doc, 'ref'); // Ref node.
		XMLCustomWriter::setAttribute($refNode, 'id', 'addid');
		XMLCustomWriter::appendChild($refListNode, $refNode);
		
		$labelNode =& XMLCustomWriter::createChildWithText($doc, $refNode, 'label', 'addlabel', false); // Label node.
		
		$elementCitationNode =& XMLCustomWriter::createElement($doc, 'element-citation'); // Element-citation node.
		XMLCustomWriter::setAttribute($elementCitationNode, 'publication-type', 'journal');
		XMLCustomWriter::appendChild($refNode, $elementCitationNode);
		
		$personGroupNode =& XMLCustomWriter::createElement($doc, 'person-group'); // Person-group node.
		XMLCustomWriter::setAttribute($personGroupNode, 'person-group-type', 'author');
		XMLCustomWriter::appendChild($elementCitationNode, $personGroupNode);
		
		$nameRefNode =& XMLCustomWriter::createElement($doc, 'name'); // Name node (repeat this group for each author).
		XMLCustomWriter::appendChild($personGroupNode, $nameRefNode);
		
		$surnameRefNode =& XMLCustomWriter::createChildWithText($doc, $nameRefNode, 'surname', 'addsurname', false); // Surname node.
		
		$givenNamesRefNode =& XMLCustomWriter::createChildWithText($doc, $nameRefNode, 'given-names', 'addgiven-names', false); // Given-names node.
		
		$articleTitleRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'article-title', 'addarticle-title', false); // Article-title node.
		XMLCustomWriter::setAttribute($articleTitleRefNode, 'xml:lang', 'addlang');
		
		$sourceRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'source', 'addsource', false); // Source node.
		XMLCustomWriter::setAttribute($sourceRefNode, 'xml:lang', 'addlang');
		
		$yearRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'year', 'addyear', false); // Year node.
		
		$volumeRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'volume', 'addvolume', false); // Volume node.
		
		$fpageRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'fpage', 'addfpage', false); // Fpage node.
		
		$lpageRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'lpage', 'addlpage', false); // Lpage node.
		
		$sourceRefNode =& XMLCustomWriter::createChildWithText($doc, $refNode, 'mixed-citation', 'addmixed-citation', false); // Mixed-citation node.
	}

	/*
	 * This function gets and adds the nodes that correspond to the abstract of the document. Still, this function is incomplete: for the SciELO schema this function needs
	 * to add each html node within the abstract of the document (check the SciELO XML document).
	 * @$doc: XML document created by XMLCustomWriter.
	 * @$article: selected journal (object).
	 * @$articleMetaNode: XML node (father node).
	 */
	private function addAbstract(&$doc, &$article, &$articleMetaNode) {
		if (is_array($article->getAbstract(null))) foreach ($article->getAbstract(null) as $locale => $abstract) {
			$abstractNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'abstract', $abstract, false);
			if ($abstractNode) {
				$lang = substr($locale, 0, 2); // Locale contains the localization of the document (es_ES, en_EN, etc.), so to obtain the idiom we just take the first two letters from the string (es, en, etc.).
				XMLCustomWriter::setAttribute($abstractNode, 'xml:lang', $lang);
			}
			unset($abstractNode);
		}
	}

	/*
	 * This function gets and adds the journal nodes to the XML tree. It is used inside "addFrontNode".
	 * @$doc: XML document created by XMLCustomWriter.
	 * @$frontNode: XML front node (father node).
	 * @$journal: selected journal (object).
	 */
	private function addJournalMeta(&$doc, &$frontNode, &$journal, &$article) {
		$journalMetaNode =& XMLCustomWriter::createElement($doc, 'journal-meta'); // Journal-meta node.
		XMLCustomWriter::appendChild($frontNode, $journalMetaNode);

		$journalIDNode =& XMLCustomWriter::createChildWithText($doc, $journalMetaNode, 'journal-id', $journal->getJournalId(), false); // Journal-id node.
		XMLCustomWriter::setAttribute($journalIDNode, 'journal-id-type', 'nlm-ta');

		$journalTitleGroupNode =& XMLCustomWriter::createElement($doc, 'journal-title-group'); // Journal-title-group node.
		XMLCustomWriter::appendChild($journalMetaNode, $journalTitleGroupNode);

		$journalTitleNode =& XMLCustomWriter::createChildWithText($doc, $journalTitleGroupNode, 'journal-title', $journal->getLocalizedTitle(), false); // Journal-title node.
		
		// At this point, there is an optional node for the translation of the title. It might be added to the document.
		
		$abbrevJournalTitleNode =& XMLCustomWriter::createChildWithText($doc, $journalTitleGroupNode, 'abbrev-journal-title', $journal->getLocalizedInitials(), false); // Abbrev-journal-title node.
		XMLCustomWriter::setAttribute($abbrevJournalTitleNode, 'abbrev-type', 'publisher');
		
		$issn = Ojs2ScieloExportDom::getISSN($journal);
		$issnNode =& XMLCustomWriter::createChildWithText($doc, $journalMetaNode, 'issn', $issn, false); // ISSN node.
		XMLCustomWriter::setAttribute($issnNode, 'pub-type', 'ppub');

		$publisherNode =& XMLCustomWriter::createElement($doc, 'publisher'); // Publisher node.
		XMLCustomWriter::appendChild($journalMetaNode, $publisherNode);

		$publisherNameNode =& XMLCustomWriter::createChildWithText($doc, $publisherNode, 'publisher-name', $journal->getSetting('publisherInstitution'), false); // Publisher-name node.
	}
	
	/*
	 * This function gets and adds the publication date nodes to the XML tree. It is used inside "addFrontNode".
	 * @$doc: XML document created by XMLCustomWriter.
	 * @$articleMetaNode: XML node (father node).
	 * @$article: selected journal (object).
	 */
	private function addPubDate(&$doc, &$articleMetaNode, &$article) {
		$pubDateNode =& XMLCustomWriter::createElement($doc, 'pub-date'); // Pub-date node.
		XMLCustomWriter::setAttribute($pubDateNode, 'pub-type', 'epub-ppub');
		XMLCustomWriter::appendChild($articleMetaNode, $pubDateNode);
		
		$season = Ojs2ScieloExportDom::getSeason($article); // Season node.
		$seasonNode =& XMLCustomWriter::createChildWithText($doc, $pubDateNode, 'season', $season, false);
		
		$year = date('Y', strtotime($article->getDatePublished())); // Gets the year of the publication by using the label 'Y'.
		$yearNode =& XMLCustomWriter::createChildWithText($doc, $pubDateNode, 'year', $year, false); // Year node.
	}
	
	/*
	 * This function gets and adds the history nodes to the XML tree. It is used inside "addFrontNode". The parameter list must
	 * be modified in order to add content to the nodes.
	 * @$doc: XML document created by XMLCustomWriter.
	 * @$articleMetaNode: XML node (father node).
	 */
	private function addHistory(&$doc, &$articleMetaNode, &$issue) {
		$historyNode =& XMLCustomWriter::createElement($doc, 'history'); // History node.
		XMLCustomWriter::appendChild($articleMetaNode, $historyNode);
		
		$dateTypeRecivedNode =& XMLCustomWriter::createElement($doc, 'date'); // Date-type received node.
		XMLCustomWriter::setAttribute($dateTypeRecivedNode, 'date-type', 'received');
		XMLCustomWriter::appendChild($historyNode, $dateTypeRecivedNode);
		
		$dayRecivedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeRecivedNode, 'day', 'addday', false); // Day-received node.
		
		$monthRecivedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeRecivedNode, 'month', 'addmonth', false); // Month-received node.
		
		$yearRecivedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeRecivedNode, 'year', 'addyear', false); // Year-received node.
		
		$dateTypeAcceptedNode =& XMLCustomWriter::createElement($doc, 'date'); // Date-type accepted node.
		XMLCustomWriter::setAttribute($dateTypeAcceptedNode, 'date-type', 'accepted');
		XMLCustomWriter::appendChild($historyNode, $dateTypeAcceptedNode);
		
		$dayAcceptedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeAcceptedNode, 'day', 'addday', false); // Day-accepted node.
		
		$monthAcceptedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeAcceptedNode, 'month', 'addmonth', false); // Month-accepted node.
		
		$yearAcceptedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeAcceptedNode, 'year', 'addyear', false); // Year-accepted node.
	}
	
	/*
	 * This function gets and adds the counts nodes to the XML tree. It is used inside "addFrontNode". The parameter list must
	 * be modified in order to add content to the nodes.
	 * @$doc: XML document created by XMLCustomWriter.
	 * @$articleMetaNode: XML node (father node).
	 */
	private function addCounts(&$doc, &$articleMetaNode) {
		$countsNode =& XMLCustomWriter::createElement($doc, 'counts'); // Counts node.
		XMLCustomWriter::appendChild($articleMetaNode, $countsNode);
		
		$figCountNode =& XMLCustomWriter::createElement($doc, 'fig-count'); // Fig-count node (number of figures in the article).
		XMLCustomWriter::setAttribute($figCountNode, 'count', 'addcount');
		XMLCustomWriter::appendChild($countsNode, $figCountNode);
		
		$tableCountNode =& XMLCustomWriter::createElement($doc, 'table-count'); // Table-count node (number of tables in the article).
		XMLCustomWriter::setAttribute($tableCountNode, 'count', 'addcount');
		XMLCustomWriter::appendChild($countsNode, $tableCountNode);
		
		$refCountNode =& XMLCustomWriter::createElement($doc, 'ref-count'); // Ref-count node (number of references in the article).
		XMLCustomWriter::setAttribute($refCountNode, 'count', 'addcount');
		XMLCustomWriter::appendChild($countsNode, $refCountNode);
		
		$pageCountNode =& XMLCustomWriter::createElement($doc, 'page-count'); // Page-count node (numbrer of pages in the article).
		XMLCustomWriter::setAttribute($pageCountNode, 'count', 'addcount');
		XMLCustomWriter::appendChild($countsNode, $pageCountNode);
	}
	
	/*
	 * This function gets and adds the funding group nodes to the XML tree. It is used inside "addFrontNode". The parameter list must
	 * be modified in order to add content to the nodes.
	 * @$doc: XML document created by XMLCustomWriter.
	 * @$articleMetaNode: XML node (father node).
	 */
	private function addFundingGroup(&$doc, &$articleMetaNode, $article) {
		$fundingGroupNode =& XMLCustomWriter::createElement($doc, 'funding-group'); // Funding-group node.
		XMLCustomWriter::appendChild($articleMetaNode, $fundingGroupNode);
		
		$awardGroupNode =& XMLCustomWriter::createElement($doc, 'award-group'); // Award-group node.
		XMLCustomWriter::appendChild($fundingGroupNode, $awardGroupNode);
		
		$fundingSourceNode =& XMLCustomWriter::createChildWithText($doc, $awardGroupNode, 'funding-source', 'addfunding-source', false); // Funding-source node.
		
		$awardIDNode =& XMLCustomWriter::createChildWithText($doc, $awardGroupNode, 'award-id', 'addaward-id', false); // Award-id node.
	}
	
	/*
	 * This function gets and adds the funding group nodes to the XML tree. It is used inside "addFrontNode". The parameter list must
	 * be modified in order to add content to the nodes.
	 * @$doc: XML document created by XMLCustomWriter.
	 * @$articleMetaNode: XML node (father node).
	 */
	private function addPermissions(&$doc, &$articleMetaNode) {
		$permissionsNode =& XMLCustomWriter::createElement($doc, 'permissions'); // Permissions node.
		XMLCustomWriter::appendChild($articleMetaNode, $permissionsNode);
		
		$licenseNode =& XMLCustomWriter::createElement($doc, 'license'); // License node.
		XMLCustomWriter::setAttribute($licenseNode, 'license-type', 'addlicensetype');
		XMLCustomWriter::setAttribute($licenseNode, 'xlink:href', 'addhref');
		XMLCustomWriter::appendChild($permissionsNode, $licenseNode);
		
		$licensePNode =& XMLCustomWriter::createChildWithText($doc, $licenseNode, 'license-p', 'addlicense-p', false); // License-p node.
	}
	
	/*
	 * Searches for the ISSN of a journal. The function excecutes three queries: print ISSN, ISSN or online ISSN; otherwise, it returns
	 * an empty variable.
	 * @journal refers to the selected journal.
	 * @return the ISSN of the publication or an empty value if it was not found.
	 */
	private function getISSN(&$journal) {
		if ($journal->getSetting('printIssn') != '') $issn = $journal->getSetting('printIssn');
		elseif ($journal->getSetting('issn') != '') $issn = $journal->getSetting('issn');
		elseif ($journal->getSetting('onlineIssn') != '') $issn = $journal->getSetting('onlineIssn');
		else $issn = '';
		return $issn;
	}
	
	/*
	 * This function obtains the title or titles of an article and adds them to the tree of nodes.
	 * @$doc XML document created by XMLCustomWriter.
	 * @$article from where the titles are going to be extracted.
	 * @$titleGroupNode father node.
	 */
	private function getArticleTitle(&$doc, &$article, &$titleGroupNode) {
		// Checks if the article contains more than one title.
		if (is_array($article->getTitle(null))) foreach ($article->getTitle(null) as $locale => $title) {
			if($article->getLocale() == $locale) {
				$titleNode =& XMLCustomWriter::createChildWithText($doc, $titleGroupNode, 'article-title', $title, false);
				$lang = substr($locale, 0, 2); // Locale contains the localization of the document (es_ES, en_EN, etc.), so to obtain the idiom we just take the first two letters from the string (es, en, etc.).
				XMLCustomWriter::setAttribute($titleNode, 'xml:lang', $lang); // Adds title & language properly.
				unset($titleNode); // Desocupies the variable for the next title.
				unset($lang); // Desocupies the variable for the next language.
			} else {
				$transTitleGroupNode =& XMLCustomWriter::createElement($doc, 'trans-title-group');
				$lang = substr($locale, 0, 2); // Locale contains the localization of the document (es_ES, en_EN, etc.), so to obtain the idiom we just take the first two letters from the string (es, en, etc.).
				XMLCustomWriter::setAttribute($transTitleGroupNode, 'xml:lang', $lang); // Adds title & language properly.
				XMLCustomWriter::appendChild($titleGroupNode, $transTitleGroupNode);
				$transTitleNode =& XMLCustomWriter::createChildWithText($doc, $transTitleGroupNode, 'trans-title', $title, false);
				unset($transTitleGroupNode); // Desocupies the variable for the title group.
				unset($transTitleNode); // Desocupies the variable for the next title.
				unset($lang); // Desocupies the variable for the next language.
			}
		}
	}
	
	/*
	 * This function obtains the author or authors of an article and adds them to the tree of nodes.
	 * @$doc XML document created by XMLCustomWriter.
	 * @$article from where the authors are going to be extracted.
	 * @$contribGroupNode father node.
	 */
	private function getArticleAuthors(&$doc, &$article, &$contribGroupNode) {
		$i = 1; // Integer that indicates the position of the author in the XML tree.
		foreach ($article->getAuthors() as $author) {
			$contribNode =& XMLCustomWriter::createElement($doc, 'contrib'); // Contrib node.
			XMLCustomWriter::setAttribute($contribNode, 'contrib-type', 'author');
			XMLCustomWriter::appendChild($contribGroupNode, $contribNode);
			
			$nameNode =& XMLCustomWriter::createElement($doc, 'name'); // Name node.
			XMLCustomWriter::appendChild($contribNode, $nameNode);
			
			$surnameNode =& XMLCustomWriter::createChildWithText($doc, $nameNode, 'surname', $author->getFirstName(), false); // Surname node.
			
			$givenNamesNode =& XMLCustomWriter::createChildWithText($doc, $nameNode, 'given-names', $author->getLastName(), false); // Given-names node.
			
			$rid = "aff" . $i; // Concatenation of "aff" plus the position of the author. This label corresponds to the schema developed by SciELO.
			$affLinkNode =& XMLCustomWriter::createChildWithText($doc, $contribNode, 'xref', $i, false); // Xref node.
			XMLCustomWriter::setAttribute($affLinkNode, 'ref-type', 'aff');
			XMLCustomWriter::setAttribute($affLinkNode, 'rid', $rid);
			
			$i++;
			
			unset($authorNode);
			unset($contribNode);
			unset($nameNode);
			unset($surnameNode);
			unset($givenNamesNode);
			unset($affLinkNode);
		}
	}
	
	/*
	 * This function obtains the affiliation an author and adds them to the tree of nodes.
	 * @$doc XML document created by XMLCustomWriter.
	 * @$article from where the affiliations are going to be extracted.
	 * @$articleMetaNode father node.
	 */
	private function getAuthorsAffiliation(&$doc, &$article, &$articleMetaNode) {
		$j = 1; // Integer that indicates the position of the affiliation of an author in the XML tree. In order to make sense, this variable and the one in the function "getArticleAuthors" must match.
		foreach ($article->getAuthors() as $author) {
			$affGroupNode =& XMLCustomWriter::createElement($doc, 'aff'); // Aff node.
			$affid = "aff" . $j; // This concatenation bounds the author with its corresponding affiliation. The affiliation with label 1 must refer to the first author in the tree.
			XMLCustomWriter::setAttribute($affGroupNode, 'id', $affid);
			XMLCustomWriter::appendChild($articleMetaNode, $affGroupNode);

			$labelNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'label', $j, false); // Label node.
			
			$affiliations = $author->getAffiliation(null); // Extracts the affiliations.

			$i = 0; // Integer that indicates the number of affiliation.

			// Extracts the information each author.
			if (is_array($affiliations)) foreach ($affiliations as $locale => $affiliation) {
				$i++;
				$orgDiv = "orgdiv" . $i; // Concatenates the division where the author belongs with the number of affiliation.

				$orgDivNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'institution', $affiliation, false); // Orgdiv node.
				XMLCustomWriter::setAttribute($orgDivNode, 'content-type', $orgDiv);
				
				$orgNameNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'institution', $affiliation, false); // Orgname node.
				XMLCustomWriter::setAttribute($orgNameNode, 'content-type', 'orgname');

				unset($orgDivNode);
				unset($orgNameNode);
			}
			
			$zipCodeNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'named-content', 'addzipcode', false); // Zipcode node.
			XMLCustomWriter::setAttribute($zipCodeNode, 'content-type', 'zipcode');
			
			$cityNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'named-content', 'addcity', false); // City node.
			XMLCustomWriter::setAttribute($cityNode, 'content-type', 'city');
			
			$stateNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'named-content', 'addstate', false); // State node.
			XMLCustomWriter::setAttribute($stateNode, 'content-type', 'state');
			
			$addrNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'addr-line', 'addaddr-line', false); // Addr-line node.
			
			$countryNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'country', $author->getCountry(), false); // Country node.
			
			$institutionalEmailNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'email', $author->getEmail(), false); // Email node.

			$j++; // Increments the number of authors.
			
			// Releases the nodes (variables) for the next iteration.
			unset($affGroupNode);
			unset($labelNode);
			unset($zipCodeNode);
			unset($cityNode);
			unset($stateNode);
			unset($addrNode);
			unset($countryNode);
			unset($institutionalEmailNode);	
		}
	}
	
	/*
	 * Since the SciELO's schema requieres a season (name of the month) for the publication date, there must be a casting. This function
	 * obtains the corresponding numbrer of the month and returns the initials of the month.
	 * @$article from where the season is going to be extracted.
	 * @return initials of the month or an empty variable if there is an error.
	 */
	private function getSeason(&$article) {
		$m = date('m', strtotime($article->getDatePublished())); // Gets the number of the month by using the label 'm'.
		switch ($m) {
    			case 1:
        			$season = "Jan";
        			break;
    			case 2:
        			$season = "Feb";
        			break;
    			case 3:
        			$season = "Mar";
        			break;
        		case 4:
        			$season = "Apr";
        			break;
        		case 5:
        			$season = "May";
        			break;
        		case 6:
        			$season = "Jun";
        			break;
        		case 7:
        			$season = "Jul";
        			break;
        		case 8:
        			$season = "Aug";
        			break;
        		case 9:
        			$season = "Sep";
        			break;
        		case 10:
        			$season = "Oct";
        			break;
        		case 11:
        			$season = "Nov";
        			break;
        		case 12:
        			$season = "Dec";
        			break;
        		default:
        			$season = "";
        			break;
		}
		return $season;
	}
	
	/*
	 * After the tree of nodes is completed, this method exports it into the XML file.
	 * @$article refers to the selected article (object).
	 * @$doc refers to the XML document.
	 * @$outputFile refers to the output type.
	 */
	private function exportXML(&$article, &$doc, $outputFile = null) {
		if (!empty($outputFile)) {
			if (($h = fopen($outputFile, 'w'))===false) return false;
			fwrite($h, XMLCustomWriter::getXML($doc));
			fclose($h);
		} else {
			header("Content-Type: application/xml");
			header("Cache-Control: private");
			header("Content-Disposition: attachment; filename=\"article-" . $article->getId() . ".xml\""); // Name & extension of the file.
			XMLCustomWriter::printXML($doc);
		}
	}	
}

?>
