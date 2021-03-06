<?php

namespace App\Pagination;

use JMS\Serializer\Annotation as Serializer;

class PaginatedCollection
{
    /**
     * @Serializer\Groups({"products_list", "users_list"})
     */
    private $items;
    /**
     * @Serializer\Groups({"products_list", "users_list"})
     */
    private $total;
    /**
     * @Serializer\Groups({"products_list", "users_list"})
     */
    private $count;
    /**
     * @Serializer\Groups({"products_list", "users_list"})
     */
    private $page;
    /**
     * @Serializer\Groups({"products_list", "users_list"})
     */
    private $_links = [];

    public function __construct($items, $total, $page)
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
