<?php

namespace App\Livewire\Admin\Investors;

use App\Enums\UserRole;
use App\Models\Investor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Investitor')]
class Form extends Component
{
    use WithFileUploads;

    public ?Investor $investor = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $company_name = '';

    public string $oib = '';

    public string $contact_phone = '';

    public string $contact_email = '';

    public $logo = null;

    public function mount(?Investor $investor = null): void
    {
        $this->investor = $investor;

        if ($investor) {
            $this->name = $investor->user->name;
            $this->email = $investor->user->email;
            $this->company_name = $investor->company_name;
            $this->oib = (string) $investor->oib;
            $this->contact_phone = (string) $investor->contact_phone;
            $this->contact_email = (string) $investor->contact_email;
        }
    }

    protected function rules(): array
    {
        $userId = $this->investor?->user_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => [$this->investor ? 'nullable' : 'required', 'string', 'min:8'],
            'company_name' => ['required', 'string', 'max:255'],
            'oib' => ['nullable', 'string', 'max:32'],
            'contact_phone' => ['nullable', 'string', 'max:64'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            if ($this->investor) {
                $this->investor->user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    ...($validated['password'] ? ['password' => Hash::make($validated['password'])] : []),
                ]);

                $investor = $this->investor;
            } else {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => UserRole::Investor,
                    'email_verified_at' => now(),
                ]);

                $investor = new Investor(['user_id' => $user->id]);
            }

            $investor->fill([
                'company_name' => $validated['company_name'],
                'oib' => $validated['oib'] ?: null,
                'contact_phone' => $validated['contact_phone'] ?: null,
                'contact_email' => $validated['contact_email'] ?: null,
            ]);

            if ($this->logo) {
                $investor->logo_path = $this->logo->store('investors/logos', 'public');
            }

            $investor->save();

            $this->investor = $investor;
        });

        session()->flash('status', 'Investitor je spremljen.');

        $this->redirect(route('admin.investors.show', $this->investor), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.investors.form');
    }
}
