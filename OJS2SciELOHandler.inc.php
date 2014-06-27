<?php

import('classes.handler.Handler');
import('file.ArticleFileManager');
import('lib.pkp.classes.core.JSONManager');
import('lib.pkp.classes.core.JSONMessage');

class OJS2SciELOHandler extends Handler {

    /**
     * Show authorAditionalFields submit form.
     */
    function submitAuthorAdditionalFields($args = array()) {
        ArticlesExtrasHandler::validate();
        ArticlesExtrasHandler::setupTemplate();
        $journal = &Request::getJournal();

        if ($journal != null) {
            $journalId = $journal->getJournalId();
        } else {
            Request::redirect(null, 'index');
        }

        $articlesExtrasPlugin = &PluginRegistry::getPlugin('generic', ARTICLES_EXTRAS_PLUGIN_NAME);

        if ($articlesExtrasPlugin != null) {
            $articlesExtrasEnabled = $articlesExtrasPlugin->getEnabled();
        }

        if ($articlesExtrasEnabled) {
            $articlesExtrasPlugin->import('pages.forms.ArticlesExtrasAuthorAdditionalFieldsForm');
            $form = & new ArticlesExtrasAuthorAdditionalFieldsForm($articlesExtrasPlugin, $journal->getJournalId());

            $form->initData($args);
            $form->display();
        } else {
            Request::redirect(null, 'index');
        }
    }

}

?>
