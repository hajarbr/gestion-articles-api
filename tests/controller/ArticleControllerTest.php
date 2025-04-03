<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Helper\JwtTokenHelperTrait;

class ArticleControllerTest extends WebTestCase
{
    use JwtTokenHelperTrait;

    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testListArticles(): void
    {
        $token = $this->getJwtToken('hager@gmail.com', 'haajr123');

        $this->client->request('GET', '/api/articles', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('totalItems', $responseData);
        $this->assertArrayHasKey('itemsPerPage', $responseData);
        $this->assertArrayHasKey('currentPage', $responseData);
        $this->assertArrayHasKey('totalPages', $responseData);
    }

    public function testCreateArticle(): void
    {
        $token = $this->getJwtToken('hager@gmail.com', 'haajr123');

        $this->client->request('POST', '/api/articles', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => 'Test Article',
            'content' => 'This is a test article.',
        ]));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Article created successfully', $responseData['message']);
        $this->assertArrayHasKey('article', $responseData);
        $this->assertArrayHasKey('id', $responseData['article']);
        $this->assertArrayHasKey('title', $responseData['article']);
        $this->assertArrayHasKey('content', $responseData['article']);
    }
}