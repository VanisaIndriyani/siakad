<x-portal-layout :title="'Profil - '.config('app.name')" subtitle="Pengaturan Akun">
    <x-slot:sidebar>
        @php
            $role = auth()->user()->role ?? null;
        @endphp

        @if (in_array($role, ['admin', 'keuangan'], true))
            @include('admin.partials.sidebar')
        @elseif ($role === 'mahasiswa')
            @include('mahasiswa.partials.sidebar')
        @elseif ($role === 'dosen')
            @include('dosen.partials.sidebar')
        @else
            @include('admin.partials.sidebar')
        @endif
    </x-slot:sidebar>

    <div style="max-width: 980px; margin: 0 auto; display: flex; flex-direction: column; gap: 22px; padding-bottom: 50px;">
        <div style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px;">
            <div>
                <h1 style="color: white; font-size: 1.7rem; font-weight: 900; margin: 0; letter-spacing: -0.5px;">EDIT PROFIL</h1>
                <div style="margin-top: 6px; color: rgba(52,211,153,0.6); font-size: 13px; font-weight: 600;">
                    Ubah nama, email login, dan password akun.
                </div>
            </div>
        </div>

        @if (session('status') === 'profile-updated')
            <div style="border: 1px solid rgba(16,185,129,0.22); background-color: rgba(16,185,129,0.10); color: #d1fae5; padding: 12px 14px; border-radius: 14px; font-weight: 700; font-size: 13px;">
                Profil berhasil diupdate.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div style="border: 1px solid rgba(16,185,129,0.22); background-color: rgba(16,185,129,0.10); color: #d1fae5; padding: 12px 14px; border-radius: 14px; font-weight: 700; font-size: 13px;">
                Password berhasil diupdate.
            </div>
        @endif

        <div style="background-color: #0d2a23; border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 18px;">
            <div style="color: white; font-size: 14px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase;">
                Data Akun
            </div>
            <div style="margin-top: 12px;">
                <form method="POST" action="{{ route('profile.update') }}" style="display: flex; flex-direction: column; gap: 14px;">
                    @csrf
                    @method('PATCH')

                    <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                        <div>
                            <label for="name" style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 800; color: rgba(255,255,255,0.85); letter-spacing: 0.6px; text-transform: uppercase;">Nama</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"
                                   style="width: 100%; height: 48px; background-color: #0a1f1a; color: white; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12); padding: 0 14px; font-weight: 600; outline: none;" />
                            @error('name')
                                <div style="margin-top: 6px; color: #fca5a5; font-size: 12px; font-weight: 700;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="email" style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 800; color: rgba(255,255,255,0.85); letter-spacing: 0.6px; text-transform: uppercase;">Email Login</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                                   style="width: 100%; height: 48px; background-color: #0a1f1a; color: white; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12); padding: 0 14px; font-weight: 600; outline: none;" />
                            @error('email')
                                <div style="margin-top: 6px; color: #fca5a5; font-size: 12px; font-weight: 700;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 6px;">
                        <button type="submit"
                                style="height: 44px; padding: 0 18px; border-radius: 12px; background: linear-gradient(to right, #059669, #10b981); color: white; font-weight: 900; font-size: 12px; letter-spacing: 1px; text-transform: uppercase; border: 1px solid rgba(255,255,255,0.12); cursor: pointer;">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div style="background-color: #0d2a23; border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 18px;">
            <div style="color: white; font-size: 14px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase;">
                Ubah Password
            </div>
            <div style="margin-top: 12px;">
                <form method="POST" action="{{ route('password.update') }}" style="display: flex; flex-direction: column; gap: 14px;">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 800; color: rgba(255,255,255,0.85); letter-spacing: 0.6px; text-transform: uppercase;">Password Lama</label>
                        <div style="position: relative;">
                            <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                                   style="width: 100%; height: 48px; background-color: #0a1f1a; color: white; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12); padding: 0 44px 0 14px; font-weight: 600; outline: none;" />
                            <button type="button" onclick="togglePassword('current_password', this)" aria-label="Tampilkan password lama" title="Tampilkan"
                                    style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); height: 34px; width: 34px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.10); background-color: rgba(255,255,255,0.04); color: rgba(255,255,255,0.75); cursor: pointer;">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @if ($errors->updatePassword->has('current_password'))
                            <div style="margin-top: 6px; color: #fca5a5; font-size: 12px; font-weight: 700;">
                                {{ $errors->updatePassword->first('current_password') }}
                            </div>
                        @endif
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                        <div>
                            <label for="password" style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 800; color: rgba(255,255,255,0.85); letter-spacing: 0.6px; text-transform: uppercase;">Password Baru</label>
                            <div style="position: relative;">
                                <input id="password" name="password" type="password" autocomplete="new-password"
                                       style="width: 100%; height: 48px; background-color: #0a1f1a; color: white; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12); padding: 0 44px 0 14px; font-weight: 600; outline: none;" />
                                <button type="button" onclick="togglePassword('password', this)" aria-label="Tampilkan password baru" title="Tampilkan"
                                        style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); height: 34px; width: 34px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.10); background-color: rgba(255,255,255,0.04); color: rgba(255,255,255,0.75); cursor: pointer;">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            @if ($errors->updatePassword->has('password'))
                                <div style="margin-top: 6px; color: #fca5a5; font-size: 12px; font-weight: 700;">
                                    {{ $errors->updatePassword->first('password') }}
                                </div>
                            @endif
                        </div>

                        <div>
                            <label for="password_confirmation" style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 800; color: rgba(255,255,255,0.85); letter-spacing: 0.6px; text-transform: uppercase;">Konfirmasi Password</label>
                            <div style="position: relative;">
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                       style="width: 100%; height: 48px; background-color: #0a1f1a; color: white; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12); padding: 0 44px 0 14px; font-weight: 600; outline: none;" />
                                <button type="button" onclick="togglePassword('password_confirmation', this)" aria-label="Tampilkan konfirmasi password" title="Tampilkan"
                                        style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); height: 34px; width: 34px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.10); background-color: rgba(255,255,255,0.04); color: rgba(255,255,255,0.75); cursor: pointer;">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            @if ($errors->updatePassword->has('password_confirmation'))
                                <div style="margin-top: 6px; color: #fca5a5; font-size: 12px; font-weight: 700;">
                                    {{ $errors->updatePassword->first('password_confirmation') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 6px;">
                        <button type="submit"
                                style="height: 44px; padding: 0 18px; border-radius: 12px; background: linear-gradient(to right, #2563eb, #3b82f6); color: white; font-weight: 900; font-size: 12px; letter-spacing: 1px; text-transform: uppercase; border: 1px solid rgba(255,255,255,0.12); cursor: pointer;">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            window.togglePassword = function (inputId, buttonEl) {
                const input = document.getElementById(inputId);
                if (!input) return;
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';

                if (!(buttonEl instanceof HTMLElement)) return;
                const icon = buttonEl.querySelector('i');
                if (icon) {
                    icon.classList.remove(isPassword ? 'fa-eye' : 'fa-eye-slash');
                    icon.classList.add(isPassword ? 'fa-eye-slash' : 'fa-eye');
                }
                buttonEl.title = isPassword ? 'Sembunyikan' : 'Tampilkan';
                buttonEl.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            };
        })();
    </script>
</x-portal-layout>
