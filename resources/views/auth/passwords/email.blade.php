<x-auth
    pageTitle="Recover Password"
    title="Recover Password"
    description="Recover your Lumina account password through your email"
    :url="route('password.email')"
    action="Send Reset Link"
>
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
                value="{{ @old('email') }}"
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
</x-auth>
