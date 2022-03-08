<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Form\CategoryType;
use App\Form\SubCategoryType;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/category', name: 'category')]
    #[Route('/editCategory/{id}', name: 'editCategory')]
    public function category(Request $request, EntityManagerInterface $manager, CategoryRepository $repository, $id = null){

        $categories = $repository->findAll();

        if (!empty($id)) {
            $category = $repository->find($id);
        } else {
            $category = new Category();
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($category);
            $manager->flush();
            if (!empty($id)) {
                $this->addFlash('success', 'Catégorie modifiée');
            } else {
                $this->addFlash('success', 'Catégorie ajoutée');
            }

            return $this->redirectToRoute('category');
        }



        return $this->render('admin/category.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'titre' => 'Gestion catégories'
        ]);
    }

    #[Route('/deleteCategory/{id}', name: 'deleteCategory')]
    public function deleteCategory(Category $category, EntityManagerInterface $manager)
    {
        $manager->remove($category);
        $manager->flush();
        $this->addFlash('success', 'Catégorie supprimée');
        return $this->redirectToRoute('category');
    }

    #[Route('/subcategory', name: 'subCategory')]
    #[Route('/editSubCategory/{id}', name: 'editSubCategory')]
    public function categorie(Request $request, EntityManagerInterface $manager, SubCategoryRepository $repository, $id = null)
    {

        $subCategories = $repository->findAll();

        if (!empty($id)) {
            $subCategory = $repository->find($id);
        } else {
            $subCategory = new SubCategory();
        }

        $form = $this->createForm(SubCategoryType::class, $subCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($subCategory);
            $manager->flush();
            if (!empty($id)) {
                $this->addFlash('success', 'Sous-catégorie modifiée');
            } else {
                $this->addFlash('success', 'Sous-catégorie ajoutée');
            }

            return $this->redirectToRoute('subCategory');
        }


        return $this->render('admin/subCategory.html.twig', [
            'form' => $form->createView(),
            'subCategories' => $subCategories,
            'titre' => 'Gestion sous-catégories'
        ]);
    }

    #[Route('/deleteSubCategory/{id}', name: 'deleteSubCategory')]
    public function deleteSubCategory(SubCategory $subCategory, EntityManagerInterface $manager)
    {
        $manager->remove($subCategory);
        $manager->flush();
        $this->addFlash('success', 'sous-catégorie supprimée');
        return $this->redirectToRoute('category');
    }
    


}
