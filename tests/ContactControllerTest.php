<?php

namespace Controllers;

use PHPUnit\Framework\TestCase;
use Models\Contact;
use Models\City;
use Views\HtmlRenderer;

// Mock the header function in the Controllers namespace
function header($header) {
   ContactControllerTest::$headers[] = $header;
}

class ContactControllerTest extends TestCase
{
    public static $headers = [];

    private $contactModel;
    private $cityModel;
    private $htmlRenderer;
    private $contactController;

    protected function setUp(): void
    {
        self::$headers = [];

        $this->contactModel = $this->createMock(Contact::class);
        $this->cityModel = $this->createMock(City::class);
        $this->htmlRenderer = $this->createMock(HtmlRenderer::class);

        // Create the controller with mocked dependencies
        $this->contactController = new ContactController(
            $this->contactModel,
            $this->cityModel,
            $this->htmlRenderer
        );
    }

    public function testIndex()
    {
        $this->contactModel->method('getAllContacts')->willReturn([]);
        $this->cityModel->method('getAllCities')->willReturn([]);

        $this->htmlRenderer->expects($this->once())
            ->method('withContent')
            ->with('Views/contact/index.php')
            ->willReturn($this->htmlRenderer);

        $this->htmlRenderer->expects($this->once())
            ->method('withGlobals')
            ->with([
                'contacts' => [],
                'cities' => []
            ])
            ->willReturn($this->htmlRenderer);

        $this->htmlRenderer->expects($this->once())
            ->method('render');

        $this->contactController->index();
    }

    public function testShow()
    {
        $contactId = 1;
        $this->contactModel->method('getContactById')->with($contactId)->willReturn(['id' => $contactId, 'name' => 'John Doe']);
        $this->cityModel->method('getCityById')->with($contactId)->willReturn(['id' => $contactId, 'name' => 'Sample City']);

        $this->htmlRenderer->expects($this->once())
            ->method('withContent')
            ->with('Views/contact/show.php')
            ->willReturn($this->htmlRenderer);

        $this->htmlRenderer->expects($this->once())
            ->method('withGlobals')
            ->with([
                'contact' => ['id' => $contactId, 'name' => 'John Doe'],
                'city' => ['id' => $contactId, 'name' => 'Sample City']
            ])
            ->willReturn($this->htmlRenderer);

        $this->htmlRenderer->expects($this->once())
            ->method('render');

        $this->contactController->show($contactId);
    }

    public function testCreate()
    {
        $this->cityModel->method('getAllCities')->willReturn([]);

        $this->htmlRenderer->expects($this->once())
            ->method('withContent')
            ->with('Views/contact/form.php')
            ->willReturn($this->htmlRenderer);

        $this->htmlRenderer->expects($this->once())
            ->method('withGlobals')
            ->with(['cities' => []])
            ->willReturn($this->htmlRenderer);

        $this->htmlRenderer->expects($this->once())
            ->method('render');

        $this->contactController->create();
    }

    public function testStore()
    {
        $_POST = ['name' => 'John Doe', 'city_id' => 1];
        $this->contactModel->expects($this->once())
            ->method('addContact')
            ->with($_POST);

        // Run the store method
        $this->contactController->store();

        // Check if the header was set correctly
        $this->assertContains('Location: /contacts', self::$headers);
    }

    public function testEdit()
    {
        $contactId = 1;
        $this->contactModel->method('getContactById')->with($contactId)->willReturn(['id' => $contactId, 'name' => 'John Doe']);
        $this->cityModel->method('getAllCities')->willReturn([]);

        $this->htmlRenderer->expects($this->once())
            ->method('withContent')
            ->with('Views/contact/form.php')
            ->willReturn($this->htmlRenderer);

        $this->htmlRenderer->expects($this->once())
            ->method('withGlobals')
            ->with([
                'contact' => ['id' => $contactId, 'name' => 'John Doe'],
                'cities' => []
            ])
            ->willReturn($this->htmlRenderer);

        $this->htmlRenderer->expects($this->once())
            ->method('render');

        $this->contactController->edit($contactId);
    }

    public function testUpdate()
    {
        $contactId = 1;
        $_POST = ['name' => 'John Doe', 'city_id' => 1];
        $this->contactModel->expects($this->once())
            ->method('updateContact')
            ->with($contactId, $_POST);

        // Run the update method
        $this->contactController->update($contactId);

        // Check if the header was set correctly
        $this->assertContains('Location: /contacts', self::$headers);
    }

    public function testDestroy()
    {
        $contactId = 1;
        $this->contactModel->expects($this->once())
            ->method('deleteContact')
            ->with($contactId);

        // Run the destroy method
        $this->contactController->destroy($contactId);

        // Check if the header was set correctly
        $this->assertContains('Location: /contacts', self::$headers);
    }
}
