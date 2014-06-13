# OJS_Scielo Plugin
Modificación del plugin "native" para exportar a SciELO.

Dudas sobre el esquema SciELO:
==============================

- ¿Sponsor (OJS) se refiere a lo mismo que Funding source (SciELO)?
- El nodo: journal-id journal-id-type="nlm-ta", ¿puede ser un entero?
- ¿A qué hace referencia el grupo de nodos: award-group?
- ¿La fecha del nodo: date date-type="received" (SciELO) equivale a la fecha: DateNotified (OJS)?

Pendientes:
===========

- article-type
- article-id pub-id-type="doi"
- subject
- named-content content-type="zipcode" (*)
- named-content content-type="city" (*)
- named-content content-type="state" (*)
- addr-line (*)
- email
- fpage
- lpage
- history
- permissions
- funding-group
- counts
- body (contenido del artículo)
- back (referencias)

(*) No se encontraron en ningún objeto (deben ser solicitados al usuario en una futura versión).
