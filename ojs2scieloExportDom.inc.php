<?php

/**
 * @file plugins/importexport/ojs2scielo/Ojs2scieloExportDom.inc.php
 *
 * Copyright (c) 2013-2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class Ojs2ScieloExportDomM
 * @ingroup plugins_importexport_native
 *
 * @brief import/export plugin DOM functions for export
 * Edited by Vicerrectoría de investigación - Universidad de Costa Rica.
 */


import('lib.pkp.classes.xml.XMLCustomWriter');

define('NATIVE_DTD_URL', 'http://pkp.sfu.ca/ojs/dtds/2.3/native.dtd');
define('NATIVE_DTD_ID', '-//PKP//OJS Articles and Issues XML//EN');

/*
 * This class exports an article to a XML using the XMLCustomWriter class already implemented (for more information about
 * the creation of nodes and attributes: http://pkp.sfu.ca/ojs/doxygen/stable/html/XMLCustomWriter_8inc_8php_source.html).
 * Most methods are used for modularization (construction of each main node) and gather information.
 */
class Ojs2ScieloExportDom {

	/*
	 * exportArticle creates the XML document, adds the front node to the document (with its children), adds the body
	 * node to the document (with its children), adds the back node to the document (with its children) and finally
	 * exports the XML.
	 * This method receives five parameters that contain the corresponding journal, issue, section, and article chosen
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
		
		// Header node and its attributes (first node of the document).
		$header =& XMLCustomWriter::createElement($doc, 'article');
		XMLCustomWriter::setAttribute($header, 'xmlns:xlink', 'addurl');
		XMLCustomWriter::setAttribute($header, 'xmlns:mml', 'addmml');
		XMLCustomWriter::setAttribute($header, 'dtd-version', '3.0');
		XMLCustomWriter::setAttribute($header, 'article-type', 'addtype');
		XMLCustomWriter::setAttribute($header, 'xml:lang', $article->getLanguage());
		XMLCustomWriter::appendChild($doc, $header);
		
		// Adds the front node to the document (with its children).
		Ojs2ScieloExportDom::addFrontNode($journal, $article, $doc, $header);
		
		// Adds the body node to the document (with its children).
		Ojs2ScieloExportDom::addBodyNode($article, $doc, $header);
		
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
	private function addFrontNode(&$journal, &$article, &$doc, &$header) {
		// Front node.
		$frontNode =& XMLCustomWriter::createElement($doc, 'front');
		XMLCustomWriter::appendChild($header, $frontNode);

		// Journal-meta node.
		$journalMetaNode =& XMLCustomWriter::createElement($doc, 'journal-meta');
		XMLCustomWriter::appendChild($frontNode, $journalMetaNode);

		// Journal-id node.
		$journalIDNode =& XMLCustomWriter::createChildWithText($doc, $journalMetaNode, 'journal-id', $journal->getJournalId(), false);
		XMLCustomWriter::setAttribute($journalIDNode, 'journal-id-type', 'nlm-ta');

		// Journal-title-group node.
		$journalTitleGroupNode =& XMLCustomWriter::createElement($doc, 'journal-title-group');
		XMLCustomWriter::appendChild($journalMetaNode, $journalTitleGroupNode);

		// Journal-title node.
		$journalTitleNode =& XMLCustomWriter::createChildWithText($doc, $journalTitleGroupNode, 'journal-title', $journal->getLocalizedTitle(), false);
		
		// At this point, there is an optional node for the translation of the title. It might be added to the document.
		
		// Abbrev-journal-title node.
		$abbrevJournalTitleNode =& XMLCustomWriter::createChildWithText($doc, $journalTitleGroupNode, 'abbrev-journal-title', $journal->getLocalizedInitials(), false);
		XMLCustomWriter::setAttribute($abbrevJournalTitleNode, 'abbrev-type', 'publisher');
		
		// ISSN node.
		$issn = Ojs2ScieloExportDom::getISSN($journal);
		$issnNode =& XMLCustomWriter::createChildWithText($doc, $journalMetaNode, 'issn', $issn, false);
		XMLCustomWriter::setAttribute($issnNode, 'pub-type', 'ppub');
		
		// Publisher node.
		$publisherNode =& XMLCustomWriter::createElement($doc, 'publisher');
		XMLCustomWriter::appendChild($journalMetaNode, $publisherNode);

		// Publisher-name node.
		$publisherNameNode =& XMLCustomWriter::createChildWithText($doc, $publisherNode, 'publisher-name', 'addpublisher-name', false);
		
		// Article-meta node.
		$articleMetaNode =& XMLCustomWriter::createElement($doc, 'article-meta');
		XMLCustomWriter::appendChild($frontNode, $articleMetaNode);
		
		// Article-id (publisher-id) node.
		$publisherIDNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'article-id', $article->getPublishedArticleId(), false);
		XMLCustomWriter::setAttribute($publisherIDNode, 'pub-id-type', 'publisher-id');
		
		// Article-id (doi) node.
		$publisherIDNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'article-id', 'adddoi', false);
		XMLCustomWriter::setAttribute($publisherIDNode, 'pub-id-type', 'doi');
		
		// Article-categories node.
		$articleCategoriesNode =& XMLCustomWriter::createElement($doc, 'article-categories');
		XMLCustomWriter::appendChild($articleMetaNode, $articleCategoriesNode);
		
		// Subj-group node.
		$subjectGroupNode =& XMLCustomWriter::createElement($doc, 'subj-group');
		XMLCustomWriter::setAttribute($subjectGroupNode, 'subj-group-type', 'heading');
		XMLCustomWriter::appendChild($articleCategoriesNode, $subjectGroupNode);
		
		// Subject node.
		$subjectNode =& XMLCustomWriter::createChildWithText($doc, $subjectGroupNode, 'subject', 'addsubject', false);
		
		// Title-group node.
		$titleGroupNode =& XMLCustomWriter::createElement($doc, 'title-group');
		XMLCustomWriter::appendChild($articleMetaNode, $titleGroupNode);
		
		// Article-title(s) node(s).
		Ojs2ScieloExportDom::getArticleTitle($article, $titleGroupNode);
		
		// Contrib-group node(s).
		$contribGroupNode =& XMLCustomWriter::createElement($doc, 'contrib-group');
		XMLCustomWriter::appendChild($articleMetaNode, $contribGroupNode);
		
		// Authors node.
		Ojs2ScieloExportDom::getArticleAuthors($article, $contribGroupNode);
		
		// Affiliation node(s).
		Ojs2ScieloExportDom::getAuthorsAffiliation($article, $articleMetaNode);
		
		// Author-notes node.
		$authorNotesNode =& XMLCustomWriter::createElement($doc, 'author-notes');
		XMLCustomWriter::appendChild($articleMetaNode, $authorNotesNode);
		
		// Corresp node.
		$correspNode =& XMLCustomWriter::createElement($doc, 'corresp');
		XMLCustomWriter::appendChild($authorNotesNode, $correspNode);
		
		// Bold node.
		$boldNode =& XMLCustomWriter::createChildWithText($doc, $correspNode, 'bold', 'Correspondence', false);
		
		// Personal email node.
		$personalEmailNode =& XMLCustomWriter::createChildWithText($doc, $correspNode, 'email', 'addemail', false);
		
		// Pub-date node.
		$pubDateNode =& XMLCustomWriter::createElement($doc, 'pub-date');
		XMLCustomWriter::setAttribute($pubDateNode, 'pub-type', 'epub-ppub');
		XMLCustomWriter::appendChild($articleMetaNode, $pubDateNode);
		
		// Season node.
		$season = Ojs2ScieloExportDom::getSeason($article);
		$seasonNode =& XMLCustomWriter::createChildWithText($doc, $pubDateNode, 'season', $season, false);
		
		// Year node.
		$year = date('Y', strtotime($article->getDatePublished())); // Gets the year of the publication by using the label 'Y'.
		$yearNode =& XMLCustomWriter::createChildWithText($doc, $pubDateNode, 'year', $year, false);
		
		// Volume node.
		$volumeNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'volume', 'addvolume', false);
		
		// Issue node.
		$issueNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'issue', 'addissue', false);
		
		// Fpage node.
		$fPageNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'fpage', 'addfpage', false);
		
		// Lpage node.
		$lPageNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'lpage', 'addlpage', false);
		
		// History node.
		$historyNode =& XMLCustomWriter::createElement($doc, 'history');
		XMLCustomWriter::appendChild($articleMetaNode, $historyNode);
		
		// Date-type received node.
		$dateTypeRecivedNode =& XMLCustomWriter::createElement($doc, 'date');
		XMLCustomWriter::setAttribute($dateTypeRecivedNode, 'date-type', 'received');
		XMLCustomWriter::appendChild($historyNode, $dateTypeRecivedNode);
		
		// Day-received node.
		$dayRecivedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeRecivedNode, 'day', 'addday', false);
		
		// Month-received node.
		$monthRecivedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeRecivedNode, 'month', 'addmonth', false);
		
		// Year-received node.
		$yearRecivedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeRecivedNode, 'year', 'addyear', false);
		
		// Date-type accepted node.
		$dateTypeAcceptedNode =& XMLCustomWriter::createElement($doc, 'date');
		XMLCustomWriter::setAttribute($dateTypeAcceptedNode, 'date-type', 'accepted');
		XMLCustomWriter::appendChild($historyNode, $dateTypeAcceptedNode);
		
		// Day-accepted node.
		$dayAcceptedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeAcceptedNode, 'day', 'addday', false);
		
		// Month-accepted node.
		$monthAcceptedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeAcceptedNode, 'month', 'addmonth', false);
		
		// Year-accepted node.
		$yearAcceptedNode =& XMLCustomWriter::createChildWithText($doc, $dateTypeAcceptedNode, 'year', 'addyear', false);
		
		// Permissions node.
		$permissionsNode =& XMLCustomWriter::createElement($doc, 'permissions');
		XMLCustomWriter::appendChild($articleMetaNode, $permissionsNode);
		
		// License node.
		$licenseNode =& XMLCustomWriter::createElement($doc, 'license');
		XMLCustomWriter::setAttribute($licenseNode, 'license-type', 'addlicensetype');
		XMLCustomWriter::setAttribute($licenseNode, 'xlink:href', 'addhref');
		XMLCustomWriter::appendChild($permissionsNode, $licenseNode);
		
		// License-p node.
		$licensePNode =& XMLCustomWriter::createChildWithText($doc, $licenseNode, 'license-p', 'addlicense-p', false);
		
		// Abstract node.
		$abstractNode =& XMLCustomWriter::createChildWithText($doc, $articleMetaNode, 'abstract', 'addabstract', false);
		XMLCustomWriter::setAttribute($abstractNode, 'xml:lang', 'addlang');
		
		// Inside the abstract node, there are the "title" and "paragraph" nodes. These nodes do not appear in
		// this schema because they depend on the content of the abstact. However, they must be attached in
		// the next release. Likewise, there is a "translation" node that must be taken into account.
		
		// Kwd-group node.
		$kwdGroupNode =& XMLCustomWriter::createElement($doc, 'kwd-group');
		XMLCustomWriter::setAttribute($kwdGroupNode, 'xml:lang', 'addlanguage');
		XMLCustomWriter::appendChild($articleMetaNode, $kwdGroupNode);
		
		// For each key word, the document must create a new node. There is also an optional "translation key word" group of
		// nodes. This group is similar to the "kwd-group", with the exception that it contains the translation language
		// as an attribute of the node.
		
		// Funding-group node.
		$fundingGroupNode =& XMLCustomWriter::createElement($doc, 'funding-group');
		XMLCustomWriter::appendChild($articleMetaNode, $fundingGroupNode);
		
		// Award-group node.
		$awardGroupNode =& XMLCustomWriter::createElement($doc, 'award-group');
		XMLCustomWriter::appendChild($fundingGroupNode, $awardGroupNode);
		
		// Funding-source node.
		$fundingSourceNode =& XMLCustomWriter::createChildWithText($doc, $awardGroupNode, 'funding-source', 'addfunding-source', false);
		
		// Award-id node.
		$awardIDNode =& XMLCustomWriter::createChildWithText($doc, $awardGroupNode, 'award-id', 'addaward-id', false);
		
		// Counts node.
		$countsNode =& XMLCustomWriter::createElement($doc, 'counts');
		XMLCustomWriter::appendChild($articleMetaNode, $countsNode);
		
		// Fig-count node (number of figures in the article).
		$figCountNode =& XMLCustomWriter::createElement($doc, 'fig-count');
		XMLCustomWriter::setAttribute($figCountNode, 'count', 'addcount');
		XMLCustomWriter::appendChild($countsNode, $figCountNode);
		
		// Table-count node (number of tables in the article).
		$tableCountNode =& XMLCustomWriter::createElement($doc, 'table-count');
		XMLCustomWriter::setAttribute($tableCountNode, 'count', 'addcount');
		XMLCustomWriter::appendChild($countsNode, $tableCountNode);
		
		// Ref-count node (number of references in the article).
		$refCountNode =& XMLCustomWriter::createElement($doc, 'ref-count');
		XMLCustomWriter::setAttribute($refCountNode, 'count', 'addcount');
		XMLCustomWriter::appendChild($countsNode, $refCountNode);
		
		// Page-count node (numbrer of pages in the article).
		$pageCountNode =& XMLCustomWriter::createElement($doc, 'page-count');
		XMLCustomWriter::setAttribute($pageCountNode, 'count', 'addcount');
		XMLCustomWriter::appendChild($countsNode, $pageCountNode);
	}
	
	/*
	 * This method adds the body node to the XML document. For now, it just contains the basic node.
	 * Here, the nodes are tied to the content of the document, so there must be a strategy to extract the body from the document and
	 * transform these sections into nodes (paragraphs, titles, tables, etc.).
	 */
	private function addBodyNode(&$article, &$doc, &$header) {
		// Body node.
		$bodyNode =& XMLCustomWriter::createElement($doc, 'body');
		XMLCustomWriter::appendChild($header, $bodyNode);
	}
	
	/*
	 * addBackNode creates the structure for the "back" node. For now, it just has the basic requirements, but later some of these
	 * groups of nodes must be replicated; for instance: "ref-list". This is directly related to the content of the documents.
	 */
	private function addBackNode(&$article, &$doc, &$header) {
		// Back node.
		$backNode =& XMLCustomWriter::createElement($doc, 'back');
		XMLCustomWriter::appendChild($header, $backNode);
		
		// Ack node.
		$ackNode =& XMLCustomWriter::createElement($doc, 'ack');
		XMLCustomWriter::appendChild($backNode, $ackNode);
		
		// Sec node.
		$secAckNode =& XMLCustomWriter::createElement($doc, 'sec');
		XMLCustomWriter::appendChild($ackNode, $secAckNode);
		
		// Title node.
		$titleSecNode =& XMLCustomWriter::createChildWithText($doc, $secAckNode, 'title', 'ACKNOWLEDGMENTS', false);
		
		// P (acknowledgments paragraph) node.
		$pNode =& XMLCustomWriter::createChildWithText($doc, $secAckNode, 'p', 'addacknowledgments', false);
		
		// Ref-list node (repeat this group for each reference).
		$refListNode =& XMLCustomWriter::createElement($doc, 'ref-list');
		XMLCustomWriter::appendChild($backNode, $refListNode);
		
		// Title node.
		$titleSecNode =& XMLCustomWriter::createChildWithText($doc, $refListNode, 'title', 'REFERENCES', false);
		
		// Ref node.
		$refNode =& XMLCustomWriter::createElement($doc, 'ref');
		XMLCustomWriter::setAttribute($refNode, 'id', 'addid');
		XMLCustomWriter::appendChild($refListNode, $refNode);
		
		// Label node.
		$labelNode =& XMLCustomWriter::createChildWithText($doc, $refNode, 'label', 'addlabel', false);
		
		// Element-citation node.
		$elementCitationNode =& XMLCustomWriter::createElement($doc, 'element-citation');
		XMLCustomWriter::setAttribute($elementCitationNode, 'publication-type', 'journal');
		XMLCustomWriter::appendChild($refNode, $elementCitationNode);
		
		// Person-group node.
		$personGroupNode =& XMLCustomWriter::createElement($doc, 'person-group');
		XMLCustomWriter::setAttribute($personGroupNode, 'person-group-type', 'author');
		XMLCustomWriter::appendChild($elementCitationNode, $personGroupNode);
		
		// Name node (repeat this group for each author).
		$nameRefNode =& XMLCustomWriter::createElement($doc, 'name');
		XMLCustomWriter::appendChild($personGroupNode, $nameRefNode);
		
		// Surname node.
		$surnameRefNode =& XMLCustomWriter::createChildWithText($doc, $nameRefNode, 'surname', 'addsurname', false);
		
		// Given-names node.
		$givenNamesRefNode =& XMLCustomWriter::createChildWithText($doc, $nameRefNode, 'given-names', 'addgiven-names', false);
		
		// Article-title node.
		$articleTitleRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'article-title', 'addarticle-title', false);
		XMLCustomWriter::setAttribute($articleTitleRefNode, 'xml:lang', 'addlang');
		
		// Source node.
		$sourceRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'source', 'addsource', false);
		XMLCustomWriter::setAttribute($sourceRefNode, 'xml:lang', 'addlang');
		
		// Year node.
		$yearRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'year', 'addyear', false);
		
		// Volume node.
		$volumeRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'volume', 'addvolume', false);
		
		// Fpage node.
		$fpageRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'fpage', 'addfpage', false);
		
		// Lpage node.
		$lpageRefNode =& XMLCustomWriter::createChildWithText($doc, $elementCitationNode, 'lpage', 'addlpage', false);
		
		// Mixed-citation node.
		$sourceRefNode =& XMLCustomWriter::createChildWithText($doc, $refNode, 'mixed-citation', 'addmixed-citation', false);
	}
	
	/*
	 * Searches for the ISSN of a journal. The function excecutes three queries: print ISSN, ISSN or online ISSN; otherwise, it returns
	 * an empty variable.
	 * @journal refers to the selected journal.
	 * @return value of the ISSN or an empty value if it was not found.
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
	 * @$article from where the titles are going to be extracted.
	 * @$titleGroupNode father node.
	 */
	private function getArticleTitle(&$article, &$titleGroupNode) {
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
	 * @$article from where the authors are going to be extracted.
	 * @$contribGroupNode father node.
	 */
	private function getArticleAuthors(&$article, &$contribGroupNode) {
		$i = 1; // Integer that indicates the position of the author in the XML tree.
		foreach ($article->getAuthors() as $author) {
			// Contrib node.
			$contribNode =& XMLCustomWriter::createElement($doc, 'contrib');
			XMLCustomWriter::setAttribute($contribNode, 'contrib-type', 'author');
			XMLCustomWriter::appendChild($contribGroupNode, $contribNode);
			
			// Name node.
			$nameNode =& XMLCustomWriter::createElement($doc, 'name');
			XMLCustomWriter::appendChild($contribNode, $nameNode);
			
			// Surname node.
			$surnameNode =& XMLCustomWriter::createChildWithText($doc, $nameNode, 'surname', $author->getFirstName(), false);
			
			// Given-names node.
			$givenNamesNode =& XMLCustomWriter::createChildWithText($doc, $nameNode, 'given-names', $author->getLastName(), false);
			
			// Xref node.
			$rid = "aff" . $i; // Concatenation of "aff" plus the position of the author. This label corresponds to the schema developed by SciELO.
			$affLinkNode =& XMLCustomWriter::createChildWithText($doc, $contribNode, 'xref', $i, false);
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
	
	private function getAuthorsAffiliation(&$article, &$articleMetaNode) {
		$j = 1;
		foreach ($article->getAuthors() as $author) {
			// Nodo: aff.
			$affGroupNode =& XMLCustomWriter::createElement($doc, 'aff');
			$affid = "aff" . $j; // $affid + $j: enlazan el grupo de autor con su correspondiente afiliación. Es decir, el primer autor deberá tener como etiqueta 1 y el atributo del nodo $affGroupNode: 'aff1', el segudo etiqueta 2 y atributo 'aff2', etc.
			XMLCustomWriter::setAttribute($affGroupNode, 'id', $affid);
			XMLCustomWriter::appendChild($articleMetaNode, $affGroupNode);

			// Nodo: label.
			$labelNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'label', $j, false);
		
			// Obtiene las afiliaciones de cada autor.
			$affiliations = $author->getAffiliation(null);

			$i = 0;

			// Itera sobre las afiliaciones de cada autor.
			if (is_array($affiliations)) foreach ($affiliations as $locale => $affiliation) {
				$i++;
				$orgDiv = "orgdiv" . $i; // "orgdiv + #"

				// Nodo: orgdiv.
				$orgDivNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'institution', $affiliation, false);
				XMLCustomWriter::setAttribute($orgDivNode, 'content-type', $orgDiv);
				
				// Nodo: orgname.
				$orgNameNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'institution', $affiliation, false);
				XMLCustomWriter::setAttribute($orgNameNode, 'content-type', 'orgname');

				unset($orgDivNode);
				unset($orgNameNode);
			}
		
			// Nodo: zipcode.
			$zipCodeNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'named-content', 'addzipcode', false);
			XMLCustomWriter::setAttribute($zipCodeNode, 'content-type', 'zipcode');
		
			// Nodo: city.
			$cityNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'named-content', 'addcity', false);
			XMLCustomWriter::setAttribute($cityNode, 'content-type', 'city');
		
			// Nodo: state
			$stateNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'named-content', 'addstate', false);
			XMLCustomWriter::setAttribute($stateNode, 'content-type', 'state');
		
			// Nodo: addr-line.
			$addrNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'addr-line', 'addaddr-line', false);
		
			// Nodo: country.
			$countryNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'country', $author->getCountry(), false);
		
			// Nodo: email.
			$institutionalEmailNode =& XMLCustomWriter::createChildWithText($doc, $affGroupNode, 'email', $author->getEmail(), false);

			$j++;

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
	
	private function getSeason(&$article) {
		$m = date('m', strtotime($article->getDatePublished())); // Obtiene el mes de la publicación.

		// Se transforma el número correspondiente al año a sus iniciales para obtener $season.
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
        		$season = "Dic";
        	break;
        	default:
        		$season = "";
        	break;
		}
		return $season;
	}
	
	/*
	 * After the tree of nodes is completed, this method exports it into the XML file.
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
