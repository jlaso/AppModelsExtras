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

    /**
     * The entity we are paginating
     *
     * @var string $entity
     */
    private $entity;

    /**
     * Query that must apply to table for paginate
     *
     * @var string $query
     */
    private $query;

    /**
     * Params of query
     *
     * @var string $params
     */
    private $params;

<<<<<<< HEAD
    /**
     * Current page
     *
     * @var int $page
     */
=======
    private $order;

>>>>>>> 4ffcf2d33c5846575ac2c608c201f8cdf31300de
    private $page;

    /**
     * Total pages
     *
     * @var int $pages
     */
    private $pages;

    /**
     * Total number of records applying query
     *
     * @var int $nbRecords
     */
    private $nbRecords;

    /**
     * Number of records have each page
     *
     * @var int $recPerPage
     */
    private $recPerPage;

    /**
     * Slim logic route that return to list, must have :page param
     *
     * @var string $route
     */
    private $route;

    /**
     * Slim route params (fixed, page not)
     *
     * @var mixed $routeParams
     */
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
        $this->order      = isset($options['order']) ? $options['order'] : 'id:asc';

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
        if ('last'==$page) {
            $db      = ORM::get_db();
            $last    = $db->query(sprintf("SELECT COUNT(*) FROM `%s`", strtolower($this->entity)))->fetch();
            $last    = $last[0];
            $page    = ceil($last / $this->recPerPage);

        }else{
            if (preg_match('/id-(\d+)/i', $page, $matches)) {
                //@TODO: optimize this section
                $id      = $matches[1];
                $db      = ORM::get_db();
                $records = $db->query(sprintf("SELECT `id` FROM `%s`", strtolower($this->entity)))->fetchAll();
                $page    = 1;
                for ($index=0,$len=count($records); $index<$len; $index++) {
                    if ($records[$index]['id'] == $id) {
                        break;
                    }
                    if ($index % $this->recPerPage === 0) {
                        $page++;
                    }
                }
            }else{
                $page = intval($page);
                if (!is_int($page)) {
                    throw new Exception('El número de página indicado no es correcto');
                }

                if ($page < 1) {
                    throw new Exception('El número de página indicado no es correcto');
                }
            }
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

    /**
     * Indicates if table with current query has more than one page
     *
     * @return bool
     */
    public function needPagination()
    {
        return $this->nbRecords > $this->recPerPage;
    }


    /**
     * Indicates if we are not in a first page
     *
     * @return bool
     */
    public function hasPreviousPage()
    {
        return $this->page > 1;
    }

    /**
     * Get number of previous page
     *
     * @return int
     *
     * @throws LogicException
     */
    public function getPreviousPage()
    {
        if (!$this->hasPreviousPage()) {
            throw new LogicException('There is not previous page.');
        }

        return $this->page - 1;
    }

    /**
     * Indicates if we are not in the last page
     *
     * @return bool
     */
    public function hasNextPage()
    {
        return $this->page < $this->pages;
    }

    /**
     * Get the next page
     *
     * @return int
     *
     * @throws LogicException
     */
    public function getNextPage()
    {
        if (!$this->hasNextPage()) {
            throw new LogicException('There is not next page.');
        }

        return $this->page + 1;
    }

    /**
     * Get Number of Records (applying query)
     *
     * @return int
     */
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