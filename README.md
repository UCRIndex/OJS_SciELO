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

<p align="justify">Existen errores al intentar implementar el menú para incluír los nuevos campos. Actualmente se están efectuando pruebas para recuperar la información desde el plugin ArticlesExtras, el cual se encuentra instalado en el servidor de pruebas (solución provisional).</p>

Actualización 04/07/2014:
=========================

<p align="justify">Se intentó agregar un menú similar al del plugin ArticlesExtras con el objetivo de agregar la información faltante. Se creó correctamente; sin embargo, no fue posible administrar las funciones de cada ítem. Es decir, en el plugin actual se puede mostrar el tpl correspondiente a cada ítem del menú pero no se puede recuperar la información correspondiente al número seleccionado. Al parecer, el plugin ArticlesExtras, por ser de una clase diferente (generic), tiene la posiblididad de administrar las opciones del menú mediante una clase Handler. Por medio de ella, cuando se selecciona un vínculo (autores, cuerpo, citas, etc.), se captura el evento y así se puede obtener la información (autores, revista y número) correspondiente a la selección del usuario. Esto se intentó implementar en el plugin ojs2scieloImportExportPlugin; no obstante, el registro de la clase Handler debe efectuarse en el registro de cada plugin, y como ambos son de clases diferenes, su fase de registro busca elementos distintos. En el regisro del ojs2scieloImportExportPlugin no se está registrando la clase Handler y, por lo tanto, los eventos del menú se están tomando solamente como vínculos a los tpl (el repositorio actual contiene las clases correspondientes al Handler y DAO que no son registradas por el OJS).</p>
<p align="justify">Así mismo, se efectuaron pruebas con el plugin ArticlesExtras con el fin de aprovechar sus tpl y el DAO. Se comprobó que dicho plugin puede llegar a ser compatible con el ojs2scieloImportExportPlugin. En este momento, se puede editar el cuerpo de un artículo desde el ArticlesExtras y la información puede ser recuperada desde el ojs2scieloImportExportPlugin (aparecerá en el XML).</p>
<p align="justify">Debido a todo esto, es necesario modificar la estructura del plugin ojs2scieloImportExportPlugin. Parece ser obligatorio separarlo en dos fases: inserción de datos (plugin genérico) y exportar datos (plugin de importar/exportar). El problema de esta situación es que el plugin de exportación va a depender de que el editor de la revista complete correctamente todos los campos requeridos por el esquema SciELO cuando se agrega un número nuevo. Además, la instalación de ambos también deberá ser condicionada para que funcionen correctamente.</p>
<p>Para solucionar esto, se puede crear otro plugin basado en ArticlesExtras para completar la fase de ingreso de datos. Una idea sería incluír el nuevo plugin dentro del quicksubmit para simplificar el proceso de ingresar información. Esto puede hacerse como un complemento para no modificar la base del OJS. La otra opción sería obtener los datos directamente (por ejemplo, sacar el cuerpo del documento agregado).</p>

Actualización 15/07/2014:
=========================

<p align="justify">El plugin compartido que se encuentra en el repositorio funciona correctamente para exportar artículos individuales. Hasta el momento exporta el árbol de nodos (estructura) y algunos datos correspondientes a cada artículo, los cuales posiblemente han sido ingresados mediante el plugin quickSubmit. Los datos que se exportan en el archivo XML se obtienen por medio de los objetos DAO (básicos) descritos en la documentación del OJS.</p>
<p align="justify">A partir de esto, lo que se busca es agregar una nueva sección en el plugin con el objetivo de completar los nuevos datos que faltan en el árbol de nodos. Para lograrlo se intentó imitar el manejo de páginas que realiza el plugin ArticlesExtras. Esto se decidió debido a dos razones primordiales: la falta de información en el tema (poca documentación al respecto), similitudes en cuanto a la estructura y campos requeridos.</p>
<p align="justify">Así, se inició con el estudio del plugin como tal y se determinó que el manejo de las páginas se efectuaba por medio de una clase de tipo Handler (http://pkp.sfu.ca/ojs/doxygen/stable/html/classHandler.html). Cada evento del menú correspondía a un método de dicha clase y dentro de cada uno se obtiene el identificador de cada número para almacenar la información correctamente. Así mismo el plugin ArticlesExtras crea su propio DAO en el cual almacena los nuevos datos recopilados. Esta estructura se intentó implementar en el plugin OJS2SciELO; sin embargo, no ha sido posible que la clase de tipo Handler funcione correctamente. Se creó el menú pero al momento de manejar cada evento no se ejecutaba de la forma esperada. Se cree que el error ocurre en el registro del plugin. En este punto, no fue posible determinar si el error se debía a una incorrecta instalación (errores en el código) o que la clase de tipo Handler no puede ser implementada en los plugins de tipo import/export. No hay información suficiente (documentación) con la que se pueda afirmar que dicha clase no se acepte en los plugin de tipo import/export; sin embargo, como las estructuras de los plugins de import/export y los regulares es distinta a nivel de código, existe la posibilidad de que el error se deba a que la clase de tipo Handler no es soportada por los plugins de tipo import/export. Para solucionar esto una posibilidad sería estudiar otros plugins de import/export que manejen varias páginas y analizar la forma en la cual se manejan estos eventos e incluso comprobar si se pueden ingresar datos nuevos. Esto podría confirmar el tema de la clase Handler. Otra opción sería estudiar plugins similares y comprobar si el manejo de las páginas únicamente debería ser por este medio, sino analizar la otra forma en cómo puede implementarse. Por otra parte, podría descartarse por completo el ingreso y la exportación de datos en un mismo plugin y separar las dos acciones en distintos plugins. De esta forma se estaría imitando el funcionamiento de los plugins ArticlesExtras y OJS SciELO Export Plugin, los cuales trabajan complementariamente.</p>
<p align="justify">Una opción que se estudió durante los últimos días fue agregar campos al plugin quickSubmit para que el proceso de ingreso de datos fuera más transparente. Esto reduciría la carga del plugin de import/export; sin embargo, analizando el plugin de quickSubmit se determinó que los archivos tpl utilizados por él para presentar los datos son plantillas creadas dentro de la estructura de directorios del OJS. Por esta razón, como no se busca modificar los archivos originales del OJS, parece que esta opción debería descartarse al menos desde este punto de vista.</p>
<p align="justify">Se continúa estudiando la manera en cómo maneja los datos el plugin quickSubmit. Y se deben considerar las otras opciones mencionadas arriba.</p>

Actualización 16/07/2014:
=========================

<p align="justify">Se eliminó la clase de tipo Handler y el DAO creado dentro de este plugin para volver a la versión original del plugin. Además, se eliminaron los campos del cuerpo con los cuales se comprobaba la compatibilidad del plugin con el DAO creado por ArticlesExtras (pruebas).</p>
<p align="justify">Inició el análisis del plugin crossref con el fin de estudiar los puntos expuestos ayer. Se encontró que la estructura es muy similar a la actual y no cuenta con el manejo de eventos. No fue de utilidad.</p>
<p align="justify">Así mismo, se analizaron otros plugins de tipo import/export como por ejemplo el datacite, doaj, duracloud, erudit y medra y en ninguno de ellos se encontró el registro de una clase de tipo Handler para manejar eventos. De hecho, la estructura de estos plugins es muy similar a la del plugin estándar de import/export (sample), el cual en la fase de registro, solamente contempla los métodos de register, getName, getDisplayName, getDescription, display y usage. Por lo tanto, ninguno de estos plugins requiere el manejo de eventos mediante una clase de tipo Handle. El caso del plugin quickSubmit es distinto pues maneja datos nuevos; es necesario profundizar más en las funciones que permiten guardar información.</p>

Actualización 17/07/2014:
=========================

<p align="justify">Se encontró un error en la presentación de los nombres del plugin. Se intentó recuperar con versiones anteriores del repositorio; sin embargo, no se ha podido solucionar. El error parece estar en el servidor de pruebas pues no se ha modificado la estructura interna del plugin en las últimas semanas (el problema es a nivel local, en general, el plugin debería funcionar correctamente hasta lo especificado en la actualización del 15/07/2014).</p>
<p align="justify">Parece haber una incompatibilidad con el plugin OJS_Citas, ya que al exportar el XML sin dicho plugin se efectúa correctamente y con el plugin se genera un error. Por el momento, esta situación quedará como pendiente para conversar sobre la situación porque no se ha podido solucionar estos errores.</p>