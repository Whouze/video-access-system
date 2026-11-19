<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\Video;
use Illuminate\Http\Request;

class AccessRequestController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');
        $status = in_array($status, ['pending', 'approved', 'rejected'], true) ? $status : '';

        $requests = AccessRequest::with(['video', 'grant'])
            ->where('user_id', auth()->id())
            ->when($search !== '', fn ($query) => $query->whereHas('video', fn ($videoQuery) => $videoQuery->where('judul', 'like', "%{$search}%")))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Ambil semua video untuk pilihan di form permohonan baru
        $availableVideos = Video::orderBy('judul')->get();

        return view('customer.requests.index', compact('requests', 'availableVideos', 'search', 'status'));
    }

    public function store(Request $request, ?Video $video = null)
    {
        $videoId = $video ? $video->id : $request->input('video_id');

        $request->validate([
            'video_id' => $video ? 'nullable' : 'required|exists:videos,id',
            'durasi_diminta' => 'nullable|integer|min:1|max:365',
        ], [
            'video_id.required' => 'Silakan pilih video yang ingin diajukan.',
            'video_id.exists' => 'Video yang dipilih tidak valid.',
            'durasi_diminta.integer' => 'Durasi harus berupa angka.',
            'durasi_diminta.min' => 'Durasi minimal 1 hari.',
            'durasi_diminta.max' => 'Durasi maksimal 365 hari.',
        ]);

        $base = AccessRequest::where('user_id', auth()->id())
            ->where('video_id', $videoId);

        // Cek jika masih ada permohonan yang berstatus pending
        if ((clone $base)->where('status', 'pending')->exists()) {
            return back()->with('error', 'Permohonan Anda untuk video ini masih menunggu persetujuan admin.');
        }

        // Cek jika masih punya izin akses yang masih aktif (belum expired)
        $stillValid = (clone $base)->where('status', 'approved')
            ->whereHas('grant', fn ($q) => $q->where('tanggal_selesai', '>=', now()))
            ->exists();

        if ($stillValid) {
            return back()->with('error', 'Anda masih memiliki akses aktif untuk video ini.');
        }

        AccessRequest::create([
            'user_id' => auth()->id(),
            'video_id' => $videoId,
            'durasi_diminta' => $request->input('durasi_diminta') ?: null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permohonan akses berhasil dikirim ke admin.');
    }
}
