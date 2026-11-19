<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Permohonan Akses Video') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            <form method="GET" action="{{ route('admin.requests.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="search" name="search" value="{{ $search }}" placeholder="Cari email customer atau judul video..." class="input input-bordered w-full sm:max-w-md bg-white dark:bg-gray-800 dark:text-gray-100">
                <select name="status" class="select select-bordered w-full sm:w-56 bg-white dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Semua status</option>
                    <option value="pending" @selected($status === 'pending')>Menunggu</option>
                    <option value="approved" @selected($status === 'approved')>Disetujui</option>
                    <option value="rejected" @selected($status === 'rejected')>Ditolak</option>
                </select>
                <button type="submit" class="btn btn-primary">Terapkan</button>
                @if ($search !== '' || $status !== '')
                    <a href="{{ route('admin.requests.index') }}" class="btn btn-ghost">Reset</a>
                @endif
            </form>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-8 gap-x-6">
                @forelse ($requests as $req)
                    <div class="flex flex-col gap-3 group">
                        
                        <div class="relative aspect-video w-full rounded-2xl overflow-hidden bg-black shadow-md border border-gray-800">
                            @if ($req->video)
                                <video 
                                    controls 
                                    preload="metadata" 
                                    class="w-full h-full object-contain"
                                >
                                    <source src="{{ route('admin.videos.stream', $req->video) }}" type="video/mp4">
                                    Browser Anda tidak mendukung pemutar video HTML5.
                                </video>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-500 text-xs">
                                    File video tidak ditemukan.
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col gap-3 px-1">
                            
                            <div class="space-y-1">
                                <div>
                                    @if ($req->status === 'pending')
                                        <span class="badge badge-warning badge-xs font-semibold">Menunggu Persetujuan</span>
                                    @elseif ($req->status === 'approved')
                                        <span class="badge badge-success badge-xs font-semibold">Disetujui</span>
                                    @else
                                        <span class="badge badge-error badge-xs font-semibold">Ditolak</span>
                                    @endif
                                </div>

                                <h3 class="font-bold text-sm text-gray-900 dark:text-gray-100 line-clamp-2 leading-snug" title="{{ $req->video->judul ?? 'Video telah dihapus' }}">
                                    {{ $req->video->judul ?? 'Video telah dihapus' }}
                                </h3>

                                <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                                    Pemohon: <span class="text-gray-900 dark:text-gray-100">{{ $req->user->name ?? 'User dihapus' }}</span>
                                </p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">
                                    Diajukan: {{ $req->created_at->format('d-m-Y') }} ({{ $req->durasi_diminta ? $req->durasi_diminta . ' Hari' : 'Bebas' }})
                                </p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">
                                    Diperbarui: {{ $req->updated_at->format('d-m-Y H:i') }}
                                </p>

                                {{-- Masa Izin Akses Jika Sudah Disetujui --}}
                                @if ($req->status === 'approved' && $req->grant)
                                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium pt-0.5">
                                        Aktif: {{ $req->grant->tanggal_mulai->format('d-m-Y') }} s/d {{ $req->grant->tanggal_selesai->format('d-m-Y') }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                @if ($req->status === 'pending')
                                    {{-- Tombol Setujui Akses (Memicu Modal) --}}
                                    <button 
                                        type="button" 
                                        class="btn btn-xs btn-primary"
                                        x-data=""
                                        x-on:click="$dispatch('open-modal', 'approve-modal-{{ $req->id }}')"
                                    >
                                        Setujui Akses
                                    </button>

                                    {{-- Tombol Tolak --}}
                                    <form method="POST" action="{{ route('admin.requests.reject', $req) }}" onsubmit="return confirm('Apakah Anda yakin ingin menolak permohonan akses ini?');" class="inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-error">
                                            Tolak
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Permohonan telah selesai diproses.</span>
                                @endif
                            </div>

                        </div>

                    </div>

                    @if ($req->status === 'pending')
                        @php
                            $defaultDuration = $req->durasi_diminta ?: 7;
                            $defaultStartDate = now()->format('Y-m-d');
                            $defaultEndDate = now()->addDays((int) $defaultDuration)->format('Y-m-d');
                        @endphp

                        <x-modal name="approve-modal-{{ $req->id }}" maxWidth="sm">
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">
                                            Setujui Akses Video
                                        </h2>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                            Tentukan rentang tanggal akses untuk <strong class="text-gray-800 dark:text-gray-200">{{ $req->user->name }}</strong>.
                                        </p>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-ghost" x-on:click="$dispatch('close-modal', 'approve-modal-{{ $req->id }}')">
                                        ✕
                                    </button>
                                </div>

                                <div class="mt-3 p-2.5 bg-gray-50 dark:bg-gray-900 rounded-lg text-xs space-y-1">
                                    <p><strong>Video:</strong> {{ $req->video->judul ?? '-' }}</p>
                                    <p><strong>Permintaan:</strong> {{ $req->durasi_diminta ? $req->durasi_diminta . ' Hari' : 'Bebas / Tidak ditentukan' }}</p>
                                </div>

                                <form method="POST" action="{{ route('admin.requests.approve', $req) }}" class="mt-4 space-y-3">
                                    @csrf

                                    <div>
                                        <x-input-label for="tanggal_mulai_{{ $req->id }}" value="Tanggal Mulai Akses" class="text-xs" />
                                        <x-text-input 
                                            id="tanggal_mulai_{{ $req->id }}" 
                                            name="tanggal_mulai" 
                                            type="date" 
                                            class="mt-1 block w-full text-xs" 
                                            :value="old('tanggal_mulai', $defaultStartDate)" 
                                            min="{{ date('Y-m-d') }}"
                                            required 
                                        />
                                        <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-1 text-xs" />
                                    </div>

                                    <div>
                                        <x-input-label for="tanggal_selesai_{{ $req->id }}" value="Tanggal Selesai Akses" class="text-xs" />
                                        <x-text-input 
                                            id="tanggal_selesai_{{ $req->id }}" 
                                            name="tanggal_selesai" 
                                            type="date" 
                                            class="mt-1 block w-full text-xs" 
                                            :value="old('tanggal_selesai', $defaultEndDate)" 
                                            min="{{ date('Y-m-d') }}" 
                                            required 
                                        />
                                        <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-1 text-xs" />
                                    </div>

                                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        <button type="button" class="btn btn-xs btn-ghost" x-on:click="$dispatch('close-modal', 'approve-modal-{{ $req->id }}')">
                                            Batal
                                        </button>
                                        <button type="submit" class="btn btn-xs btn-primary">
                                            Konfirmasi Setujui
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>
                    @endif

                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 p-12 text-center rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-500">
                        <p class="text-base font-semibold">Belum ada permohonan akses.</p>
                        <p class="text-sm mt-1">Permohonan akses video yang diajukan oleh customer akan tampil di sini.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
