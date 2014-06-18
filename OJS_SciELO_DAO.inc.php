// In progress

<?php
/*
 * The intention with this class is to encapsulate the necesary data for this plugin in a DAO object. This DAO will contain extra information about
 * publications. For instance: the body, citations and count of images among othrers.
 */
class OJS_SciELO_DAO extends DAO {

	function OJS_SciELO_DAO() { // DAO's constructor.
		parent::DAO();
	}

	/*
	 * This function checks if a determinate setting (attribute) is already part of the corresponding table.
	 * @$articleId: article identifier ($article->getArticleId()).
	 * @$setting_name: setting to be checked into the database.
	 * @$locale: localization of the article.
	 */
	function settingExists($articleId, $settingName, $locale = null) {
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

	/*
	 * Checks if the database already contains the article body as an attribute. If so, this function updates the text. Otherwise, it includes the new
	 * setting to the database.
	 * @$article: object that contains an article.
	 * @$body: string that contains the article body.
	 */
	function setArticleBody(&$article, $body) {
		if(!$this->settingExists($article->getArticleId(), "body"))	$this->insertArticleBody($article, $body);
		else $this->updateArticleBody($article, $body);
	}
	

	/*
	 * Inserts the article body into the database (assumes that the attribute does not exist into the database).
	 * @$article: object that contains an article.
	 * @$body: string that contains the article body. 
	 */
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
	
	/*
	 * Updates the article body into the database (assumes that the attribute already exists into the database).
	 * @$article: object that contains an article.
	 * @$body: string that contains the article body. 
	 */
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

	/*
	 * Retrieves the article body from de database from an article identifier.
	 * @$articleId: article identifier ($article->getArticleId()).
	 */
	function getArticleBody($articleId) {
		$primaryLocale = Locale::getPrimaryLocale();
		$result = &$this->retrieve('SELECT setting_value FROM article_settings WHERE 
									setting_name=\'body\' AND 
									article_id = ? AND 
									locale = ? ', 
									array($articleId, $primaryLocale));
		return $result->fields['setting_value'];
	}

	/////////////////////////////////////////////////////////////////////// IMAGES

	/*
	 * Stores an image as a string in the database (assumes that the attribute does not exist into the database). This function is usefull for the
	 * 'count node' of the SciELO schema.
	 * @$article: object that contains an article.
	 * @$images: string.
	 */
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

	/*
	 * Checks if the database already contains the 'images' as an attribute. If so, this function updates the field. Otherwise, it includes the new
	 * setting to the database.
	 * @$article: object that contains an article.
	 * @$images: string that contains the description of the images.
	 */
	function setArticleImages(&$article, $images) {
		if(!$this->settingExists($article->getArticleId(), "images")) $this->insertArticleImages($article, $images);
		else $this->updateArticleImages($article, $images);
	}

	/*
	 * Counts the number of images that contains an article. In order to obtain an acurate result, the function 'insertArticleImages' must be called
	 * previously.
	 * @$articleId: article identifier ($article->getArticleId()).
	 */
	function countImagesByArticleId($articleId){
		$locale = Locale::getPrimaryLocale();
		$result = &$this->retrieve('SELECT setting_value FROM article_settings WHERE 
									setting_name = ? AND 
									article_id = ? AND 
									locale = ?', 
									array("images", $articleId, $locale)
								   );
		return $result->RecordCount();
	}
}

?>
