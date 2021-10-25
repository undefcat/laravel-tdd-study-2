<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::limit(10)->get();

        return response()->json([
            'articles' => $articles,
        ]);
    }

    public function show(int $id)
    {
        $article = Article::select(['id', 'title', 'content', 'created_at'])
            ->findOrFail($id);

        return response()->json([
            'article' => $article,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => ['required'],
            'content' => ['required'],
        ];

        $data = $request->validate($rules);

        $article = new Article();

        $article->user_id = Auth::id();
        $article->title = $data['title'];
        $article->content = $data['content'];

        $article->save();

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $rules = [
            'title' => ['required'],
            'content' => ['required'],
        ];

        $data = $request->validate($rules);

        $article = Article::findOrFail($id);

        if ($request->user()->cannot('update', $article)) {
            return response()->json(null, Response::HTTP_FORBIDDEN);
        }

        $article->title = $data['title'];
        $article->content = $data['content'];

        $article->save();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function delete(Request $request, int $id)
    {
        $article = Article::findOrFail($id);

        if ($request->user()->cannot('delete', $article)) {
            return response()->json(null, Response::HTTP_FORBIDDEN);
        }

        $article->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
