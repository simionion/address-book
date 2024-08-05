<?php
declare(strict_types=1);

namespace Controllers;

use Models\City;
use Models\Contact;
use Views\HtmlRenderer;

class ContactController
{
    private Contact $contactModel;
    private City $cityModel;
    private HtmlRenderer $htmlRenderer;

    public function __construct(Contact $contactModel, City $cityModel, HtmlRenderer $htmlRenderer)
    {
        $this->contactModel = $contactModel;
        $this->cityModel = $cityModel;

        $this->htmlRenderer = $htmlRenderer;
        $this->htmlRenderer->withLayout('Views/layout/main.php');
    }

    public function index(): void
    {
        $this->htmlRenderer
            ->withContent('Views/contact/index.php')
            ->withGlobals([
                'contacts' => $this->contactModel->getAllContacts(),
                'cities' => $this->cityModel->getAllCities()
            ])
            ->render();
    }

    public function show(int $id): void
    {
       $this->htmlRenderer
           ->withContent('Views/contact/show.php')
            ->withGlobals([
                'contact' => $this->contactModel->getContactById($id),
                'city' => $this->cityModel->getCityById($id)
            ])
            ->render();
    }

    public function create(): void
    {
        $this->htmlRenderer
            ->withContent('Views/contact/form.php')
            ->withGlobals([
                'cities' => $this->cityModel->getAllCities()
            ])
            ->render();
    }

    public function store(): void
    {
        $this->contactModel->addContact($_POST);
        header('Location: /contacts');
    }

    public function edit(int $id): void
    {
       $this->htmlRenderer
            ->withContent('Views/contact/form.php')
            ->withGlobals([
                'contact' => $this->contactModel->getContactById($id),
                'cities' => $this->cityModel->getAllCities()
            ])
            ->render();
    }

    public function update(int $id): void
    {
        $this->contactModel->updateContact($id, $_POST);
        header('Location: /contacts');
    }

    public function destroy(int $id): void
    {
        $this->contactModel->deleteContact($id);
        header('Location: /contacts');
    }
}
