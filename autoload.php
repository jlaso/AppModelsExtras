<?php


/**
 *
 * @author Joseluis Laso <info@joseluislaso.es>
 *
 *
 * Pagination and search system for my Slim projects in PHP 5.2.x
 *
 */

$__dir__ = dirname(__FILE__);

require_once $__dir__ . '/pagination/PaginableInterface.php';
require_once $__dir__ . '/pagination/Paginable.php';
require_once $__dir__ . '/pagination/PaginationRenderInterface.php';
require_once $__dir__ . '/pagination/PaginationRender.php';
require_once $__dir__ . '/pagination/PaginatorViewExtension.php';

require_once $__dir__ . '/search/SearchQueryBuilderInterface.php';
require_once $__dir__ . '/search/SearchQueryBuilder.php';