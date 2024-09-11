<?php

namespace Geekbrains\Application1\Application;

use Exception;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Render
{

    private string $viewFolder = '/src/Domain/Views/';
    private FilesystemLoader $loader;
    private Environment $environment;


    public function __construct()
    {
        $this->loader = new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . $this->viewFolder);
        $this->environment = new Environment($this->loader, [
//            'cache' => $_SERVER['DOCUMENT_ROOT'].'/cache/',
        ]);
    }

    public function renderPage(string $contentTemplateName = 'page-index.twig', array $templateVariables = [])
    {
        $template = $this->environment->load('main.twig');

        $templateVariables['content_template_name'] = $contentTemplateName;

        return $template->render($templateVariables);
    }

    public static function renderExceptionPage(Exception $exception): string
    {
        $render = new Render();
        return $render->renderPage("error.twig",
            [
                'title' => 'Ошибка приложения!!!',
                'error_message' => $exception->getMessage()
            ]);
    }
}