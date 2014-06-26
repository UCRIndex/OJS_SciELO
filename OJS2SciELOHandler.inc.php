<?php

import('classes.handler.Handler');
import('file.ArticleFileManager');
import('lib.pkp.classes.core.JSONManager');
import('lib.pkp.classes.core.JSONMessage');

class OJS2SciELOHandler extends Handler {

  function submitImages($args = array()) {
        OJS2SciELOHandler::validate();
        OJS2SciELOHandler::setupTemplate();
        $journal = &Request::getJournal();

        if ($journal != null) {
            $journalId = $journal->getJournalId();
        } else {
            Request::redirect(null, 'index');
        }
    }

}

?>
