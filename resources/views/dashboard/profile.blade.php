<x-dashboard>
    <div class="card">
        <div class="card-aside-wrap">
            <!-- Proile details -->
            <div class="card-inner card-inner-lg">

                <x-dashboard.profile.header />

                <div class="nk-block">
                    <!-- Basic details section -->
                    <div class="nk-data data-list">
                        <div class="data-head">
                            <h6 class="overline-title">Basics</h6>
                        </div>

                        <!-- Name -->
                        <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                            <div class="data-col">
                                <span class="data-label">Full Name</span>
                                <span class="data-value">{{ $user->name }}</span>
                            </div>
                            <!-- Icon -->
                            <div class="data-col data-col-end">
                                <span class="data-more" data-toggle="modal" data-target="#editResourceModal">
                                    <em class="icon ni ni-forward-ios"></em>
                                </a>
                            </div>
                        </div>

                        <!-- Email address -->
                        <div class="data-item">
                            <div class="data-col">
                                <span class="data-label">Email</span>
                                <span class="data-value">{{ $user->email }}</span>
                            </div>
                            <!-- Icon -->
                            <div class="data-col data-col-end">
                                <span class="data-more disable">
                                    <em class="icon ni ni-lock-alt"></em>
                                </span>
                            </div>
                        </div>

                        <!-- Phone number -->
                        <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                            <div class="data-col">
                                <span class="data-label">Phone Number</span>
                                <span class="data-value text-soft">{{ $user->phone ?? 'Not add yet' }}</span>
                            </div>
                            <!-- Icon -->
                            <div class="data-col data-col-end">
                                <span class="data-more" data-toggle="modal" data-target="#editResourceModal">
                                    <em class="icon ni ni-forward-ios"></em>
                                </a>
                            </div>
                        </div>

                        <!-- Date of birth -->
                        <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                            <div class="data-col">
                                <span class="data-label">Date of Birth</span>
                                <span class="data-value">{{ $user->dateOfBirth ?? 'Not added yet' }}</span>
                            </div>
                            <!-- Icon -->
                            <div class="data-col data-col-end">
                                <span class="data-more" data-toggle="modal" data-target="#editResourceModal">
                                    <em class="icon ni ni-forward-ios"></em>
                                </a>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                            <div class="data-col">
                                <span class="data-label">Password</span>
                                <span class="data-value text-soft">************</span>
                            </div>
                            <!-- Icon -->
                            <div class="data-col data-col-end">
                                <span class="data-more" data-toggle="modal" data-target="#editResourceModal">
                                    <em class="icon ni ni-forward-ios"></em>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selection tabs -->
            <x-dashboard.profile.sidebar :user="$user" />
        </div>
    </div>

    <x-slot name="modals">
        <x-dashboard.widgets.modals.modal
            title="Edit Profile"
            :url="route('dashboard.profile.update')"
            method="PUT"
            action="Save"
            id="edit"
        >
            <!-- Name -->
            <x-dashboard.widgets.forms.input
                label="Name"
                name="name"
                id="userName"
                wrapperClass="col-md-6"
                value="{{ $user->name }}"
                placeholder="Enter full name"
                required
            />

            <!-- Phone -->
            <x-dashboard.widgets.forms.input
                label="Phone Number"
                name="phone"
                id="userPhone"
                wrapperClass="col-md-6"
                value="{{ $user->phone }}"
                placeholder="Enter intl phone number"
            />

            <!-- Date of Birth -->
            <x-dashboard.widgets.forms.input
                label="Date of Birth"
                type="text"
                name="dob"
                class="date-picker"
                wrapperClass="col-md-6"
                data-date-format="yyyy-mm-dd"
                id="userDob"
                value="{{ $user->shortDateOfBirth ?? '' }}"
                placeholder="Select date of birth"
            />

            <!-- Password -->
            <x-dashboard.widgets.forms.input
                label="Password"
                name="password"
                id="userPassword"
                widgets="password-visibility"
                wrapperClass="col-md-6"
                placeholder="Enter new password"
            />
        </x-dashboard.widgets.modals.modal>
    </x-slot>
</x-dashboard>
