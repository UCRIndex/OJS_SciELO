# OJS_Scielo Plugin
<p>Modificación del plugin "native" para exportar a SciELO.</p>

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

(*) No se encontraron en ningún objeto (deben ser solicitados al usuario en una futura versión).

- Existen errores al intentar implementar el menú para incluír los nuevos campos. Actualmente se están efectuando pruebas para recuperar la información desde el plugin ArticlesExtras, el cual se encuentra instalado en el servidor de pruebas (solución provisional).

Actualización 04/07/2014:
=========================

<p>Se intentó agregar un menú similar al del plugin ArticlesExtras con el objetivo de agregar la información faltante. Se creó correctamente; sin embargo, no fue posible administrar las funciones de cada ítem. Es decir, en el plugin actual se puede mostrar el tpl correspondiente a cada ítem del menú pero no se puede recuperar la información correspondiente al número seleccionado. Al parecer, el plugin ArticlesExtras, por ser de una clase diferente (generic), tiene la posiblididad de administrar las opciones del menú mediante una clase Handler. Por medio de ella, cuando se selecciona un vínculo (autores, cuerpo, citas, etc.), se captura el evento y así se puede obtener la información (autores, revista y número) correspondiente a la selección del usuario. Esto se intentó implementar en el plugin ojs2scieloImportExportPlugin; no obstante, el registro de la clase Handler debe efectuarse en el registro de cada plugin, y como ambos son de clases diferenes, su fase de registro busca elementos distintos. En el regisro del ojs2scieloImportExportPlugin no se está registrando la clase Handler y, por lo tanto, los eventos del menú se están tomando solamente como vínculos a los tpl (el repositorio actual contiene las clases correspondientes al Handler y DAO que no son registradas por el OJS).</p>
<p>Así mismo, se efectuaron pruebas con el plugin ArticlesExtras con el fin de aprovechar sus tpl y el DAO. Se comprobó que dicho plugin puede llegar a ser compatible con el ojs2scieloImportExportPlugin. En este momento, se puede editar el cuerpo de un artículo desde el ArticlesExtras y la información puede ser recuperada desde el ojs2scieloImportExportPlugin (aparecerá en el XML).</p>
<p>Debido a todo esto, es necesario modificar la estructura del plugin ojs2scieloImportExportPlugin. Parece ser obligatorio separarlo en dos fases: inserción de datos (plugin genérico) y exportar datos (plugin de importar/exportar). El problema de esta situación es que el plugin de exportación va a depender de que el editor de la revista complete correctamente todos los campos requeridos por el esquema SciELO cuando se agrega un número nuevo. Además, la instalación de ambos también deberá ser condicionada para que funcionen correctamente.</p>
<p>Para solucionar esto, se puede crear otro plugin basado en ArticlesExtras para completar la fase de ingreso de datos. Una idea sería incluír el nuevo plugin dentro del quicksubmit para simplificar el proceso de ingresar información. Esto puede hacerse como un complemento para no modificar la base del OJS. La otra opción sería obtener los datos directamente (por ejemplo, sacar el cuerpo del documento agregado).</p>
