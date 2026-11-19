<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-left">
                <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Video Aktif') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Daftar video yang izin aksesnya sedang aktif untuk akun Anda.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
            <x-alert />

            {{-- Search Form --}}
            <form
                method="GET"
                action="{{ route('customer.videos.index') }}"
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

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="submit"
                        style="background-color: #4f46e5; color: #ffffff;"
                        class="px-4 py-1.5 rounded-lg text-sm font-medium hover:opacity-90 transition cursor-pointer"
                    >
                        Cari
                    </button>

                    @if ($search !== '')
                        <a
                            href="{{ route('customer.videos.index') }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Grid Video Aktif --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($videos as $video)
                    @php
                        $grant = $video->accessRequests->first()?->grant;
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col justify-between">
                        <div>
                            {{-- Pemutar Video --}}
                            <div class="relative aspect-video w-full bg-black border-b border-gray-200 dark:border-gray-700">
                                <video
                                    controls
                                    preload="metadata"
                                    class="w-full h-full object-contain"
                                >
                                    <source src="{{ route('customer.videos.stream', $video) }}" type="video/mp4">
                                    Browser Anda tidak mendukung pemutar video HTML5.
                                </video>
                            </div>

                            {{-- Detail Video --}}
                            <div class="p-4 text-left">
                                @if ($grant)
                                    <div class="mb-2">
                                        <span
                                            style="background-color: rgba(16, 185, 129, 0.15); color: #34d399;"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                                        >
                                            <svg style="width: 12px; height: 12px;" class="fill-current shrink-0" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            Batas akses: {{ $grant->tanggal_selesai->format('d-m-Y') }}
                                        </span>
                                    </div>
                                @endif

                                <h3 class="font-semibold text-sm text-gray-900 dark:text-gray-100 line-clamp-2 leading-snug" title="{{ $video->judul }}">
                                    {{ $video->judul }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ $video->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                                </p>
                            </div>
                        </div>

                        {{-- Footer Info Masa Aktif --}}
                        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700/70 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>
                                Aktif sejak {{ $grant?->tanggal_mulai?->format('d M Y') ?? '-' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 py-12 px-6 text-center rounded-xl border border-gray-200 dark:border-gray-700">
                        <div
                            style="width: 48px; height: 48px; background-color: rgba(99, 102, 241, 0.15); color: #818cf8;"
                            class="mx-auto mb-3 rounded-full flex items-center justify-center"
                        >
                            <svg style="width: 24px; height: 24px;" class="fill-current" viewBox="0 0 24 24">
                                <path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/>
                            </svg>
                        </div>

                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            @if ($search !== '')
                                Video aktif tidak ditemukan
                            @else
                                Belum Ada Video dengan Akses Aktif
                            @endif
                        </h3>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-md mx-auto">
                            @if ($search !== '')
                                Coba gunakan kata kunci judul lain atau klik tombol Reset.
                            @else
                                Video yang izinnya sudah disetujui oleh admin akan otomatis tampil di sini dan hilang jika masa aksesnya berakhir.
                            @endif
                        </p>

                        @if ($search === '')
                            <div class="mt-5">
                                <a
                                    href="{{ route('customer.requests.index') }}"
                                    style="background-color: #4f46e5; color: #ffffff;"
                                    class="inline-flex items-center px-3.5 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition"
                                >
                                    + Ajukan Permohonan Akses Video
                                </a>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($videos->hasPages())
                <div class="pt-2">
                    {{ $videos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>