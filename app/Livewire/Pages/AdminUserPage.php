<?php

namespace App\Livewire\Pages;

use App\Models\Prodi;
use App\Models\User;
use App\Services\ProdiProvisioner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Manajemen User')]
class AdminUserPage extends Component
{
    public ?int $editingId = null;

    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $role = 'sekprodi';

    public ?int $prodi_id = null;

    public string $password = '';

    public string $newProdiCode = '';

    public string $newProdiName = '';

    public string $newKaprodiName = '';

    public string $newKaprodiUsername = '';

    public string $newKaprodiEmail = '';

    public string $newKaprodiPassword = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->prodi_id = Prodi::query()->where('code', '!=', 'ADMIN')->value('id');
    }

    public function edit(int $id): void
    {
        $user = User::query()->findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->username = (string) $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->prodi_id = $user->prodi_id;
        $this->password = '';
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'username', 'email', 'password']);
        $this->role = 'sekprodi';
        $this->prodi_id = Prodi::query()->where('code', '!=', 'ADMIN')->value('id');
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:users,username,'.($this->editingId ?? 'NULL')],
            'email' => ['required', 'email', 'max:160', 'unique:users,email,'.($this->editingId ?? 'NULL')],
            'role' => ['required', 'in:admin,kaprodi,sekprodi'],
            'prodi_id' => [$this->role === 'admin' ? 'nullable' : 'required', 'exists:prodis,id'],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8'],
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, strip, dan underscore.',
        ]);

        if ($this->role !== 'admin') {
            $selectedProdi = Prodi::query()->findOrFail($this->prodi_id);
            if ($selectedProdi->code === 'ADMIN') {
                $this->addError('prodi_id', 'Kaprodi atau Sekprodi harus terhubung ke Program Studi.');

                return;
            }
        }
        if ($this->role === 'admin') {
            $this->prodi_id = Prodi::query()->where('code', 'ADMIN')->value('id');
        }

        $payload = ['name' => $this->name, 'username' => strtolower($this->username), 'email' => strtolower($this->email), 'role' => $this->role, 'prodi_id' => $this->prodi_id];
        if ($this->password !== '') {
            $payload['password'] = Hash::make($this->password);
        }
        User::query()->updateOrCreate(['id' => $this->editingId], $payload);
        session()->flash('status', 'User berhasil disimpan.');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $user = User::query()->findOrFail($id);
        if ($user->email === env('ADMIN_EMAIL', 'admin@prodi.local')) {
            return;
        }
        $user->delete();
        session()->flash('status', 'User berhasil dihapus.');
    }

    public function deleteProdi(int $id): void
    {
        $prodi = Prodi::query()->whereKey($id)->where('code', '!=', 'ADMIN')->firstOrFail();

        DB::transaction(function () use ($prodi): void {
            // Hapus seluruh data turunan agar tidak meninggalkan data yatim.
            foreach ([
                'dashboard_monthly_stats', 'dashboard_year_stats', 'dashboard_program_items',
                'annual_report_sections', 'home_page_settings', 'profile_sections',
                'document_items', 'contact_feedback', 'users',
            ] as $table) {
                DB::table($table)->where('prodi_id', $prodi->id)->delete();
            }
            $prodi->delete();
        });

        if ((int) session('admin_prodi_id') === $prodi->id) {
            session()->forget(['admin_prodi_id', 'public_prodi_id']);
        }
        session()->flash('status', 'Program Studi '.$prodi->name.' dan seluruh data terkait berhasil dihapus.');
    }

    public function createProdiWithKaprodi(ProdiProvisioner $provisioner): void
    {
        $this->newProdiCode = strtoupper(trim($this->newProdiCode));
        $this->newProdiName = trim($this->newProdiName);
        $this->newKaprodiName = trim($this->newKaprodiName);
        $this->newKaprodiUsername = strtolower(trim($this->newKaprodiUsername));
        $this->newKaprodiEmail = strtolower(trim($this->newKaprodiEmail));

        $validated = $this->validate([
            'newProdiCode' => ['required', 'string', 'max:20', 'alpha_dash', 'unique:prodis,code'],
            'newProdiName' => ['required', 'string', 'max:160'],
            'newKaprodiName' => ['required', 'string', 'max:120'],
            'newKaprodiUsername' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:users,username'],
            'newKaprodiEmail' => ['required', 'email', 'max:160', 'unique:users,email'],
            'newKaprodiPassword' => ['required', 'string', 'min:8'],
        ], [
            'newKaprodiUsername.regex' => 'Username kaprodi hanya boleh berisi huruf, angka, titik, strip, dan underscore.',
        ]);

        DB::transaction(function () use ($validated, $provisioner): void {
            $prodi = Prodi::query()->create([
                'code' => strtoupper($validated['newProdiCode']),
                'name' => $validated['newProdiName'],
                'is_active' => true,
            ]);

            $provisioner->cloneStarterData($prodi, $validated['newKaprodiName']);

            User::query()->create([
                'name' => $validated['newKaprodiName'],
                'username' => $validated['newKaprodiUsername'],
                'email' => strtolower($validated['newKaprodiEmail']),
                'role' => 'kaprodi',
                'prodi_id' => $prodi->id,
                'password' => Hash::make($validated['newKaprodiPassword']),
            ]);
        });

        $this->reset(['newProdiCode', 'newProdiName', 'newKaprodiName', 'newKaprodiUsername', 'newKaprodiEmail', 'newKaprodiPassword']);
        session()->flash('status', 'Program Studi dan akun Kaprodi berhasil dibuat beserta data awalnya.');
    }

    public function render()
    {
        return view('livewire.pages.admin-user-page', [
            'users' => User::query()->with('prodi')->orderBy('name')->get(),
            'prodis' => Prodi::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
