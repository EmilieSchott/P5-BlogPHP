<?php

namespace EmilieSchott\BlogPHP\Paginator;

trait Paginator
{
    public function paginator(array $datas, int $itemsPerPage): array
    {
        $datasPages = \array_chunk($datas, $itemsPerPage);
        \array_unshift($datasPages, '');
        unset($datasPages[0]);
        $pagesNbr = \array_key_last($datasPages);

        $paginator = [
        'datasPages' => $datasPages,
        'pagesNbr' => $pagesNbr
        ];

        return $paginator;
    }

    public function displayPage(array $datasPages, int $page): array
    {
        $offset = $page - 1;
        $datasPage = array_slice($datasPages, $offset, 1);
        $datasPage = $datasPage[0];

        return $datasPage;
    }
}
