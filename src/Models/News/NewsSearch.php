<?php
namespace App\Models\News;

use App\Entity\Category;
use App\Entity\User;

class NewsSearch{
    private ?Category $category= null;
    private ?User $user=null;
    private ?string $keyword=null;

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): self
    {
        $this->keyword =$keyword;
        
        return $this;
    }
}
