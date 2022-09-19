<x-dashboard title="User Management" pageTitle="User Management">
    <x-slot name="header">
        <x-dashboard.widgets.modals.button
            label="Add User"
        />
    </x-slot>

    <table
        class="datatable-init nk-tb-list nk-tb-ulist no-footer"
        data-auto-responsive="false"
        id="user_management_datatable"
        aria-describedby="user_management_datatable_info">
        <thead>
            <tr>
                <!-- User ID -->
                <th
                    class="nk-tb-col sorting"
                    tabindex="-1"
                    aria-controls="user_management_datatable"
                    rowspan="1"
                    colspan="1"
                    aria-label="ID: activate to sort column ascending"
                >
                    <span class="sub-text">ID</span>
                </th>

                <!-- Name + Avatar -->
                <th
                    class="nk-tb-col sorting"
                    tabindex="-1"
                    aria-controls="user_management_datatable"
                    rowspan="1"
                    colspan="1"
                    aria-label="User: activate to sort column ascending"
                >
                    <span class="sub-text">User</span>
                </th>


                <!-- Phone -->
                <th
                    class="nk-tb-col tb-col-md sorting"
                    tabindex="-1"
                    aria-controls="user_management_datatable"
                    rowspan="1"
                    colspan="1"
                    aria-label="Phone: activate to sort column ascending"
                >
                    <span class="sub-text">Phone</span>
                </th>

                <!-- Date of Birth -->
                <th
                    class="nk-tb-col tb-col-lg sorting"
                    tabindex="-1"
                    aria-controls="user_management_datatable"
                    rowspan="1"
                    colspan="1"
                    aria-label="Date of Birth: activate to sort column ascending"
                >
                    <span class="sub-text">Date of Birth</span>
                </th>

                <!-- Role -->
                <th
                    class="nk-tb-col tb-col-md sorting"
                    tabindex="-1"
                    aria-controls="user_management_datatable"
                    rowspan="1"
                    colspan="1"
                    aria-label="Role: activate to sort column ascending"
                >
                    <span class="sub-text">Role</span>
                </th>

                <!-- Workload -->
                <th
                    class="nk-tb-col tb-col-md sorting"
                    tabindex="-1"
                    aria-controls="user_management_datatable"
                    rowspan="1"
                    colspan="1"
                    aria-label="Workload: activate to sort column ascending"
                >
                    <span class="sub-text">Workload</span>
                </th>

                <!-- Actions -->
                <th
                    class="nk-tb-col nk-tb-col-tools text-right"
                    tabindex="-1"
                    aria-controls="user_management_datatable"
                    rowspan="1"
                    colspan="1">
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="nk-tb-item {{ $loop->even ? 'even' : 'odd' }}">
                    <!-- User ID -->
                    <td class="nk-tb-col">{{ $user->id }}</td>

                    <!-- Name + Avatar -->
                    <td class="nk-tb-col">
                        <div class="user-card">
                            <!-- Avatar -->
                            <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                                <span>AB</span>
                            </div>

                            <!-- Details -->
                            <div class="user-info">
                                <span class="tb-lead">{{ $user->name }}</span>
                                <a
                                    href="mailto:{{ $user->email }}"
                                    class="text-muted"
                                    data-toggle="tooltip"
                                    data-placement="right"
                                    title="Click to send email"
                                    target="_blank"
                                >
                                    <small>{{ $user->email }}</small>
                                </a>
                            </div>
                        </div>
                    </td>

                    <!-- Phone number -->
                    <td class="nk-tb-col tb-col-md">
                        @if ($user->phone)
                            <a
                                href="tel:{{ $user->phone }}"
                                class="text-muted"
                                data-toggle="tooltip"
                                data-placement="top"
                                title="Click to call"
                            >
                                {{ $user->phone }}
                            </a>
                        @else
                            <span>Not added yet</span>
                        @endif
                        <span></span>
                    </td>

                    <!-- Date of Birth -->
                    <td class="nk-tb-col tb-col-lg">
                        <span>{{ $user->dateOfBirth ?? 'Not added yet' }}</span>
                    </td>

                    <!-- Role -->
                    <td class="nk-tb-col tb-col-md">
                        @if ($user->isAdmin)
                            <strong class="text-primary">
                                Administrator
                            </strong>
                        @else
                            <span>Normal User</span>
                        @endif
                    </td>

                    <td class="nk-tb-col tb-col-md">
                        {{ $user->workload ?: 'None' }}
                    </td>

                    <!-- Actions -->
                    <td class="nk-tb-col nk-tb-col-tools">
                        <div class="drodown">
                            <a
                                href="#"
                                class="dropdown-toggle btn btn-icon btn-trigger"
                                data-toggle="dropdown"
                            >
                                <em class="icon ni ni-more-v"></em>
                            </a>

                            <!-- Content -->
                            <div class="dropdown-menu dropdown-menu-right">
                                <ul
                                    class="link-list-opt no-bdr"
                                    data-resource="{{ $user->toJson() }}"
                                >
                                    <!-- Update -->
                                    <li>
                                        <a
                                            href="#"
                                            data-toggle="modal"
                                            data-target="#editResourceModal"
                                            onclick="populateFields(this)"
                                        >
                                            <em class="icon ni ni-edit"></em>
                                            <span>Edit</span>
                                        </a>
                                    </li>

                                    <!-- Delete -->
                                    <li>
                                        <a
                                            href="#"
                                            data-toggle="modal"
                                            data-target="#deleteResourceModal"
                                            onclick="populateFields(this)"
                                        >
                                            <em class="icon ni ni-trash"></em>
                                            <span>Delete</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <x-slot name="modals">
        <x-dashboard.widgets.modals.modal
            title="Add User"
            :url="route('dashboard.users.create')"
        >

            {{-- Generate fake data if not in production --}}
            @production
                @php
                    $__defaults = [
                        'name'     => old('name'),
                        'email'    => old('email'),
                        'phone'    => old('phone'),
                        'password' => '',
                    ];
                @endphp
            @else
                @php
                    $faker = Faker\Factory::create();

                    $__defaults = [
                        'name'     => $faker->name(),
                        'email'    => $faker->unique()->safeEmail(),
                        'phone'    => $faker->e164PhoneNumber(),
                        'password' => config('lumina.accounts.defaults.password'),
                    ];
                @endphp
            @endproduction

            <!-- Name -->
            <x-dashboard.widgets.forms.input
                label="Name"
                name="name"
                id="userName"
                value="{{ $__defaults['name'] }}"
                placeholder="Enter full name"
                required
            />

            <!-- Email -->
            <x-dashboard.widgets.forms.input
                label="Email"
                type="email"
                name="email"
                id="userEmail"
                value="{{ $__defaults['email'] }}"
                placeholder="Enter email address"
                required
            />

            <!-- Phone number -->
            <x-dashboard.widgets.forms.input
                label="Phone Number"
                type="tel"
                name="phone"
                id="userPhone"
                value="{{ $__defaults['phone'] }}"
                placeholder="Enter phone number"
            />

            <!-- User role -->
            <x-dashboard.widgets.forms.select
                label="Select Role"
                name="role"
                id="userRole"
                value="{{ old('role') }}"
            >
                <option value="user" selected>Normal User</option>
                <option value="admin">Administrator</option>
            </x-dashboard.widgets.forms.select>

            <!-- Password -->
            <x-dashboard.widgets.forms.input
                label="Password"
                type="password"
                name="password"
                id="userPassword"
                value="{{ $__defaults['password'] }}"
                widgets="password-visibility"
                placeholder="Enter a strong password"
            />
        </x-dashboard.widgets.modals.modal>

        <x-dashboard.widgets.modals.modal
            title="Edit User"
            method="PATCH"
            :url="route('dashboard.users.update', ':id')"
            action="Save"
            id="edit"
        >
            <!-- Name -->
            <x-dashboard.widgets.forms.input
                label="Name"
                name="name"
                id="editUserName"
                placeholder="Enter full name"
                required
            />

            <!-- Email -->
            <x-dashboard.widgets.forms.input
                label="Email"
                type="email"
                name="email"
                id="editUserEmail"
                placeholder="Enter email address"
                required
            />

            <!-- Phone number -->
            <x-dashboard.widgets.forms.input
                label="Phone Number"
                type="tel"
                name="phone"
                id="editUserPhone"
                placeholder="Enter phone number"
            />

            <!-- User role -->
            <x-dashboard.widgets.forms.select
                label="Select Role"
                name="role"
                id="editUserRole"
            >
                <option value="user">Normal User</option>
                <option value="admin">Administrator</option>
            </x-dashboard.widgets.forms.select>

            <!-- Password -->
            <x-dashboard.widgets.forms.input
                label="Password"
                type="password"
                name="password"
                widgets="password-visibility"
                id="editUserPassword"
                placeholder="Enter a strong password"
            />
        </x-dashboard.widgets.modals.modal>

        <x-dashboard.widgets.modals.modal
            title="Delete User"
            method="DELETE"
            :url="route('dashboard.users.delete', ':id')"
            action="Delete"
            id="delete"
            noHeader
        >
            <h5 class="text-center col-12 my-5">
                Are you sure want to delete this user?
            </h5>
        </x-dashboard.widgets.modals.modal>
    </x-slot>

    <x-slot name="scripts">
        <script type="text/javascript">
            function populateFields(that) {
                // Fetch resource data and parse the JSON string
                const user = $(that).parent().parent().data('resource')

                // Extract necessary fields from parsed data
                const {id, name, email, phone, role} = user

                // Update form URLs accordingly
                const editUrl = $('#editResourceModalForm').attr('action');
                $('#editResourceModalForm').attr('action', editUrl.replace(':id', id));
                const deleteUrl = $('#deleteResourceModalForm').attr('action');
                $('#deleteResourceModalForm').attr('action', deleteUrl.replace(':id', id));

                // Populate update input fields with data
                $('#editUserNameInputField').val(name);
                $('#editUserEmailInputField').val(email);
                $('#editUserPhoneInputField').val(phone);
                $('#editUserRoleSelectField').val(role);
            }
        </script>
    </x-slot>
</x-dashboard>
