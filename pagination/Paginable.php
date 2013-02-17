<?php

/**
 *
 * @author Joseluis Laso <info@joseluislaso.es>
 *
 *
 * Pagination system for my Slim projects in PHP 5.2.x
 *
 */

/**
 * show sample in twig template
 * ============================
 *
 * <div class="pagination span8" id="paginator" style="text-align:right;">
 *  <ul>
 *      {% for page in 1..paginator.pages %}
 *      <li>
 *          <a href="{{ paginator.routeForPage(page) }}">{{ page }}</a>
 *      </li>
 *      {% endfor %}
 *  </ul>
 * </div>
 */
class Paginable implements PaginableInterface
{

    private $entity;

    private $query;

    private $params;

    private $order;

    private $page;

    private $pages;

    private $nbRecords;

    private $recPerPage;

    private $route;

    private $routeParams;

    /**
     * Generates a paginator from the ORMWrapper specified with ten records per page as default
     *
     * @param ORMWrapper $ormWrapper
     * @param int $recPerPage
     */
    public function __construct($entity, $options = array())
    {

        $options = array_merge(array(
            'query'     => null,
            'params'    => null,
            'recPerPage'=> 10,
            'order'     => null,
        ),$options);
        $this->entity     = $entity;
        $this->query      = $options['query'];
        $this->params     = $options['params'];
        $this->order      = $options['order'] ?: 'id:asc';

        if ($this->query && $this->params) {
            $this->nbRecords  = BaseModel::factory($entity)
                ->where_raw($this->query,$this->params)
                ->count();
        }else{
            $this->nbRecords  = BaseModel::factory($entity)->count();
        }

        $this->setNumRecPerPage($options['recPerPage']);

    }

    /**
     * Returns the items for the page selected
     *
     * @return ORM ArrayCollection
     */
    public function getResults()
    {
        if ($this->page>0) {

            $order         = explode(':', $this->order);
            $orderSentence = 'order_by_' . $order[1];
            $orderField    = $order[0];

            $start  = ($this->page-1) * $this->recPerPage;

            if (($start >= 0) && ($this->recPerPage > 0)){

                if ($this->query && $this->params) {
                    $result = BaseModel::factory($this->entity)
                        ->where_raw($this->query,$this->params)
                        ->offset($start)
                        ->$orderSentence($orderField)
                        ->limit($this->recPerPage)
                        ->find_many();
                    $log = ORM::get_query_log();
                    $qry = ORM::get_last_query();
                    return $result;
                }else{
                    return BaseModel::factory($this->entity)
                        ->offset($start)
                        ->$orderSentence($orderField)
                        ->limit($this->recPerPage)
                        ->find_many();
                }

            }
        }

    }

    /**
     * Set the records per page desired
     *
     * @param int $num
     */
    public function setNumRecPerPage($num)
    {

        $this->recPerPage = $num;
        $this->pages      = intval(ceil($this->nbRecords / $this->recPerPage));

    }

    /**
     * sets the current page
     *
     * @param $page
     */
    public function setCurrentPage($page)
    {
        $page = intval($page);
        if (!is_int($page)) {
            throw new Exception('El número de página indicado no es correcto');
        }

        if ($page < 1) {
            throw new Exception('El número de página indicado no es correcto');
        }

        $this->page = $page;

    }

    /**
     * obtains total page number
     *
     * @return mixed
     */
    public function  getPages()
    {
        return $this->pages;
    }

    /**
     * obtains the current page
     * @return mixed
     *
     */
    public function getCurrentPage()
    {
        return $this->page;
    }

    /**
     * Set the base route and params to generate route for each page
     *
     * @param $route
     * @param $params
     */
    public function setBaseRouteAndParams($route, $params = array())
    {
        $this->route       = $route;
        $this->routeParams = $params;
    }

    /**
     * returns the route for specified page
     *
     * @param int $num
     * @return string
     */
    public function getRouteForPage($num)
    {

        /** @var $app Slim */
        $app = Slim::getInstance();

        $params = array_merge(array('page'=>intval($num)),$this->routeParams);
        return $app->urlFor($this->route, $params);

    }

    public function needPagination()
    {
        return $this->nbRecords > $this->recPerPage;
    }


    public function hasPreviousPage()
    {
        return $this->page > 1;
    }

    public function getPreviousPage()
    {
        if (!$this->hasPreviousPage()) {
            throw new LogicException('There is not previous page.');
        }

        return $this->page - 1;
    }

    public function hasNextPage()
    {
        return $this->page < $this->pages;
    }

    public function getNextPage()
    {
        if (!$this->hasNextPage()) {
            throw new LogicException('There is not next page.');
        }

        return $this->page + 1;
    }

    public function getNbRecords()
    {

        return $this->nbRecords;

    }

    /**
     * Get current page
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }



}