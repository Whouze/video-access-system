<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-left">
                <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Riwayat Permohonan Akses') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Kelola dan pantau status permohonan akses video Anda.
                </p>
            </div>

            <button
                type="button"
                x-data
                x-on:click="$dispatch('open-modal', 'request-create')"
                style="background-color: #4f46e5; color: #ffffff;"
                class="self-start sm:self-auto px-3.5 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition cursor-pointer shrink-0"
            >
                + Buat Permohonan Baru
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
            <x-alert />

            {{-- Filter & Search Bar --}}
            <form
                method="GET"
                action="{{ route('customer.requests.index') }}"
                class="bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-stretch sm:items-center gap-2"
            >
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari judul video..."
                    aria-label="Cari judul video"
                    class="w-full sm:flex-1 rounded-lg px-3 py-1.5 text-sm text-left bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >

                <select
                    name="status"
                    aria-label="Filter status permohonan"
                    class="w-full sm:w-48 rounded-lg px-3 py-1.5 text-sm bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option value="">Semua status</option>
                    <option value="pending" @selected($status === 'pending')>Menunggu</option>
                    <option value="approved" @selected($status === 'approved')>Disetujui</option>
                    <option value="rejected" @selected($status === 'rejected')>Ditolak</option>
                </select>

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="submit"
                        style="background-color: #4f46e5; color: #ffffff;"
                        class="px-4 py-1.5 rounded-lg text-sm font-medium hover:opacity-90 transition cursor-pointer"
                    >
                        Terapkan
                    </button>

                    @if ($search !== '' || $status !== '')
                        <a
                            href="{{ route('customer.requests.index') }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Daftar Permohonan --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <th class="py-3 px-4 w-12">#</th>
                                <th class="py-3 px-4">Judul Video</th>
                                <th class="py-3 px-4">Diajukan Pada</th>
                                <th class="py-3 px-4">Durasi Diminta</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Masa Akses</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/70">
                            @forelse ($requests as $index => $req)
                                @php
                                    $isApproved = $req->status === 'approved';
                                    $isPending = $req->status === 'pending';
                                    $isRejected = $req->status === 'rejected';

                                    $isExpired = $isApproved && $req->grant && now()->gt($req->grant->tanggal_selesai);
                                    $isActive = $isApproved && $req->grant && now()->between($req->grant->tanggal_mulai, $req->grant->tanggal_selesai);
                                @endphp

                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="py-3.5 px-4 text-xs font-medium text-gray-400">
                                        {{ $requests->firstItem() + $index }}
                                    </td>

                                    <td class="py-3.5 px-4 font-semibold text-sm text-gray-900 dark:text-gray-100">
                                        {{ $req->video->judul ?? 'Video tidak ditemukan' }}
                                    </td>

                                    <td class="py-3.5 px-4 text-xs text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                        <div>{{ $req->created_at->format('d M Y, H:i') }}</div>
                                        <div class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ $req->created_at->diffForHumans() }}
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                            {{ $req->durasi_diminta ? $req->durasi_diminta . ' Hari' : 'Bebas' }}
                                        </span>
                                    </td>

                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($isPending)
                                            <span
                                                style="background-color: rgba(245, 158, 11, 0.15); color: #fbbf24;"
                                                class="px-2.5 py-1 rounded-full text-xs font-medium inline-block"
                                            >
                                                Menunggu Persetujuan
                                            </span>
                                        @elseif ($isRejected)
                                            <span
                                                style="background-color: rgba(239, 68, 68, 0.15); color: #f87171;"
                                                class="px-2.5 py-1 rounded-full text-xs font-medium inline-block"
                                            >
                                                Ditolak
                                            </span>
                                        @elseif ($isExpired)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 inline-block">
                                                Kedaluwarsa
                                            </span>
                                        @elseif ($isActive)
                                            <span
                                                style="background-color: rgba(16, 185, 129, 0.15); color: #34d399;"
                                                class="px-2.5 py-1 rounded-full text-xs font-medium inline-block"
                                            >
                                                Disetujui (Aktif)
                                            </span>
                                        @else
                                            <span
                                                style="background-color: rgba(59, 130, 246, 0.15); color: #60a5fa;"
                                                class="px-2.5 py-1 rounded-full text-xs font-medium inline-block"
                                            >
                                                Disetujui
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-xs whitespace-nowrap">
                                        @if ($isApproved && $req->grant)
                                            <div class="font-medium text-gray-700 dark:text-gray-300">
                                                {{ $req->grant->tanggal_mulai->format('d-m-Y') }} s/d {{ $req->grant->tanggal_selesai->format('d-m-Y') }}
                                            </div>
                                            @if ($isActive)
                                                <div class="text-[11px] text-emerald-500 dark:text-emerald-400 font-medium mt-0.5">
                                                    Batas akses: {{ $req->grant->tanggal_selesai->format('d-m-Y') }}
                                                </div>
                                            @elseif ($isExpired)
                                                <div class="text-[11px] text-red-400 mt-0.5">
                                                    Akses telah berakhir
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        @if ($isActive)
                                            <a
                                                href="{{ route('customer.videos.index', $req->video) }}"
                                                style="background-color: #4f46e5; color: #ffffff;"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold hover:opacity-90 transition"
                                            >
                                                Tonton
                                            </a>
                                        @elseif ($isRejected || $isExpired)
                                            <button
                                                type="button"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 're-request-modal-{{ $req->id }}')"
                                                style="background-color: rgba(99, 102, 241, 0.15); color: #818cf8;"
                                                class="px-3 py-1.5 rounded-lg text-xs font-medium hover:opacity-90 transition cursor-pointer"
                                            >
                                                Minta Akses Lagi
                                            </button>

                                            {{-- Modal Minta Akses Lagi --}}
                                            <x-modal name="re-request-modal-{{ $req->id }}" maxWidth="md">
                                                <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-left">
                                                    <div class="flex items-start justify-between gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                                                        <div>
                                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                                Minta Akses Ulang
                                                            </h3>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                                Ajukan permohonan baru untuk video <span class="font-medium text-gray-700 dark:text-gray-300">{{ $req->video->judul }}</span>.
                                                            </p>
                                                        </div>

                                                        <button
                                                            type="button"
                                                            x-on:click="$dispatch('close-modal', 're-request-modal-{{ $req->id }}')"
                                                            aria-label="Tutup"
                                                            class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer shrink-0"
                                                        >
                                                            Tutup
                                                        </button>
                                                    </div>

                                                    <form method="POST" action="{{ route('customer.requests.store') }}" class="mt-5 space-y-4">
                                                        @csrf
                                                        <input type="hidden" name="video_id" value="{{ $req->video_id }}">

                                                        <div>
                                                            <x-input-label value="Video yang Dipilih" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                                                            <div class="mt-1.5 px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-800 dark:text-gray-200">
                                                                {{ $req->video->judul }}
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <x-input-label for="re_durasi_{{ $req->id }}" value="Durasi Akses yang Diminta (Hari, Opsional)" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                                                            <x-text-input
                                                                id="re_durasi_{{ $req->id }}"
                                                                name="durasi_diminta"
                                                                type="number"
                                                                min="1"
                                                                max="365"
                                                                class="mt-1.5 block w-full rounded-lg text-sm"
                                                                placeholder="Contoh: 7 (Kosongkan jika bebas)"
                                                                :value="old('durasi_diminta', $req->durasi_diminta)"
                                                            />
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                                                                Berapa hari Anda membutuhkan akses video ini.
                                                            </p>
                                                        </div>

                                                        <div class="flex items-center justify-end gap-2.5 pt-4 mt-2 border-t border-gray-200 dark:border-gray-700">
                                                            <button
                                                                type="button"
                                                                x-on:click="$dispatch('close-modal', 're-request-modal-{{ $req->id }}')"
                                                                class="px-4 py-2 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition cursor-pointer"
                                                            >
                                                                Batal
                                                            </button>

                                                            <button
                                                                type="submit"
                                                                style="background-color: #4f46e5; color: #ffffff;"
                                                                class="px-4 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition cursor-pointer"
                                                            >
                                                                Kirim Permohonan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </x-modal>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500">Menunggu admin</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 px-6 text-center">
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                            @if ($search !== '' || $status !== '')
                                                Permohonan akses tidak ditemukan
                                            @else
                                                Belum ada riwayat permohonan akses
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            @if ($search !== '' || $status !== '')
                                                Coba ubah kata kunci atau filter status, lalu klik Reset.
                                            @else
                                                Klik tombol "+ Buat Permohonan Baru" di atas untuk meminta akses ke video.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($requests->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Pop-up: Buat Permohonan Akses Baru --}}
    <x-modal name="request-create" :show="$errors->any()" maxWidth="md">
        <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            <div class="flex items-start justify-between gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Ajukan Permohonan Akses Video
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Pilih video yang ingin Anda tonton dan tentukan durasi hari yang diinginkan.
                    </p>
                </div>

                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'request-create')"
                    aria-label="Tutup"
                    class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer shrink-0"
                >
                    Tutup
                </button>
            </div>

            <form method="POST" action="{{ route('customer.requests.store') }}" class="mt-5 space-y-4">
                @csrf

                <div>
                    <x-input-label for="video_id" value="Pilih Video" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                    <select
                        id="video_id"
                        name="video_id"
                        required
                        class="mt-1.5 block w-full rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="" disabled selected>-- Pilih salah satu video --</option>
                        @foreach ($availableVideos as $v)
                            <option value="{{ $v->id }}" {{ old('video_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->judul }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('video_id')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="durasi_diminta" value="Durasi Akses yang Diminta (Hari, Opsional)" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                    <x-text-input
                        id="durasi_diminta"
                        name="durasi_diminta"
                        type="number"
                        min="1"
                        max="365"
                        class="mt-1.5 block w-full rounded-lg text-sm"
                        placeholder="Contoh: 7 (Kosongkan jika bebas)"
                        :value="old('durasi_diminta')"
                    />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                        Contoh: masukkan 7 jika membutuhkan akses selama 7 hari. Admin dapat menyesuaikan tanggal akhirnya.
                    </p>
                    <x-input-error :messages="$errors->get('durasi_diminta')" class="mt-1.5" />
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 mt-2 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="button"
                        x-on:click="$dispatch('close-modal', 'request-create')"
                        class="px-4 py-2 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        style="background-color: #4f46e5; color: #ffffff;"
                        class="px-4 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition cursor-pointer"
                    >
                        Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>