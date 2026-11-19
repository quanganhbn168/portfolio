<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /** Gửi bình luận cho bất kỳ nội dung nào dùng HasComments (khóa morph: post, capability, project). */
    public function store(StoreCommentRequest $request, string $type, int $id): RedirectResponse
    {
        $model = Relation::getMorphedModel($type);

        abort_unless(in_array($type, Comment::COMMENTABLE_TYPES, true) && $model !== null, 404);

        $commentable = $model::query()->published()->findOrFail($id);

        if ($commentable->commentsRequireRating()) {
            $request->validate([
                'rating' => ['required', 'integer', 'between:1,5'],
            ]);
        }

        if ($request->filled('website_url')) {
            return $this->thanks();
        }

        $data = $request->validated();

        $commentable->comments()->create([
            'author_name' => trim(strip_tags($data['author_name'])),
            'author_email' => $data['author_email'] ?? null,
            'rating' => $data['rating'] ?? null,
            'body' => trim(strip_tags($data['body'])),
            'status' => Comment::STATUS_PENDING,
        ]);

        return $this->thanks();
    }

    private function thanks(): RedirectResponse
    {
        return back()->with('success', __('Cảm ơn bạn. Bình luận đã được gửi và đang chờ duyệt.'));
    }
}
