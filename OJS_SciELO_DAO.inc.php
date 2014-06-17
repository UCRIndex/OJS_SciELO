// In progress

<?php

class OJS_SciELO_DAO extends DAO {

	function OJS_SciELO_DAO() {
		parent::DAO();
	}

	function getArticleBody($articleId) {
		$primaryLocale = Locale::getPrimaryLocale();
		$result = &$this->retrieve('SELECT setting_value FROM article_settings WHERE 
									setting_name=\'body\' AND 
									article_id = ? AND 
									locale = ? ', 
									array($articleId, $primaryLocale));
		return $result->fields['setting_value'];
	}

	function settingExists($articleId, $settingName, $locale = null){
		if($locale == null) $locale = Locale::getPrimaryLocale();
		$result = &$this->retrieve('SELECT setting_value FROM article_settings WHERE 
									setting_name = ? AND 
									article_id = ? AND 
									locale = ?', 
									array($settingName, $articleId, $locale)
								   );
		if ($result->RecordCount() != 0) {
			return true;
		} else return false;
	}

	function setArticleBody(&$article, $body){
		if(!$this->settingExists($article->getArticleId(), "body"))
			$this->insertArticleBody($article, $body);
		else
			$this->updateArticleBody($article, $body);
	}
	
	// ================= <BODY> ================= \\
	function insertArticleBody(&$article, $body) {
		$primaryLocale = Locale::getPrimaryLocale();
		$this->update(
			sprintf('INSERT INTO article_settings
				(
					article_id,
					locale,
					setting_name,
					setting_value,
					setting_type
				)
				VALUES 
				(%s, \'%s\', \'%s\', \'%s\', \'%s\')',
				$article->getArticleId(),
				$primaryLocale,
				"body",
				$body,
				"string"
			)
	 	);
	}
	
	function updateArticleBody(&$article, $body) {
		$primaryLocale = Locale::getPrimaryLocale();
		$this->update(
			sprintf('UPDATE article_settings SET 
					setting_value = \'%s\' 
					WHERE article_id = %s AND 
					setting_name=\'body\' AND 
					locale=\'%s\'', 
					$body, 
					$article->getArticleId(), 
					$primaryLocale
				)
	 	);
	}

	function getArticleBody($articleId) {
		$primaryLocale = Locale::getPrimaryLocale();

		$result = &$this->retrieve('SELECT setting_value FROM article_settings WHERE 
									setting_name=\'body\' AND 
									article_id = ? AND 
									locale = ? ', 
									array($articleId, $primaryLocale));
		
		return $result->fields['setting_value'];
	}
	// ================= </BODY> ================= \\

	function insertArticleImages(&$article, $images) {

		$primaryLocale = Locale::getPrimaryLocale();
		$this->update(
			sprintf('INSERT INTO article_settings
				(
					article_id,
					locale,
					setting_name,
					setting_value,
					setting_type
				)
				VALUES 
				(%s, \'%s\', \'%s\', \'%s\', \'%s\')',
				$article->getArticleId(),
				$primaryLocale,
				"images",
				$images,
				"string"
			)
	 	);
	}

}

?>
