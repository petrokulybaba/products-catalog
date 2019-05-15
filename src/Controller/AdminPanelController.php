<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminPanelController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("")
     */
    public function index()
    {
        return $this->render('admin_panel/index.html.twig', [
            'usersCount' => count($this->getDoctrine()->getRepository(User::class)->findAll()),
            'productsCount' => count($this->getDoctrine()->getRepository(Product::class)->findAll()),
            'categoriesCount' => count($this->getDoctrine()->getRepository(Category::class)->findAll()),
            'lastUsers' => $this->getDoctrine()->getRepository(User::class)->findLast($this->getParameter('last_admin')),
            'lastCategories' => $this->getDoctrine()->getRepository(Category::class)->findLast($this->getParameter('last_admin')),
            'lastProducts' => $this->getDoctrine()->getRepository(Product::class)->findLast($this->getParameter('last_admin')),
        ]);
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/users")
     */
    public function usersList(Request $request, PaginatorInterface $paginator)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->getAll();

        return $this->render('admin_panel/usersList.html.twig', [
            'users' => $paginator->paginate(
                $users,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }

    /**
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/users/{id}/make-manager")
     */
    public function makeManager(User $user)
    {
        $user->setRoles([User::ROLE_ADMIN_MANAGER]);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('app_adminpanel_userslist');
    }

    /**
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/users/{id}/make-user")
     */
    public function makeUser(User $user)
    {
        $user->setRoles([User::ROLE_USER]);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('app_adminpanel_userslist');
    }
}
