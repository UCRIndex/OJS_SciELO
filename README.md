# OJS_Scielo Plugin
Modificación del plugin "native" para exportar a SciELO.

Dudas sobre el esquema SciELO:
==============================

¿Sponsor (OJS) se refiere a lo mismo que Funding source (SciELO)? El nodo: journal-id journal-id-type="nlm-ta", ¿puede ser un entero? ¿A qué hace referencia el nodo: award-id?

Pendiende (en proceso):
=======================

Se intentó completar el nodo publisher-name (plugins/importexport/ojs2scielo/Ojs2scieloExportDom.inc.php: línea 261) mediante el método:

	foreach ($article->getSuppFiles() as $suppFile) {
			if (is_array($suppFile->getPublisher(null))) foreach ($suppFile->getPublisher(null) as $locale => $publisher) {
				if($publisher !== '') { // El contenido de $publisher tampoco es NULL (ya se verificó)
					$publisherNameNode =& XMLCustomWriter::createChildWithText($doc, $publisherNode, 'publisher-name', $publisher, false); // Publisher-name node.
					unset($publisherNameNode);
				} else {
					$publisherNameNode =& XMLCustomWriter::createChildWithText($doc, $publisherNode, 'publisher-name', 'addpublisher-name', false); // Publisher-name node.
				}
			}
		}

Pero el nodo desaparece por completo indicando un error, esto ocurre debido a que todavía no se conoce el valor que contiene la variable $publisher luego de su asignación.

Respuesta al problema anterior:
===============================

No se puede utilizar la función "getSuppFiles". Todavía no de ha determinado la razón.
