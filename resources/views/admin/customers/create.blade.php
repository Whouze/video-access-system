<x-modal name="customer-create" :show="$errors->any() && old('_form_mode') === 'create'" maxWidth="md">
    <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
        {{-- Modal Header --}}
        <div class="flex items-start justify-between gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Tambah Customer
                </h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    Isi data customer baru.
                </p>
            </div>

            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'customer-create')"
                aria-label="Tutup"
                class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer shrink-0"
            >
                Tutup
            </button>
        </div>

        {{-- Form --}}
        <form
            method="POST"
            action="{{ route('admin.customers.store') }}"
            class="mt-5 space-y-4"
            x-data="{
                password: '',
                touched: false,
                get passwordMessage() {
                    if (this.password.length < 8) return 'Gunakan minimal 8 karakter.';
                    if (!/[a-z]/.test(this.password) || !/[A-Z]/.test(this.password)) return 'Tambahkan minimal satu huruf kecil dan satu huruf kapital.';
                    if (!/[0-9]/.test(this.password)) return 'Tambahkan minimal satu angka.';
                    if (!/[^A-Za-z0-9]/.test(this.password)) return 'Tambahkan minimal satu simbol.';
                    return '';
                },
                updatePassword(event) {
                    this.password = event.target.value;
                    this.touched = true;
                    event.target.setCustomValidity(this.passwordMessage);
                }
            }"
            x-on:submit="if (!$refs.password.checkValidity()) { $event.preventDefault(); touched = true; $refs.password.reportValidity(); }"
        >
            @csrf
            <input type="hidden" name="_form_mode" value="create">

            <div>
                <x-input-label for="new-customer-name" value="Nama" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                <x-text-input
                    id="new-customer-name"
                    name="name"
                    type="text"
                    class="mt-1.5 block w-full rounded-lg text-sm"
                    :value="old('_form_mode') === 'create' ? old('name') : ''"
                    required
                    maxlength="255"
                    placeholder="Masukkan nama customer..."
                />
                <x-input-error :messages="old('_form_mode') === 'create' ? $errors->get('name') : []" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="new-customer-email" value="Email" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                <x-text-input
                    id="new-customer-email"
                    name="email"
                    type="email"
                    class="mt-1.5 block w-full rounded-lg text-sm"
                    :value="old('_form_mode') === 'create' ? old('email') : ''"
                    required
                    maxlength="255"
                    placeholder="nama@email.com"
                />
                <x-input-error :messages="old('_form_mode') === 'create' ? $errors->get('email') : []" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="new-customer-password" value="Password" class="text-xs font-medium text-gray-700 dark:text-gray-300" />
                <x-text-input
                    id="new-customer-password"
                    name="password"
                    type="password"
                    class="mt-1.5 block w-full rounded-lg text-sm"
                    x-ref="password"
                    x-on:input="updatePassword($event)"
                    required
                    autocomplete="new-password"
                />
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    Minimal 8 karakter dengan kombinasi huruf besar, huruf kecil, angka, dan simbol.
                </p>
                <p
                    x-show="touched && passwordMessage"
                    x-text="passwordMessage"
                    class="mt-1.5 text-xs font-medium text-red-500 dark:text-red-400"
                    aria-live="polite"
                ></p>
                <x-input-error :messages="old('_form_mode') === 'create' && $errors->has('password') ? [$errors->first('password')] : []" class="mt-1.5" />
            </div>

            {{-- Footer Actions --}}
            <div class="flex items-center justify-end gap-2.5 pt-4 mt-2 border-t border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'customer-create')"
                    class="px-4 py-2 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    style="background-color: #4f46e5; color: #ffffff;"
                    class="px-4 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition cursor-pointer"
                >
                    Tambah Customer
                </button>
            </div>
        </form>
    </div>
</x-modal>