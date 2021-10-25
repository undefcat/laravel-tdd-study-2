<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/articles';

    public function test_게시글_목록_페이지당_10개_가져오기_200_성공(): void
    {
        // 11개의 게시글을 만들어도
        // 10개만 가져와야 한다.
        Article::factory()
            ->for(User::factory())
            ->count(11)
            ->create();

        $response = $this->getJson(self::URL);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(10, 'articles');
    }

    public function test_게시글_가져오기_200_성공()
    {
        $article = Article::factory()
            ->for(User::factory())
            ->create();

        $response = $this->getJson(self::URL."/{$article->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('article', fn ($json) =>
                $json
                    ->where('id', $article->id)
                    ->where('title', $article->title)
                    ->where('content', $article->content)
                    ->etc()
            )
        );
    }

    public function test_게시글_작성_201_성공(): void
    {
        $user = User::factory()->create();

        $formData = [
            'title' => 'title',
            'content' => 'content',
        ];

        $response = $this->actingAs($user)
            ->postJson(self::URL, $formData);

        $response->assertStatus(Response::HTTP_CREATED);

        $isArticleCreated = Article::where([
            ['user_id', '=', $user->id],
            ['title', '=', $formData['title']],
        ])->count() > 0;

        $this->assertTrue($isArticleCreated);
    }

    public function test_게시글_작성자_본인_수정_204_성공(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()
            ->for($user)
            ->create();

        $formData = [
            'title' => 'modified',
            'content' => 'modified',
        ];

        $response = $this->actingAs($user)
            ->putJson(self::URL."/{$article->id}", $formData);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $article = Article::where('id', '=', $article->id)->first();

        $this->assertNotEmpty($article);

        $this->assertEquals($formData['title'], $article->title);
        $this->assertEquals($formData['content'], $article->content);
    }

    public function test_게시글_작성자_다른사람_수정_403_실패(): void
    {
        $article = Article::factory()
            ->for(User::factory())
            ->create();

        $formData = [
            'title' => 'modified',
            'content' => 'modified',
        ];

        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->putJson(self::URL."/{$article->id}", $formData);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_게시글_작성자_본인_삭제_204_성공(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()
            ->for($user)
            ->create();

        $response = $this->actingAs($user)
            ->deleteJson(self::URL."/{$article->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $isDeleted = Article::where('id', '=', $article->id)
            ->count() === 0;

        $this->assertTrue($isDeleted);
    }

    public function test_게시글_작성자_다른사람_삭제_403_실패(): void
    {
        $article = Article::factory()
            ->for(User::factory())
            ->create();

        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->deleteJson(self::URL."/{$article->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_게시글_관리자_수정_204_성공(): void
    {
        $superAdmin = User::factory()
            ->has(Role::factory()->superAdmin())
            ->create();

        $article = Article::factory()
            ->for(User::factory())
            ->create();

        $formData = [
            'title' => 'modified',
            'content' => 'modified',
        ];

        $response = $this->actingAs($superAdmin)
            ->putJson(self::URL."/{$article->id}", $formData);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $article = Article::where('id', '=', $article->id)->first();

        $this->assertNotEmpty($article);

        $this->assertEquals($formData['title'], $article->title);
        $this->assertEquals($formData['content'], $article->content);
    }

    public function test_게시글_관리자_삭제_204_성공(): void
    {
        $superAdmin = User::factory()
            ->has(Role::factory()->superAdmin())
            ->create();

        $article = Article::factory()
            ->for(User::factory())
            ->create();

        $response = $this->actingAs($superAdmin)
            ->deleteJson(self::URL."/{$article->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $isDeleted = Article::where('id', '=', $article->id)->count() === 0;

        $this->assertTrue($isDeleted);
    }
}
