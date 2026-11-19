<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $customers = User::whereHas('role', fn ($q) => $q->where('role_name', 'customer'))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => Role::where('role_name', 'customer')->value('id'),
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function update(Request $request, User $customer)
    {
        $this->ensureCustomer($customer);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($customer->id)],
            'password' => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $customer->name = $data['name'];
        $customer->email = $data['email'];
        if (! empty($data['password'])) {
            $customer->password = Hash::make($data['password']);
        }
        $customer->save();

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(User $customer)
    {
        $this->ensureCustomer($customer);
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil dihapus.');
    }

    // cegah admin mengubah/menghapus akun admin lewat URL customer
    private function ensureCustomer(User $user): void
    {
        abort_if($user->role->role_name !== 'customer', 404);
    }
}
