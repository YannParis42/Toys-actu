<?php

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php;

class SearchNews
{
    private ?string $search;
    private ?Category $categories=null;

    public function getSearch(): string {return $this->search;}
    public function setSearch(string $search): self { $this->search = $search; return $this;}

    public function getCategories(): ?Category  {return $this->categories;}
    public function setCategories(Category $categories): self { $this->categories = $categories; return $this;}

}