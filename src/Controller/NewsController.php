<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\News;
use App\Entity\User;
use App\Form\NewsType;
use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use App\Service\News\NewsSearchProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/news')]
class NewsController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em,private NewsRepository $newsRepository ,private CategoryRepository $categoryRepository, private UserRepository $userRepository){}

    #[Route('/', name: 'app_news_index', methods: ['GET'])]
    public function index(NewsSearchProvider $newsSearchProvider): Response
    {
        return $this->render('back/news/index.html.twig', [
            'news' => $newsSearchProvider->getFrontNews(),
        ]);
    }

    #[Route('/category/{id}', name: 'app_news_category')]
    public function findByCategories(Category $category, NewsSearchProvider $newsSearchProvider): Response
    {
        $news = $newsSearchProvider->getFrontNewsForCategory($category);
        return $this->render('news/category.html.twig', [
            'categorie'=>$category,
            'actu' => $news
                    ]);
    }

    #[Route('/user/{id}', name: 'app_news_user')]
    public function findByUser(User $user, NewsSearchProvider $newsSearchProvider): Response
    {
        $newsUser = $newsSearchProvider->getFrontNewsForUser($user);
        return $this->render('news/news_user.html.twig', [
            'actuUser'=>$newsUser,
        ]);
    }

    #[Route('/new', name: 'app_news_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $news = new News();
        $news->setReleaseDate(new \DateTimeImmutable());
        $news->setCreatedBy($user);
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('news_photo')->getData();
            // $newsRepository->add($news, true);
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $news->setNewsPhoto($newFilename);

            }

            $this->em->persist($news);
            $this->em->flush();
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('news/new.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    #[Route('/newAdmin', name: 'app_news_new_admin', methods: ['GET', 'POST'])]
    public function newAdmin(Request $request, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $news = new News();
        $news->setReleaseDate(new \DateTimeImmutable());
        $news->setCreatedBy($user);
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('news_photo')->getData();
            // $newsRepository->add($news, true);
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $news->setNewsPhoto($newFilename);
                

            }

            $this->em->persist($news);
            $this->em->flush();
            return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('back/news/new_admin.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_news_show', methods: ['GET'])]
    public function show(News $news): Response
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_news_show_admin', methods: ['GET'])]
    public function showAdmin(News $news): Response
    {
        return $this->render('back/news/show_admin.html.twig', [
            'news' => $news,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_news_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, News $news, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('POST_EDIT', $news);
        $news->setReleaseDate(new \DateTimeImmutable());
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        $oldFileName = $news->getNewsPhoto();

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('news_photo')->getData();
            // $newsRepository->add($news, true);
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                //suppression ancienne photo si nouvelle
                if($oldFileName and $oldFileName !== $newFilename){
                        $nomPhoto = $this->getParameter('photo_directory').'/'.$oldFileName;
                        if(file_exists($nomPhoto)){
                            unlink($nomPhoto);
                        }
                }

                try {
                    $photo->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $news->setNewsPhoto($newFilename);
                

            }

            $this->em->persist($news);
            $this->em->flush();
            return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/news/edit.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edituser', name: 'app_news_edit_user', methods: ['GET', 'POST'])]
    public function editUser(Request $request, News $news, SluggerInterface $slugger, Security $security): Response
    {
        if (!$security->isGranted('POST_EDIT', $news)) {
        return $this->redirectToRoute('app_home');
        } 
        $news->setReleaseDate(new \DateTimeImmutable());
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        $oldFileName = $news->getNewsPhoto();

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('news_photo')->getData();
            // $newsRepository->add($news, true);
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                //suppression ancienne photo si nouvelle
                if($oldFileName and $oldFileName !== $newFilename){
                        $nomPhoto = $this->getParameter('photo_directory').'/'.$oldFileName;
                        if(file_exists($nomPhoto)){
                            unlink($nomPhoto);
                        }
                }

                try {
                    $photo->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $news->setNewsPhoto($newFilename);
                

            }

            $this->em->persist($news);
            $this->em->flush();
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('news/edit.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_news_delete', methods: ['POST'])]
    public function delete(Request $request, News $news, NewsRepository $newsRepository): Response
    {
        $photo = $news->getNewsPhoto();
        
        if($photo){
            $nomPhoto = $this->getParameter('photo_directory').'/'.$photo;
            if(file_exists($nomPhoto)){
                unlink($nomPhoto);
            }
         }

        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $newsRepository->remove($news, true);
        }

        return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_news_delete_user', methods: ['POST'])]
    public function deleteByUser(Request $request, News $news, NewsRepository $newsRepository): Response
    {
           $photo = $news->getNewsPhoto();
        
        if($photo){
            $nomPhoto = $this->getParameter('photo_directory').'/'.$photo;
            if(file_exists($nomPhoto)){
                unlink($nomPhoto);
            }
        }

        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $newsRepository->remove($news, true);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}