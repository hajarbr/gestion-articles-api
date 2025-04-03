<?php
namespace App\Tests\Helper;

trait JwtTokenHelperTrait
{
    private function getJwtToken(string $email, string $password): string
    {
        $this->client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => $email,
            'password' => $password,
        ]));

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        if (!isset($responseData['token'])) {
            throw new \RuntimeException('Failed to retrieve JWT token.');
        }

        return $responseData['token'];
    }
}