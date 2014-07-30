# OJS_Scielo Plugin
<p align="justify">Modificación del plugin "native" para exportar a SciELO.</p>

Dudas sobre el esquema SciELO:
==============================

- ¿Sponsor (OJS) se refiere a lo mismo que Funding source (SciELO)?
- El nodo: journal-id journal-id-type="nlm-ta", ¿puede ser un entero?
- ¿A qué hace referencia el grupo de nodos: award-group?
- ¿La fecha del nodo: date date-type="received" (SciELO) equivale a la fecha: DateNotified (OJS)?

Pendientes:
===========

- header: url, mml, type
- article-id pub-id-type="doi"
- subject
- named-content content-type="zipcode" (*)
- named-content content-type="city" (*)
- named-content content-type="state" (*)
- addr-line (*)
- email
- fpage
- lpage
- permissions
- kwd-group
- funding-group
- history
- counts
- body (contenido del artículo)
- back (referencias)

<p align="justify">(*) No se encontraron en ningún objeto (deben ser solicitados al usuario en una futura versión).</p>

Agregar un condicional para que se pueda exportar el XML en caso de que el campo no exista.
