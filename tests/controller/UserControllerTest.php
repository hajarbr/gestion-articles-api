<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Helper\JwtTokenHelperTrait;

class UserControllerTest extends WebTestCase
{
    use JwtTokenHelperTrait;

    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetAccount(): void
    {
        $token = $this->getJwtToken('hager@gmail.com', 'haajr123');

        $this->client->request('GET', '/api/account', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertArrayHasKey('email', $responseData);
    }

    public function testUpdateUser(): void
    {
        $token = $this->getJwtToken('hager@gmail.com', 'haajr123');
    
        $this->client->request('PUT', '/api/users/67ed667416168d9748075712', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Hajija',
            'email' => 'hajija@gmail.com',
        ]));
    
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
    
        $this->assertEquals('User updated successfully', $responseData['message']);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertEquals('Hajija', $responseData['user']['name']);
        $this->assertEquals('hajija@gmail.com', $responseData['user']['email']);
    }
}