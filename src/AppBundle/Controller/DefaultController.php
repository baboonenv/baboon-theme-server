<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Theme;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="add_theme")
     */
    public function indexAction(Request $request)
    {
        $messages = [];
        if($request->getMethod() == 'POST'){
            $em = $this->getDoctrine()->getManager();
            $enableThemeService = $this->get('baboon_server.enable_theme_service');
            $zipUri = $request->request->get('zipUri');
            $confData = $enableThemeService->downloadAndValidate($zipUri);
            if(is_array($confData)){
                $findTheme = $em->getRepository(Theme::class)->findBy([
                    'zipUrl' => $confData['info']['update_zip_uri'],
                ]);
                if(!$findTheme){

                $theme = new Theme();
                $theme
                    ->setName($confData['info']['theme_name'])
                    ->setGitUrl($confData['info']['update_git_uri'])
                    ->setZipUrl($confData['info']['update_zip_uri'])
                    ;
                $em->persist($theme);
                $em->flush();

                $messages[] = 'theme successfully saved!';
                }else{
                    $messages[] = 'theme already exists';
                }
            }else{
                $messages = $enableThemeService->errors;
            }
        }
        return $this->render('@App/Default/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/configuration", name="configuration")
     */
    public function configurationAction(Request $request)
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
        $em = $this->getDoctrine()->getManager();
        $getThemes = $em->getRepository(Theme::class)->findAll();
        $data = [];
        foreach ($getThemes as $theme){
            $data[] = [
                'themeName' => $theme->getName(),
                'zipUrl' => $theme->getZipUrl(),
                'gitUrl' => $theme->getGitUrl(),
            ];
        }

        return new JsonResponse($data);
    }
}
