<?php

namespace Modules\SystemSetting\app\Repositories\Api;

use Modules\SystemSetting\app\Interfaces\Api\BlogApiRepositoryInterface;
use Modules\SystemSetting\app\Models\Blog;

class BlogApiRepository implements BlogApiRepositoryInterface
{
    public function all($filters = [])
    {
        $query = Blog::with(['blogCategory.translations', 'translations', 'attachments'])
        ->withCount('comments')
        ->active()
        ->filter($filters)->latest();
        if(isset($filters['per_page'])) {
            return ($filters['per_page'] == 0) ? $query->get() : $query->paginate($filters['per_page']);
        } else {
            return $query->paginate(10);
        }
    }

    public function find($id)
    {
        $blog = Blog::with(['blogCategory.translations', 'translations', 'attachments', 'comments' => function($q) {
                $q->latest();
            }])
        ->withCount('comments')
        ->active()
            ->where(function($q) use ($id) {
                $q->where('slug', $id)->orWhere('id', $id);
            })
            ->firstOrFail();

        $blog->increment('views_count');
        
        return $blog;
    }

    public function getHostTopics($filters = [])
    {
        $query = Blog::withCount('comments')->with(['blogCategory.translations', 'translations', 'attachments'])
            ->active()
            ->filter($filters)
            ->orderBy('comments_count', 'desc');

        $perPage = $filters['per_page'] ?? 5;
        return $perPage == 0 ? $query->get() : $query->paginate($perPage);
    }

    public function addComment($blog, $data)
    {
        return $blog->comments()->create([
            'customer_id' => auth()->id(),
            'comment' => $data['comment'],
        ]);
    }
}
