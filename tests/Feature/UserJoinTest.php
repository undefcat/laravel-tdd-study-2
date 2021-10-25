<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserJoinTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/users/sign-up';

    private static $joinForm = [
        'name' => '회원',
        'email' => 'email@email.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    public function test_회원가입_201_성공(): void
    {
        $response = $this->postJson(self::URL, self::$joinForm);

        $response->assertStatus(Response::HTTP_CREATED);

        $isExistUser = User::where('email', '=', self::$joinForm['email'])
            ->count() > 0;

        $this->assertTrue($isExistUser);
    }

    public function test_회원가입_중복이메일_422_실패(): void
    {
        $existUser = User::factory()->create();

        $form = $this->getJoinFormDataWithMerge([
            'email' => $existUser->email,
        ]);

        $response = $this->postJson(self::URL, $form);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_회원가입_비밀번호확인_422_실패(): void
    {
        $form = $this->getJoinFormDataWithMerge([
            'password' => 'password',
            'password_confirmation' => 'password2',
        ]);

        $response = $this->postJson(self::URL, $form);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_회원가입_이름_비어있는_값_422_실패(): void
    {
        $this->testEmptyValueForm('name');
    }

    public function test_회원가입_이름_필드_없음_422_실패(): void
    {
        $this->testEmptyFieldForm('name');
    }

    public function test_회원가입_이메일_비어있는_값_422_실패(): void
    {
        $this->testEmptyValueForm('email');
    }

    public function test_회원가입_이메일_필드_없음_422_실패(): void
    {
        $this->testEmptyFieldForm('email');
    }

    public function test_회원가입_비밀번호_비어있는_값_422_실패(): void
    {
        $this->testEmptyValueForm('password');
    }

    public function test_회원가입_비밀번호_필드_없음_422_실패(): void
    {
        $this->testEmptyFieldForm('password');
    }

    private function testEmptyValueForm(string $targetField): void
    {
        $form = $this->getJoinFormDataWithMerge();

        $form[$targetField] = '';

        $response = $this->postJson(self::URL, $form);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function testEmptyFieldForm(string $targetField): void
    {
        $form = $this->getJoinFormDataWithMerge();

        unset($form[$targetField]);

        $response = $this->postJson(self::URL, $form);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function getJoinFormDataWithMerge(array $data = []): array
    {
        return array_merge(self::$joinForm, $data);
    }
}
