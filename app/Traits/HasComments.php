<?php

namespace App\Traits;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasComments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /** Nội dung có bắt buộc kèm số sao khi bình luận (đánh giá) hay không. */
    public function commentsRequireRating(): bool
    {
        return false;
    }

    public function approvedComments(): MorphMany
    {
        return $this->comments()
            ->approved()
            ->whereNull('parent_id');
    }
}
