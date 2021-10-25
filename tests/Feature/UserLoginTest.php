<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/users/sign-in';

    public function test_로그인_200_성공(): void
    {
        $user = User::factory()->create();

        $credential = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson(self::URL, $credential);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_로그인_존재하지_않는_사용자_401_실패(): void
    {
        User::factory()->create();

        $credential = [
            'email' => 'not_a_user@email.com',
            'password' => 'password',
        ];

        $response = $this->postJson(self::URL, $credential);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_로그인_비밀번호_틀린_사용자_401_실패()
    {
        $user = User::factory()->create();

        $credential = [
            'email' => $user->email,
            'password' => 'not_password',
        ];

        $response = $this->postJson(self::URL, $credential);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
