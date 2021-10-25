<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function update(User $user, Article $article)
    {
        return (int)$user->id === (int)$article->user_id;
    }

    public function delete(User $user, Article $article)
    {
        return (int)$user->id === (int)$article->user_id;
    }
}
