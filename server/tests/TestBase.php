<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

abstract class TestBase extends TestCase
{
    /**
     * Biztosítja, hogy a megadott email/jelszó/role kombó létezzen, majd visszaadja.
     */
    protected function ensureUser(string $email, string $password, int $role): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $email,
                'password' => Hash::make($password),
                'role' => $role,
                'phone' => '000',
                'city' => 'City',
                'street' => 'Street',
                'house_number' => '1',
                'zip_code' => '0000',
            ]
        );
    }

    protected function login(string $email = 'admin@example.com', string $password = '123')
    {
        $this->ensureUser($email, $password, $email === 'admin@example.com' ? 1 : 2);

        $uri = '/api/users/login';
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        $data = [
            'email' => $email,
            'password' => $password,
        ];
        return $this->withHeaders($headers)->postJson($uri, $data);
    }

    protected function logout(string $token)
    {
        $uri = '/api/users/logout';
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token"
        ];
        return $this->withHeaders($headers)->postJson($uri);
    }

    protected function myGetToken($response): string
    {
        return $response->json('data')['token'];
    }

    protected function myGet(string $uri, string $token)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        return $this->withHeaders($headers)->get($uri);
    }

    protected function myPost(string $uri, array $data, string $token)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        return $this->withHeaders($headers)->postJson($uri, $data);
    }

    protected function myPatch(string $uri, array $data, string $token)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        return $this->withHeaders($headers)->patchJson($uri, $data);
    }

    protected function myDelete(string $uri, string $token)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        return $this->withHeaders($headers)->delete($uri);
    }
}

