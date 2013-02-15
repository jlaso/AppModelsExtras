<?php

/**
 *
 * @author Joseluis Laso <info@joseluislaso.es>
 *
 *
 * Search system for my Slim projects in PHP 5.2.x
 *
 */

interface SearchQueryBuilderInterface
{


    public function __construct(array $form, array $data);

    public function buildQuery();

    public function getQuery();

    public function getParams();


}