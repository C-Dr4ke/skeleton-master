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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminController extends AbstractController
{
    // Permet de stocker des informations en session (il faudra essayer de comprendre comment cela fonctionne avant le jury)
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    //************************************************************************************/
    //*********************************CATEGORIES****************************************//
    //***********************************************************************************/

    #[Route('/category', name: 'category')]
    #[Route('/editCategory/{id}', name: 'editCategory')]
    #[IsGranted('ROLE_ADMIN')]
    public function category(Request $request, EntityManagerInterface $manager, CategoryRepository $repository, $id = null)
    {

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
    #[IsGranted('ROLE_ADMIN')]
    public function deleteCategory(Category $category, EntityManagerInterface $manager)
    {
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
    #[IsGranted('ROLE_ADMIN')]
    public function deleteSubCategory(SubCategory $subCategory, EntityManagerInterface $manager)
    {
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
      
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, ['add' => true]);
        $form->handleRequest($request);
      
        
        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('picture')->getData();
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
        $products = $repository->findAll();
       
        return $this->render('admin/listMenus.html.twig', [
            'products' => $products
        ]);
    }


    #[Route('/editMenu/{id}', name: 'editMenu')]
    #[IsGranted('ROLE_ADMIN')]
    public function editMenu(Request $request, EntityManagerInterface $manager, Product $product)
    {
     
        $form = $this->createForm(ProductType::class, $product,['edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('editPicture')->getData();

            if ($file) {
                $fileName = date('YmdHis') . '-' . uniqid() . '-' . $file->getClientOriginalName();

                try {
                    $file->move($this->getParameter('upload_directory'), $fileName);
                    unlink($this->getParameter('upload_directory') . '/' . $product->getPicture());
                } catch (FileException $exception) {
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
        unlink($this->getParameter('upload_directory') . '/' . $product->getPicture());

        $manager->remove($product);
        $manager->flush();

        $this->addFlash('success', 'Le menu a bien été supprimé');
        return $this->redirectToRoute('listMenus');
    }

     //************************************************************************************/
    //*********************************Commande***********************************//
    //***********************************************************************************/

    
    #[Route('/orderList', name: 'orderList')]
    #[IsGranted('ROLE_ADMIN')]
    public function orderList(OrderRepository $repository)
    {

        $orders = $repository->findAll();

        return $this->render('admin/orderList.html.twig', [
            'orders' => $orders
        ]);
    }

     /**
     *@Route("/orderDetail/{id}", name="orderDetail")
     *
     */
    #[Route('/orderDetail/{id}', name: 'orderDetail')]
    #[IsGranted('ROLE_ADMIN')]
    public function orderDetail(OrderRepository $orderRepository, Request $request, EntityManagerInterface $manager, AddressRepository $addressRepository, $id)
    {

        $order = $orderRepository->find($id);
        $address = $addressRepository->find($id);

        if (!empty($_POST)) {

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
