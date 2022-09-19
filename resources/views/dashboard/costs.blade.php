<x-dashboard title="Cost & Balance" pageTitle="Cost & Balance">
    <x-slot name="header">
        <x-dashboard.widgets.modals.button
            class="btn-secondary mr-3"
            label="Filter"
            for="filter"
            icon="filter-alt"
        />
        
        <x-dashboard.widgets.modals.button
            label="Add Cost"
        />
    </x-slot>

    <table class="nk-tb-list is-separate nk-tb-ulist">
        <thead>
            <tr class="nk-tb-item nk-tb-head">
                <!-- Billing Month -->
                <th class="nk-tb-col">
                    <span class="sub-text">Billing Month</span>
                </th>

                <!-- Paid Invoices -->
                <th class="nk-tb-col">
                    <span class="sub-text">Paid Invoices</span>
                </th>

                <!-- Cost -->
                <th class="nk-tb-col">
                    <span class="sub-text">Total Cost</span>
                </th>

                <!-- Balance -->
                <th class="nk-tb-col">
                    <span class="sub-text">End of Month Balance</span>
                </th>

                <!-- Actions -->
                <th class="nk-tb-col nk-tb-col-tools text-right"></th>
            </tr>
        </thead>
        <tbody>
            @if ($costs != null)
            @foreach ($costs as $cost)
                <tr class="nk-tb-item {{ $loop->even ? 'even' : 'odd' }}">
                    <!-- Billing Month -->
                    <td class="nk-tb-col tb-col-lg">
                        <span>{{ $cost->prettyMonth }}</span>
                    </td>

                    <!-- Paid Invoices -->
                    <td class="nk-tb-col tb-col-lg">
                        <span>{{ number_format($cost->inv) }}</span>
                    </td>

                    <!-- Cost -->
                    <td class="nk-tb-col tb-col-md">
                        <span>{{ number_format($cost->amount) }}</span>
                    </td>

                    <!-- Balance -->
                    <td class="nk-tb-col tb-col-md">
                        <span>{{ number_format($cost->balance) }}</span>
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
                                    data-resource="{{ $cost->toJson() }}"
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
            @else
            
            @endif
        </tbody>
    </table>
    @if ($costs != null)
    <div class="d-flex justify-content-end mt-2">
        {{ $costs->links() }}
    </div>
    @endif
    <x-slot name="modals">
        <x-dashboard.widgets.modals.modal
            title="Add Cost & Balance"
            :url="route('dashboard.costs.create')"
        >
            {{-- Generate fake data if not in production --}}
            @production
                @php
                    $__defaults = [
                        'billing_month' => old('billing_month'),
                        'amount'        => old('amount'),
                        'balance'       => old('balance'),
                    ];
                @endphp
            @else
                @php
                    $faker = Faker\Factory::create();

                    $__defaults = [
                        'billing_month' => date('Y-m-d'),
                        'amount'        => random_int(1000, 10000),
                        'balance'       => random_int(1000, 10000),
                    ];
                @endphp
            @endproduction

            <!-- Billing Month -->
            <x-dashboard.widgets.forms.input
                label="Billing Month"
                type="text"
                name="billing_month"
                class="date-picker"
                data-date-format="yyyy-mm-dd"
                id="expenseBillingMonth"
                value="{{ $__defaults['billing_month'] }}"
                placeholder="Select billing month"
                required
            />

            <!-- Amount (Cost) -->
            <x-dashboard.widgets.forms.input
                label="Total Cost"
                type="number"
                name="amount"
                id="expenseAmount"
                value="{{ $__defaults['amount'] }}"
                placeholder="Enter total cost"
                required
            />

            <!-- Balance -->
            <x-dashboard.widgets.forms.input
                label="End of Month Balance"
                type="number"
                name="balance"
                id="expenseBalance"
                value="{{ $__defaults['balance'] }}"
                placeholder="Enter end of month balance"
                required
            />
        </x-dashboard.widgets.modals.modal>

        <x-dashboard.widgets.modals.modal
            title="Edit Cost & Balance"
            method="PATCH"
            :url="route('dashboard.costs.update', ':id')"
            action="Save"
            id="edit"
        >
            <!-- Amount (Cost) -->
            <x-dashboard.widgets.forms.input
                label="Total Cost"
                type="number"
                name="amount"
                id="editExpenseAmount"
                placeholder="Enter total cost"
                required
            />

            <!-- Balance -->
            <x-dashboard.widgets.forms.input
                label="End of Month Balance"
                type="number"
                name="balance"
                id="editExpenseBalance"
                placeholder="Enter end of month balance"
                required
            />
        </x-dashboard.widgets.modals.modal>

        <x-dashboard.widgets.modals.modal
            title="Delete Cost & Balance"
            method="DELETE"
            :url="route('dashboard.costs.delete', ':id')"
            action="Delete"
            id="delete"
            noHeader
        >
            <h5 class="text-center col-12 my-5">
                Are you sure want to delete this expense?
            </h5>
        </x-dashboard.widgets.modals.modal>

<!-- Filter -->
        <x-dashboard.widgets.modals.modal
            title="Filter cost & balance"
            formMethod="GET"
            :url="route('dashboard.costs')"
            action="Filter"
            id="filter"
        >
            <!-- Project ID -->
            {{-- <x-dashboard.widgets.forms.select
                label="Project"
                name="project_id"
                id="filterProjectId"
                value="{{ old('project_id') }}"
            >
                <option selected value="">Select Project</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">
                        {{ $project->name }}
                    </option>
                @endforeach
            </x-dashboard.widgets.forms.select> --}}


            <!-- Month -->
            @php
                $__months = [
                    'January', 'Feburary', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November',
                    'December'
                ]
            @endphp
            <x-dashboard.widgets.forms.select
                label="Month"
                name="month"
                id="filterMonth"
                wrapperClass="col-md-6"
                value="{{ old('month') }}"
            >
                <option selected value="">Select Month</option>
                @foreach ($__months as $month)
                    <option value="{{ $loop->index + 1 }}">{{ $month }}</option>
                @endforeach
            </x-dashboard.widgets.forms.select>

            <!-- Year -->
            <x-dashboard.widgets.forms.select
                label="Year"
                name="year"
                id="filterYear"
                wrapperClass="col-md-6"
                value="{{ old('year') }}"
            >
                <option selected value="">Select Year</option>
                @for ($i = 0; $i <= $years; $i++)
                    @php $__year = (int) date('Y') - $i @endphp
                    <option value="{{ $__year }}">{{ $__year }}</option>
                @endfor
            </x-dashboard.widgets.forms.select>
        </x-dashboard.widgets.modals.modal>
<!-- Filter -->
    </x-slot>

    <x-slot name="scripts">
        <script type="text/javascript">
            function populateFields(that) {
                // Fetch resource data and parse the JSON string
                const expense = $(that).parent().parent().data('resource')

                // Extract necessary fields from parsed data
                const {id, amount, balance} = expense

                // Update form URLs accordingly
                const editUrl = $('#editResourceModalForm').attr('action');
                $('#editResourceModalForm').attr('action', editUrl.replace(':id', id));
                const deleteUrl = $('#deleteResourceModalForm').attr('action');
                $('#deleteResourceModalForm').attr('action', deleteUrl.replace(':id', id));

                // Populate update input fields with data
                $('#editExpenseAmountInputField').val(amount);
                $('#editExpenseBalanceInputField').val(balance);
            }
        </script>
    </x-slot>
</x-dashboard>
