**AppModelsExtras for my projects based on IdiORM and Slim for PHP < 5.3**

Joseluis Laso <info@joseluislaso.es>

Clone project
```
cd app/models
git clone git://github.com/jlaso/AppModelsExtras.git extras

```
folder hierarchy
```
   app
     - models
         - extras (this module)
            - pagination
            - search

   index.php   -> here put require "app/models/extras/autoload.php"

```

If your master project contains this as a submodule and when you have clone the master forgot
--recursive clause, you can retrieve all submodule typing:
```
git submodule init
git submodule update
```

## Instructions

First put a line in your bootstrap index.php that require "app/models/extras/autoload.php"
For projects written in PHP >= 5.3, I have another repo.


**For use in controller**

```
$app->map('/backend/entity/list(/:page)','backend_entity_list')
    ->via('GET','POST')
    ->name('backend_entity_list');
function backend_entity_list($page = 1)
{
    $app = Slim::getInstance();

    if ($app->request()->isPost()) {
        $search = $app->request()->post('search');
        $qb     = new SearchQueryBuilder(null,$search);
        $qb->buildQuery();
        $query  = $qb->getQuery();
        $params = $qb->getParams();
    }else{
        $search = null;
        $query  = null;
        $params = null;
    }
    $paginator = new Paginable('Entity', array(
        'query'     => $query,
        'params'    => $params,
        'recPerPage'=>10
    ));

    $paginator->setBaseRouteAndParams('backend_entity_list');
    if (($page > 1) && ($page > $paginator->getPages())) {
        $app->notFound();
    }

    $paginator->setCurrentPage($page);

    $items = $paginator->getResults();
    $app->render('backend/entity/list.html.twig',array(
        'items'         => $items,
        'paginator'     => $paginator,
    ));
}
```

**How in twig template**

```
<div class="row-fluid">
    <div>
        <legend class="span4">Entity List</legend>
        {% if paginator.needPagination %}
            {{ paginator_backend_render(paginator) }}
        {% endif %}
    </div>
</div>

```
