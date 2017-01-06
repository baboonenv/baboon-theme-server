<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/configuration", name="configuration")
     */
    public function indexAction(Request $request)
    {
        $data = [
            'name' => 'Baboon Official Theme Server',
            'categoriesUrl' => $this->generateUrl('categories', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/categories", name="categories")
     */
    public function categoriesAction(Request $request)
    {
        $data = [
            'categories' => [
                [
                    'themesUrl' => $this->generateUrl('category', ['id' => 1], UrlGeneratorInterface::ABSOLUTE_URL),
                    'categoryName' => 'All',
                ],
                [
                    'themesUrl' => $this->generateUrl('category', ['id' => 2], UrlGeneratorInterface::ABSOLUTE_URL),
                    'categoryName' => 'Developer Site',
                ]
            ],
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/category/{id}", name="category")
     */
    public function categoryAction(Request $request, $id)
    {
        $data[] = [
            'themeName' => 'Baboon Default Theme',
            'zipUrl' => 'https://github.com/behram/baboon-default-theme/archive/master.zip',
            'gitUrl' => 'git@github.com:behram/baboon-default-theme.git',
        ];
        $data[] = [
            'themeName' => 'Baboon Configuration Theme',
            'zipUrl' => 'https://github.com/behram/baboon-configuration-theme/archive/master.zip',
            'gitUrl' => 'git@github.com:behram/baboon-configuration-theme.git',
        ];

        return new JsonResponse($data);
    }
}
