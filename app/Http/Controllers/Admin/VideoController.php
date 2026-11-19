<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $videos = Video::query()
            ->when($search !== '', fn ($query) => $query->where('judul', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.videos.index', compact('videos', 'search'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'file_path' => 'required|file|mimes:mp4,mov,avi|max:10240', // max 10MB
        ], [
            'judul.required' => 'Judul wajib diisi.',
            'file_path.required' => 'File video wajib diunggah.',
            'file_path.mimes' => 'Format file video harus mp4, mov, atau avi.',
            'file_path.max' => 'Ukuran file video maksimal 10MB.',
        ]);

        // Simpan file video ke storage
        $filePath = $request->file('file_path')->store('videos');

        Video::create([
            'judul' => $data['judul'],
            'keterangan' => $data['keterangan'] ?? null,
            'file_path' => $filePath,
        ]);

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil ditambahkan.');
    }

    public function edit(Request $request, Video $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {

        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:mp4,mov,avi|max:10240', // max 10MB
        ], [
            'judul.required' => 'Judul wajib diisi.',
            'file_path.mimes' => 'Format file video harus mp4, mov, atau avi.',
            'file_path.max' => 'Ukuran file video maksimal 10MB.',
        ]);

        if ($request->hasFile('file_path')) {
            // Hapus file lama jika ada
            if ($video->file_path) {
                Storage::delete($video->file_path);
            }
            // Simpan file video baru ke storage
            $filePath = $request->file('file_path')->store('videos');
            $video->file_path = $filePath;
        }

        $video->judul = $data['judul'];
        $video->keterangan = $data['keterangan'] ?? null;
        $video->save();

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil diperbarui.');
    }

    public function stream(Video $video)
    {
        abort_unless(Storage::exists($video->file_path), 404);

        return response()->file(Storage::path($video->file_path));
    }

    public function destroy(Video $video)
    {

        // Hapus file video dari storage
        if ($video->file_path) {
            Storage::delete($video->file_path);
        }

        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil dihapus.');
    }
}
