<?php

namespace App\Service\News;

use App\Entity\Category;
use App\Entity\User;
use App\Models\News\NewsSearch;
use App\Repository\NewsRepository;

class NewsSearchProvider{

    public function __construct(private NewsRepository $newsRepository)
    {}

    // Récupere toutes les news
    public function getFrontNews(?NewsSearch $newsSearch = null): array
    {   
    $newsSearch = $newsSearch ?? new NewsSearch();
    return $this->newsRepository->search($newsSearch);
    }

    // Récupere les news par category
    public function getFrontNewsForCategory(Category $category): array
    {
        $newsSearch = (new NewsSearch())->setCategory($category);

        return $this->getFrontNews($newsSearch);
    }

    // Récupère les news par user
    public function getFrontNewsForUser(User $user): array
    {
    $newsSearch = new NewsSearch();
    $result = $newsSearch->setUser($user);

    return $this->getFrontNews($result);
    }

    // Récupère les news avec la recherche
    public function getFrontNewsByKeyword(string $keyword, ?Category $category=null):array
    {
        $newsSearch = new NewsSearch();
        $result = $newsSearch->setKeyword($keyword);
        if($category){
            $result = $newsSearch->setCategory($category);
        }
        return $this->getFrontNews($result);
    }
}
