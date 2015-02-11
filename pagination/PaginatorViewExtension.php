<?php

/**
 *
 * @author Joseluis Laso <jlaso@joseluislaso.es>
 *
 *
 * Pagination system for my Slim projects in PHP 5.2.x
 *
 */

class PaginatorViewExtension
{


    /**
     * Shows the paginator
     *
     * @param PaginableInterface $paginator
     * @return mixed
     */
    public static function render(PaginableInterface $paginator)
    {
        $pagination = new PaginationRender($paginator);
        return $pagination->render();
    }


}

