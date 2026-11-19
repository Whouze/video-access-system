<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $search = trim((string) $request->query('search', ''));

        // Hanya tampilkan video yang aksesnya sedang aktif saat ini
        $videos = Video::query()->whereHas('accessRequests', function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->where('status', 'approved')
                ->whereHas('grant', function ($g) {
                    $g->where('tanggal_mulai', '<=', now())
                        ->where('tanggal_selesai', '>=', now());
                });
        })
        ->when($search !== '', fn ($query) => $query->where('judul', 'like', "%{$search}%"))
        ->with(['accessRequests' => function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->where('status', 'approved')
                ->whereHas('grant', function ($g) {
                    $g->where('tanggal_mulai', '<=', now())
                        ->where('tanggal_selesai', '>=', now());
                })
                ->with('grant')
                ->latest();
        }])
        ->latest()
        ->paginate(9)
        ->withQueryString();

        return view('customer.videos.index', compact('videos', 'search'));
    }

    private function activeRequest(Video $video): ?AccessRequest
    {
        return AccessRequest::with('grant')
            ->where('user_id', auth()->id())
            ->where('video_id', $video->id)
            ->where('status', 'approved')
            ->whereHas('grant', fn ($q) => $q
                ->where('tanggal_mulai', '<=', now())
                ->where('tanggal_selesai', '>=', now()))
            ->latest()
            ->first();
    }

    public function stream(Video $video)
    {
        abort_if(! $this->activeRequest($video), 403);
        abort_unless(Storage::exists($video->file_path), 404);

        return response()->file(Storage::path($video->file_path));
    }
}
