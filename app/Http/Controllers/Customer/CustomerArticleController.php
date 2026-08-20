<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class CustomerArticleController extends Controller
{
    public function index(Request $request)
    {
        // 🔥 AMBIL KATEGORI UNTUK FILTER
        $categories = ArticleCategory::active()->sorted()->get();

        // 🔥 QUERY ARTIKEL
        $query = Article::with('articleCategory')
            ->active()
            ->published();

        // 🔥 FILTER KATEGORI
        if ($request->filled('category')) {
            $query->where('article_category_id', $request->category);
        }

        // 🔥 FILTER TAGS
        if ($request->filled('tag')) {
            $query->where('tags', 'like', '%' . $request->tag . '%');
        }

        // 🔥 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%');
            });
        }

        // 🔥 SORT
        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('published_at', 'asc')
                      ->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('published_at', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
        }

        // 🔥 PAGINATION
        $articles = $query->paginate(12);

        // 🔥 AMBIL SEMUA TAGS UNIK UNTUK FILTER
        $allTags = Article::active()
            ->published()
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        return view('customer.articles.index', compact(
            'articles',
            'categories',
            'allTags'
        ));
    }

    public function show($slug)
    {
        // 🔥 AMBIL ARTIKEL UTAMA
        $article = Article::with('articleCategory')
            ->where('slug', $slug)
            ->active()
            ->published()
            ->firstOrFail();

        // 🔥 INCREMENT VIEWS - HANYA JIKA BELUM DIBACA
        // Views akan diincrement melalui AJAX setelah halaman dimuat
        // dan dicek via LocalStorage

        // 🔥 1. ARTIKEL TERKAIT (same category)
        $relatedArticles = Article::with('articleCategory')
            ->where('article_category_id', $article->article_category_id)
            ->where('id', '!=', $article->id)
            ->active()
            ->published()
            ->latest()
            ->limit(5)
            ->get();

        // 🔥 2. ARTIKEL TERBARU
        $latestArticles = Article::with('articleCategory')
            ->where('id', '!=', $article->id)
            ->active()
            ->published()
            ->latest()
            ->limit(5)
            ->get();

        // 🔥 3. ARTIKEL POPULER (berdasarkan views) - TAMPILKAN SEMUA
        $popularArticles = Article::with('articleCategory')
            ->where('id', '!=', $article->id)
            ->active()
            ->published()
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        // 🔥 4. ARTIKEL REKOMENDASI (mix dari berbagai kategori, random)
        $recommendedArticles = Article::with('articleCategory')
            ->where('id', '!=', $article->id)
            ->where('article_category_id', '!=', $article->article_category_id)
            ->active()
            ->published()
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // 🔥 5. ARTIKEL DENGAN TAGS SAMA (jika ada tags)
        $tagRelatedArticles = collect();
        if ($article->tags && count($article->tags) > 0) {
            $tagRelatedArticles = Article::with('articleCategory')
                ->where('id', '!=', $article->id)
                ->where(function($query) use ($article) {
                    foreach ($article->tags as $tag) {
                        $query->orWhere('tags', 'like', '%' . $tag . '%');
                    }
                })
                ->active()
                ->published()
                ->latest()
                ->limit(4)
                ->get();
        }

        // 🔥 6. SEMUA KATEGORI UNTUK SIDEBAR
        $categories = ArticleCategory::active()
            ->withCount('articles')
            ->sorted()
            ->get();

        return view('customer.articles.show', compact(
            'article',
            'relatedArticles',
            'latestArticles',
            'popularArticles',
            'recommendedArticles',
            'tagRelatedArticles',
            'categories'
        ));
    }

    /**
     * 🔥 API untuk mencatat views (dipanggil via AJAX)
     */
    public function recordView(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
        ]);

        $article = Article::find($request->article_id);
        
        // 🔥 INCREMENT VIEWS
        $article->increment('views');
        
        return response()->json([
            'success' => true,
            'message' => 'View recorded',
            'views' => $article->views
        ]);
    }
}