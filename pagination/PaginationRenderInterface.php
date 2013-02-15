<?php

/**
 *
 * @author Joseluis Laso <info@joseluislaso.es>
 *
 *
 * Pagination system for my Slim projects in PHP 5.2.x
 *
 */

interface PaginationRenderInterface
{

    public function __construct(Paginable $paginator);

    public function setOptions(array $options = array());

    public function render();


}