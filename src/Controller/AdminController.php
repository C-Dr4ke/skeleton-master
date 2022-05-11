<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Form\SubCategoryType;
use App\Repository\AddressRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminController extends AbstractController
{
   
    //************************************************************************************/
    //*********************************CATEGORIES****************************************//
    //***********************************************************************************/

    #[Route('/category', name: 'category')]
    #[Route('/editCategory/{id}', name: 'editCategory')]
    #[IsGranted('ROLE_ADMIN')]
    public function category(Request $request, EntityManagerInterface $manager, CategoryRepository $repository, $id = null)
    {
        //Permet de récupérer toutes les catégories
        $categories = $repository->findAll();
        
        // Permet de récuperer un élément de la table si il existe sinon il crée un nouvel élément
        if (!empty($id)) {
            $category = $repository->find($id);
        } else {
            $category = new Category();
        }

        // Formulaire de création d'une nouvelle entrée dans la table "category"
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        // Si tout est valide on entre tout dans la BDD
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
    #[IsGranted('ROLE_ADMIN')]
    public function deleteCategory(Category $category, EntityManagerInterface $manager)
    {   
        // Supprime un élément de la table "category"
        $manager->remove($category);
        $manager->flush();
        $this->addFlash('success', 'Catégorie supprimée');
        return $this->redirectToRoute('category');
    }


    //************************************************************************************/
    //*********************************SOUS-CATEGORIES***********************************//
    //***********************************************************************************/


    #[Route('/subcategory', name: 'subCategory')]
    #[Route('/editSubCategory/{id}', name: 'editSubCategory')]
    #[IsGranted('ROLE_ADMIN')]
    public function categorie(Request $request, EntityManagerInterface $manager, SubCategoryRepository $repository, $id = null)
    {
        //Permet de récupérer toutes les sous-catégories
        $subCategories = $repository->findAll();
        
         // Permet de récuperer un élément de la table si il existe sinon il crée un nouvel élément
        if (!empty($id)) {
            $subCategory = $repository->find($id);
        } else {
            $subCategory = new SubCategory();
        }

        // Formulaire de création d'une nouvelle entrée dans la table "subCategory"
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
    #[IsGranted('ROLE_ADMIN')]
    public function deleteSubCategory(SubCategory $subCategory, EntityManagerInterface $manager)
    {   
        // Supprime un élément de la table "category"
        $manager->remove($subCategory);
        $manager->flush();
        $this->addFlash('success', 'sous-catégorie supprimée');
        return $this->redirectToRoute('category');
    }


    //************************************************************************************/
    //*********************************MENU***********************************//
    //***********************************************************************************/


    #[Route('/addMenu', name: 'addMenu')]
    #[IsGranted('ROLE_ADMIN')]
    public function addMenu(Request $request, EntityManagerInterface $manager)
    {
        // Ajout d'un nouveau menu
        $product = new Product();

        // Formulaire de création d'une nouvelle entrée dans la table "Menu"
        $form = $this->createForm(ProductType::class, $product, ['add' => true]);
        $form->handleRequest($request);
      
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les informations sur la photo que l'on à ajouté
            $file = $form->get('picture')->getData();

            // Si on à ajouter une photo alors le nom du fichier est modifier par la date du jour + un uniqid
            if ($file) {
                $fileName = date('YmdHis') . '-' . uniqid() . '-' . $file->getClientOriginalName();
                try {
                    $file->move($this->getParameter('upload_directory'), $fileName);
                } catch (FileException $exception) {
                    $this->redirectToRoute('addMenu', [
                        'erreur' => $exception
                    ]);
                }
                $product->setPicture($fileName);
                $manager->persist($product);
                $manager->flush();
                $this->addFlash('success', 'Le menu a bien été enregistré');
                return $this->redirectToRoute('listMenus');
            }
        }
        return $this->render('admin/addMenu.html.twig', [
            'form' => $form->createView(),
            'titre' => 'Ajout de menu'
        ]);
    }

    
   
    #[Route('/listMenus', name: 'listMenus')]
    #[IsGranted('ROLE_ADMIN')]
    public function listProduct(ProductRepository $repository)
    {   
        // Récupération de la liste de tout les menus
        $products = $repository->findAll();
       
        return $this->render('admin/listMenus.html.twig', [
            'products' => $products
        ]);
    }


    #[Route('/editMenu/{id}', name: 'editMenu')]
    #[IsGranted('ROLE_ADMIN')]
    public function editMenu(Request $request, EntityManagerInterface $manager, Product $product)
    {   
        // Crée un formulaire pour éditer le menu
        $form = $this->createForm(ProductType::class, $product,['edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère le nom de fichier de la nouvelle photo
            $file = $form->get('editPicture')->getData();
            // Remplace l'ancienne photo et le nom de fichier de l'ancienne photo par le nouveau
            if ($file) {
                $fileName = date('YmdHis') . '-' . uniqid() . '-' . $file->getClientOriginalName();

                try {
                    $file->move($this->getParameter('upload_directory'), $fileName);
                    unlink($this->getParameter('upload_directory') . '/' . $product->getPicture());
                } 
                catch (FileException $exception) {
                    $this->redirectToRoute('editProduct', [
                        'erreur' => $exception
                    ]);
                }
                $product->setPicture($fileName);
            }
            $manager->persist($product);
            $manager->flush();
            $this->addFlash('success', 'Le menu a bien été modifié');
            return $this->redirectToRoute('listMenus');
        }
        return $this->render('admin/editMenu.html.twig', [
            'form' => $form->createView(),
            'titre' => 'Modification du menu',
            'product' => $product
        ]);
    }

    #[Route('/deleteMenu/{id}', name: 'deleteMenu')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteProduct(EntityManagerInterface $manager, Product $product)
    {   
        // Sépare le fichier photo du dossier "upload_directory"
        unlink($this->getParameter('upload_directory') . '/' . $product->getPicture());
        // Suppression du produit
        $manager->remove($product);
        $manager->flush();

        $this->addFlash('success', 'Le menu a bien été supprimé');
        return $this->redirectToRoute('listMenus');
    }

     //************************************************************************************/
    //*********************************Commande*******************************************/
    //***********************************************************************************/

    
    #[Route('/orderList', name: 'orderList')]
    #[IsGranted('ROLE_ADMIN')]
    public function orderList(OrderRepository $repository)
    {
        // Récuperation de toutes les commandes
        $orders = $repository->findAll();

        return $this->render('admin/orderList.html.twig', [
            'orders' => $orders
        ]);
    }

    
    #[Route('/orderDetail/{id}', name: 'orderDetail')]
    #[IsGranted('ROLE_ADMIN')]
    public function orderDetail(OrderRepository $orderRepository, Request $request, EntityManagerInterface $manager, AddressRepository $addressRepository, $id)
    {
        // Récuperation des informations de la commande ainsi que l'adresse pour la commande concernée
        $order = $orderRepository->find($id);
        $address = $addressRepository->find($id);

        if (!empty($_POST)) {
            // On récupère les infos de livraison du formulaire
            $delivery = $order->getDelivery();
            $deliveryDate = $request->request->get('deliveryDate');
            $status = $request->request->get('status');
            $delivery->setDeliveryDate(new \DateTime($deliveryDate));
            $delivery->setStatus($status);
            $manager->persist($delivery);
            $manager->flush();
            $this->addFlash('success', 'Statut mise à jour');
            return $this->render('admin/orderDetail.html.twig', [
                'order' => $order,
            ]);
        }

        return $this->render('admin/orderDetail.html.twig', [
            'order' => $order,
            'address'=>$address
        ]);
    }
}
