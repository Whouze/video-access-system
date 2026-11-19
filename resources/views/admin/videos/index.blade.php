<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-left">
                <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Kelola Data Video') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Kelola koleksi video, pratinjau file, serta perbarui informasi konten.
                </p>
            </div>

            <button
                type="button"
                x-data
                x-on:click="$dispatch('open-modal', 'video-create')"
                style="background-color: #4f46e5; color: #ffffff;"
                class="self-start sm:self-auto px-3.5 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition cursor-pointer shrink-0"
            >
                + Tambah Video
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
            <x-alert />

            {{-- Search Form --}}
            <form
                method="GET"
                action="{{ route('admin.videos.index') }}"
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
                            href="{{ route('admin.videos.index') }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Video Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($videos as $video)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col justify-between">
                        <div>
                            {{-- Video Player --}}
                            <div class="relative aspect-video w-full bg-black border-b border-gray-200 dark:border-gray-700">
                                <video
                                    controls
                                    preload="metadata"
                                    class="w-full h-full object-contain"
                                >
                                    <source src="{{ route('admin.videos.stream', $video) }}" type="video/mp4">
                                    Browser Anda tidak mendukung pemutar video HTML5.
                                </video>
                            </div>

                            {{-- Video Info --}}
                            <div class="p-4 text-left">
                                <h3 class="font-semibold text-sm text-gray-900 dark:text-gray-100 line-clamp-2 leading-snug" title="{{ $video->judul }}">
                                    {{ $video->judul }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ $video->keterangan ?: 'Tanpa deskripsi' }}
                                </p>
                            </div>
                        </div>

                        {{-- Footer: Tanggal & Aksi --}}
                        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700/70 flex items-center justify-between gap-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400 shrink-0">
                                {{ $video->created_at?->format('d M Y') ?? '-' }}
                            </span>

                            <div class="flex items-center gap-1.5 shrink-0">
                                <button
                                    type="button"
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'video-edit-{{ $video->id }}')"
                                    class="px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition cursor-pointer"
                                >
                                    Edit
                                </button>

                                <form
                                    method="POST"
                                    action="{{ route('admin.videos.destroy', $video) }}"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus video ini?');"
                                    class="inline-flex m-0"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        style="background-color: rgba(239, 68, 68, 0.15); color: #f87171;"
                                        class="px-2.5 py-1 rounded-md text-xs font-medium hover:opacity-90 transition cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    @include('admin.videos.edit', ['video' => $video])
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 py-10 px-6 text-center rounded-xl border border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            @if ($search !== '')
                                Video tidak ditemukan
                            @else
                                Belum ada data video
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            @if ($search !== '')
                                Coba gunakan kata kunci lain atau klik tombol Reset.
                            @else
                                Klik tombol "+ Tambah Video" di atas untuk mengunggah video pertama.
                            @endif
                        </p>
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

    @include('admin.videos.create')
</x-app-layout>