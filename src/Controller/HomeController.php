<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use App\Service\News\NewsSearchProvider;

class HomeController extends AbstractController
{

    public function __construct(private NewsRepository $newsRepository, private CategoryRepository $categoryRepository, private UserRepository $userRepository){}

    #[Route('/', name: 'app_home')]
    public function findByDate(Request $request, NewsSearchProvider $newsSearchProvider): Response{
        $category = null;

        // formulaire de recherche 
        $form = $this->createForm(SearchType::class);
        $form ->handleRequest($request);
        if($form->isSubmitted() && $form->isvalid()){

            if($form->getData()){
                $keyword = $form->getData()->getSearch();
                $category = $form->getData()->getCategories();
                $news = $newsSearchProvider->getFrontNewsByKeyword($keyword, $category);
            }

            if(null == $news){
                return $this->renderForm('home/index.html.twig', [
                    'news' => null,
                    'search_form' => $form
                ]);
            }
            if($news){
                return $this->renderForm('home/index.html.twig', [
                    'news' => $news,
                    'search_form' => $form
                ]);
        }}

        return $this->renderForm('home/index.html.twig', [
            'news' => $newsSearchProvider->getFrontNews(),
            'search_form' => $form
        ]);
    }

    #[Route('/admin/{id}', name: 'app_admin_news')]
    public function indexAdmin(): Response{
        $news = $this->newsRepository->countNews();
        $users = $this->userRepository->countUser();
        $categories = $this->categoryRepository->countCat();

        return $this->render('back/admin_index.html.twig', [
            'news' => $news,
            'users' => $users,
            'categories' => $categories,
        ]);
    }
}