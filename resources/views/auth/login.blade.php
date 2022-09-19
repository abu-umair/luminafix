<x-auth
    pageTitle="Sign In"
    title="Sign In"
    description="Access the LUMINA user panel using your email and password"
    :url="route('login')"
    action="Sign In"
>
    {{-- Default values to ease development process --}}
    @production
        @php
            $__defaults = [
                'email'    => old('email'),
                'password' => '',
                'remember' => old('remember'),
            ]
        @endphp
    @else
        @php
            $__defaults = [
                // 'email'    => \App\Models\User::firstWhere('role', 'admin')?->email,
                'email'    => \App\Models\User::firstWhere('email', 'kiterative@example.com')?->email,
                'password' => config('lumina.accounts.defaults.password'),
                'remember' => true,
            ]
        @endphp
    @endproduction
    <!-- Email field -->
    <div class="form-group">
        <div class="form-label-group">
            <label class="form-label" for="email-address">Email</label>
        </div>
        <div class="form-control-wrap">
            <input
                type="text"
                name="email"
                class="form-control form-control-lg @error('email') border-danger text-danger @enderror"
                id="email-address"
                autocomplete="off"
                value="{{ $__defaults['email'] }}"
                placeholder="Enter your email address"
                required
            />
            @error('email')
                <div class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>
    </div>

    <!-- Password field -->
    <div class="form-group">
        <div class="form-label-group">
            <label class="form-label" for="password">Password</label>
            {{-- <a
                href="{{ route('password.request') }}"
                class="link link-primary link-sm"
                tabindex="-1"
            >
                Forgot Password?
            </a> --}}
        </div>
        <div class="form-control-wrap">
            <a
                tabindex="-1"
                href="#"
                class="form-icon form-icon-right passcode-switch lg"
                data-target="password"
            >
                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
            </a>
            <input
                type="password"
                name="password"
                class="form-control form-control-lg @error('password') border-danger text-danger @enderror"
                id="password"
                autocomplete="off"
                value="{{ $__defaults['password'] }}"
                placeholder="Enter your password"
                required
            />
            @error('password')
                <div class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        <div class="form-control-group mt-3">
            <div class="custom-control custom-control-xs custom-checkbox">
                <input
                    class="custom-control-input"
                    type="checkbox"
                    name="remember"
                    id="remember"
                    {{ $__defaults['remember'] ? 'checked' : '' }}
                >
                <label class="custom-control-label" for="remember">
                    Remember Me
                </label>
            </div>
        </div>
    </div>
</x-auth>
