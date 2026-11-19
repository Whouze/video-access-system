<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessGrant;
use App\Models\AccessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessRequestController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');
        $status = in_array($status, ['pending', 'approved', 'rejected'], true) ? $status : '';

        $requests = AccessRequest::with(['user', 'video', 'grant'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->whereHas('user', fn ($userQuery) => $userQuery->where('email', 'like', "%{$search}%"))
                    ->orWhereHas('video', fn ($videoQuery) => $videoQuery->where('judul', 'like', "%{$search}%"));
            }))
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.access-requests.index', [
            'requests' => $requests,
            'request' => $requests,
            'search' => $search,
            'status' => $status,
        ]);
    }


    public function approve(Request $request, AccessRequest $accessRequest)
    {
        abort_if($accessRequest->status !== 'pending', 403, 'Request sudah diproses sebelumnya.');

        $data = $request->validate([
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ], [
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai harus sama atau setelah hari ini.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        DB::transaction(function () use ($accessRequest, $data) {
            AccessGrant::create([
                'access_request_id' => $accessRequest->id,
                'approved_by' => auth()->id(),
                'tanggal_mulai' => \Carbon\Carbon::parse($data['tanggal_mulai'])->startOfDay(),
                'tanggal_selesai' => \Carbon\Carbon::parse($data['tanggal_selesai'])->endOfDay(),
            ]);

            $accessRequest->update(['status' => 'approved']);
        });

        return back()->with('success', 'Akses disetujui.');
    }

    public function reject(AccessRequest $accessRequest)
    {
        abort_if($accessRequest->status !== 'pending', 403, 'Request sudah diproses sebelumnya.');

        $accessRequest->update(['status' => 'rejected']);

        return back()->with('success', 'Akses ditolak.');
    }

}
