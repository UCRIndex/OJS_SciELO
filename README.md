# OJS_Scielo Plugin
Modificación del plugin "native" para exportar a SciELO.

Dudas sobre el esquema SciELO:
==============================

- ¿Sponsor (OJS) se refiere a lo mismo que Funding source (SciELO)?
- El nodo: journal-id journal-id-type="nlm-ta", ¿puede ser un entero?
- ¿A qué hace referencia el grupo de nodos: award-group?
- ¿La fecha del nodo: date date-type="received" (SciELO) equivale a la fecha: DateNotified (OJS)?

Pendientes (excluyendo body y back):
====================================

- article-type (encabezado)
- article-id pub-id-type="doi" (article-meta)
- subject (subj-group subj-group-type="heading")
- named-content content-type="zipcode" (institution content-type="orgname")
- named-content content-type="city" (institution content-type="orgname")
- named-content content-type="state" (institution content-type="orgname")
- addr-line (institution content-type="orgname")
- email (author-notes)
- fpage (author-notes)
- lpage (author-notes)
- history
- permissions
- funding-group
- counts
