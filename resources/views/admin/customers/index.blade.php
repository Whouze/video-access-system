<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="text-left">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Kelola Data Customer') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Kelola akun customer serta pantau status permohonan akses mereka.
                </p>
            </div>

            <button
                type="button"
                x-data
                x-on:click="$dispatch('open-modal', 'customer-create')"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm transition cursor-pointer shrink-0"
            >
                + Tambah Customer
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            {{-- Search Bar --}}
            <form
                method="GET"
                action="{{ route('admin.customers.index') }}"
                class="bg-white dark:bg-gray-800 p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center gap-3"
            >
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama atau email customer..."
                    aria-label="Cari nama atau email customer"
                    class="flex-1 rounded-lg px-3.5 py-2 text-sm bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white transition cursor-pointer"
                    >
                        Cari
                    </button>

                    @if ($search !== '')
                        <a
                            href="{{ route('admin.customers.index') }}"
                            class="inline-flex items-center justify-center px-3.5 py-2 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Customer Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($customers as $customer)
                    @php
                        $hasPending = isset($customer->pending_requests_count)
                            ? $customer->pending_requests_count > 0
                            : $customer->accessRequests()->where('status', 'pending')->exists();

                        $initial = mb_strtoupper(mb_substr(trim($customer->name), 0, 1));
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 flex flex-col justify-between gap-4">
                        {{-- Header Card --}}
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="min-w-0 flex-1 text-left">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate" title="{{ $customer->name }}">
                                    {{ $customer->name }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" title="{{ $customer->email }}">
                                    {{ $customer->email }}
                                </p>
                            </div>
                        </div>

                        {{-- Info Tanggal & Status --}}
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700/70 flex items-center justify-between gap-2 text-xs">
                            <span class="text-gray-500 dark:text-gray-400">
                                Terdaftar:
                                <span class="font-medium text-gray-700 dark:text-gray-200">
                                    {{ $customer->created_at?->format('d M Y') ?? '-' }}
                                </span>
                            </span>

                            @if ($hasPending)
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/15 text-amber-400 border border-amber-500/30 whitespace-nowrap">
                                    Ada Permohonan Menunggu
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700/70 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    Tidak ada antrean
                                </span>
                            @endif
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700/70 flex items-center justify-end gap-2">
                            <button
                                type="button"
                                x-data
                                x-on:click="$dispatch('open-modal', 'customer-edit-{{ $customer->id }}')"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition cursor-pointer"
                            >
                                Edit
                            </button>

                            <form
                                method="POST"
                                action="{{ route('admin.customers.destroy', $customer) }}"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus customer ini? Semua data permohonan aksesnya juga akan terhapus.');"
                                class="inline-flex m-0"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-red-500/15 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/30 transition cursor-pointer"
                                >
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    @include('admin.customers.edit', ['customer' => $customer])
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 py-12 px-6 text-center rounded-xl border border-gray-200 dark:border-gray-700">
                        <p class="text-base font-semibold text-gray-700 dark:text-gray-200">
                            @if ($search !== '')
                                Customer tidak ditemukan
                            @else
                                Belum ada data customer
                            @endif
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            @if ($search !== '')
                                Coba gunakan kata kunci lain atau klik tombol Reset.
                            @else
                                Klik tombol "+ Tambah Customer" di atas untuk menambahkan customer pertama.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($customers->hasPages())
                <div class="pt-2">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>

    @include('admin.customers.create')
</x-app-layout>