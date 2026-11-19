<x-modal name="video-create" :show="$errors->any() && old('_form_mode') === 'create'" maxWidth="md">
    <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
        {{-- Modal Header --}}
        <div class="flex items-start justify-between gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Tambah Video
                </h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    Isi data video baru.
                </p>
            </div>

            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'video-create')"
                aria-label="Tutup"
                class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer shrink-0"
            >
                Tutup
            </button>
        </div>

        {{-- Form --}}
        <form
            method="POST"
            action="{{ route('admin.videos.store') }}"
            enctype="multipart/form-data"
            class="mt-5 space-y-4"
        >
            @csrf
            <input type="hidden" name="_form_mode" value="create">

            <div>
                <x-input-label for="new-video-judul" value="Judul Video" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                <x-text-input
                    id="new-video-judul"
                    name="judul"
                    type="text"
                    class="mt-1.5 block w-full rounded-lg text-sm"
                    :value="old('_form_mode') === 'create' ? old('judul') : ''"
                    required
                    maxlength="255"
                    placeholder="Contoh: Tutorial Laravel"
                />
                <x-input-error :messages="old('_form_mode') === 'create' ? $errors->get('judul') : []" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="new-video-keterangan" value="Keterangan (Opsional)" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                <x-textarea
                    id="new-video-keterangan"
                    name="keterangan"
                    class="mt-1.5 block w-full rounded-lg text-sm"
                    :value="old('_form_mode') === 'create' ? old('keterangan') : ''"
                    rows="3"
                    placeholder="Deskripsi singkat video..."
                />
                <x-input-error :messages="old('_form_mode') === 'create' ? $errors->get('keterangan') : []" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="file_path" value="Upload File Video" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                <input
                    type="file"
                    id="file_path"
                    name="file_path"
                    accept="video/mp4,video/quicktime,video/x-msvideo"
                    required
                    class="mt-1.5 block w-full text-xs text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer file:mr-3 file:py-2 file:px-3.5 file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500"
                >
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    Format: MP4, MOV, AVI (Maksimal 100 MB).
                </p>
                <x-input-error :messages="$errors->get('file_path')" class="mt-1.5" />
            </div>

            {{-- Footer Actions --}}
            <div class="flex items-center justify-end gap-2.5 pt-4 mt-2 border-t border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'video-create')"
                    class="px-4 py-2 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    style="background-color: #4f46e5; color: #ffffff;"
                    class="px-4 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition cursor-pointer"
                >
                    Upload Video
                </button>
            </div>
        </form>
    </div>
</x-modal>
