<x-modal
    name="video-edit-{{ $video->id }}"
    :show="$errors->any() && old('_form_mode') === 'edit' && old('_video_id') == $video->id"
    maxWidth="md"
>
    <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
        {{-- Modal Header --}}
        <div class="flex items-start justify-between gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Edit Video
                </h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    Perbarui data <span class="font-medium text-gray-700 dark:text-gray-300">{{ $video->judul }}</span>.
                </p>
            </div>

            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'video-edit-{{ $video->id }}')"
                aria-label="Tutup"
                class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer shrink-0"
            >
                Tutup
            </button>
        </div>

        {{-- Form --}}
        <form
            method="POST"
            action="{{ route('admin.videos.update', $video) }}"
            enctype="multipart/form-data"
            class="mt-5 space-y-4"
        >
            @csrf
            @method('PUT')
            <input type="hidden" name="_form_mode" value="edit">
            <input type="hidden" name="_video_id" value="{{ $video->id }}">

            <div>
                <x-input-label for="video-judul-{{ $video->id }}" value="Judul Video" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                <x-text-input
                    id="video-judul-{{ $video->id }}"
                    name="judul"
                    type="text"
                    class="mt-1.5 block w-full rounded-lg text-sm"
                    :value="old('_video_id') == $video->id ? old('judul', $video->judul) : $video->judul"
                    required
                    maxlength="255"
                />
                <x-input-error :messages="old('_video_id') == $video->id ? $errors->get('judul') : []" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="video-keterangan-{{ $video->id }}" value="Keterangan (Opsional)" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                <x-textarea
                    id="video-keterangan-{{ $video->id }}"
                    name="keterangan"
                    class="mt-1.5 block w-full rounded-lg text-sm"
                    :value="old('_video_id') == $video->id ? old('keterangan', $video->keterangan) : $video->keterangan"
                    rows="3"
                    placeholder="Deskripsi singkat video..."
                />
                <x-input-error :messages="old('_video_id') == $video->id ? $errors->get('keterangan') : []" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="file_path_{{ $video->id }}" value="Ganti File Video (Opsional)" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                <input
                    type="file"
                    id="file_path_{{ $video->id }}"
                    name="file_path"
                    accept="video/mp4,video/quicktime,video/x-msvideo"
                    class="mt-1.5 block w-full text-xs text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer file:mr-3 file:py-2 file:px-3.5 file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500"
                >
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    Kosongkan jika tidak ingin mengganti file video. Maksimal 100 MB.
                </p>
                <x-input-error :messages="old('_video_id') == $video->id ? $errors->get('file_path') : []" class="mt-1.5" />
            </div>

            {{-- Footer Actions --}}
            <div class="flex items-center justify-end gap-2.5 pt-4 mt-2 border-t border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'video-edit-{{ $video->id }}')"
                    class="px-4 py-2 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    style="background-color: #4f46e5; color: #ffffff;"
                    class="px-4 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition cursor-pointer"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-modal>
