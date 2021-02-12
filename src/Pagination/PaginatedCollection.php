<?php

namespace App\Pagination;

class PaginatedCollection
{
    private $items;
    private $total;
    private $count;
    private $page;
    private $_links = [];

    public function __construct ($items, $total, $page)
    {
        $this->total = $total;
        $this->items = $items;
        $this->page  = $page;
        $this->count = count($items);
    }

    public function addLink($ref, $url)
    {
        $this->_links[$ref] = $url;
    }
}