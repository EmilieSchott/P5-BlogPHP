<?php

namespace EmilieSchott\BlogPHP\Paginator;

trait Paginator
{
    public function paginate(array $datas, int $itemsPerPage): array
    {
        $datasPages = \array_chunk($datas, $itemsPerPage);
        \array_unshift($datasPages, '');
        unset($datasPages[0]);
        $pagesNumber = \array_key_last($datasPages);

        $pages = [
        'datasPages' => $datasPages,
        'pagesNumber' => $pagesNumber
        ];

        return $pages;
    }

    public function displayPage(array $pages, int $pageRequested): array
    {
        if (!is_null($pages['pagesNumber'])) {
            try {
                if ($pageRequested <= 0 || $pageRequested > $pages['pagesNumber']) {
                    throw new \Exception("La page indiquée n'existe pas.");
                }
            } catch (\Exception $e) {
                $datas['invalidPage'] = $e->getMessage();
                $pageRequested = 1;
            }

            $offset = $pageRequested - 1;
            $datasPage = array_slice($pages['datasPages'], $offset, 1);
            $datasPage = $datasPage[0];
    
            return $datasPage;
        } else {
            throw new \Exception("Il n'existe aucune donnée à afficher.");
        }
    }
}
