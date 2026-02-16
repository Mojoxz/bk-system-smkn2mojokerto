<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsService
{
    public function createNews(array $data): News
    {
        if (isset($data['image']) && $data['image']) {
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if ($data['is_published'] ?? false) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        return News::create($data);
    }

    public function updateNews(News $news, array $data): News
    {
        if (isset($data['image']) && $data['image']) {
            // Delete old image
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        if (isset($data['title']) && $data['title'] !== $news->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        if (($data['is_published'] ?? false) && !$news->published_at) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        $news->update($data);

        return $news;
    }

    public function deleteNews(News $news): bool
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        return $news->delete();
    }

    private function handleImageUpload($image): string
    {
        if (is_string($image)) {
            return $image;
        }

        return $image->store('news', 'public');
    }

    public function incrementViews(News $news): void
    {
        $news->increment('views');
    }
}
