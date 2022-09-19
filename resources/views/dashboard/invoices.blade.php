<x-dashboard title="Invoices" pageTitle="Invoices">
    <x-slot name="header">
        <x-dashboard.widgets.modals.button
            class="btn-secondary mr-3"
            label="Filter"
            for="filter"
            icon="filter-alt"
        />
        <x-dashboard.widgets.modals.button
            label="Add Invoice"
        />
    </x-slot>

    <table class="nk-tb-list is-separate nk-tb-ulist">
        <thead>
            <tr class="nk-tb-item nk-tb-head">
                <!-- Project -->
                <th class="nk-tb-col">
                    <span class="sub-text">Project</span>
                </th>

                <!-- Paid amount -->
                <th class="nk-tb-col">
                    <span class="sub-text">Amount</span>
                </th>

                <!-- Payment date -->
                <th class="nk-tb-col">
                    <span class="sub-text">Payment Date</span>
                </th>

                <!-- Actions -->
                <th class="nk-tb-col nk-tb-col-tools text-right"></th>
            </tr>
        </thead>
        <tbody>
            @if ($invoices != null)
            @foreach ($invoices as $invoice)
            <tr class="nk-tb-item {{ $loop->even ? 'even' : 'odd' }}">
                <!-- Project (avatar + name) -->
                <td class="nk-tb-col">
                    <div class="user-card">
                        <div class="project-title">
                            @if ($invoice->project)
                                <div class="user-avatar sq bg-purple">
                                    <span>
                                        {{ App\Helpers\Initials::generate($invoice->project->name) }}
                                    </span>
                                </div>
                            @else
                                <div class="user-avatar sq bg-secondary">
                                    <span></span>
                                </div>
                            @endif
                            <div class="project-info">
                                <h6 class="title">
                                    {{ $invoice->project?->name ?? 'Project Deleted' }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </td>

                <!-- Paid amount -->
                <td class="nk-tb-col tb-col-lg">
                    <span>{{ number_format($invoice->amount) }}</span>
                </td>

                <!-- Paid On -->
                <td class="nk-tb-col tb-col-md">
                    <span>{{ $invoice->prettyPaymentDate }}</span>
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
                                data-resource="{{ $invoice->toJson() }}"
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
    @if ($invoices != null)
    <div class="d-flex justify-content-end mt-2">
        {{ $invoices->links() }}
    </div>
    @endif
    

    

    <x-slot name="modals">
        <x-dashboard.widgets.modals.modal
            title="Add Invoice"
            :url="route('dashboard.invoices.create')"
        >
            {{-- Generate fake data if not in production --}}
            @production
                @php
                    $__defaults = [
                        'paid_on'    => old('paid_on'),
                        'amount'     => old('amount'),
                    ];
                @endphp
            @else
                @php
                    $faker = Faker\Factory::create();

                    $__defaults = [
                        'paid_on'    => date('Y-m-d'),
                        'amount'     => random_int(1000, 10000),
                    ];
                @endphp
            @endproduction

            <!-- Project ID -->
            <x-dashboard.widgets.forms.select
                label="Project"
                name="project_id"
                id="invoiceProjectId"
                required
            >
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">
                        {{ $project->name }}
                    </option>
                @endforeach
            </x-dashboard.widgets.forms.select>

            <!-- Payment Date -->
            <x-dashboard.widgets.forms.input
                label="Payment Date"
                type="text"
                name="paid_on"
                class="date-picker"
                data-date-format="yyyy-mm-dd"
                id="invoicePaymentDate"
                value="{{ $__defaults['paid_on'] }}"
                placeholder="Select payment date"
                required
            />

            <!-- Amount -->
            <x-dashboard.widgets.forms.input
                label="Paid Amount"
                type="number"
                name="amount"
                id="invoiceAmount"
                value="{{ $__defaults['amount'] }}"
                placeholder="Enter paid amount"
                required
            />
        </x-dashboard.widgets.modals.modal>

        <x-dashboard.widgets.modals.modal
            title="Edit Invoice"
            method="PATCH"
            :url="route('dashboard.invoices.update', ':id')"
            action="Save"
            id="edit"
        >
            <!-- Payment Date -->
            <x-dashboard.widgets.forms.input
                label="Payment Date"
                type="text"
                name="paid_on"
                class="date-picker"
                data-date-format="yyyy-mm-dd"
                id="editInvoicePaymentDate"
                placeholder="Select payment date"
                required
            />

            <!-- Amount -->
            <x-dashboard.widgets.forms.input
                label="Paid Amount"
                type="number"
                name="amount"
                id="editInvoiceAmount"
                placeholder="Enter paid amount"
                required
            />
        </x-dashboard.widgets.modals.modal>

        <x-dashboard.widgets.modals.modal
            title="Delete Invoice"
            method="DELETE"
            :url="route('dashboard.invoices.delete', ':id')"
            action="Delete"
            id="delete"
            noHeader
        >
            <h5 class="text-center col-12 my-5">
                Are you sure want to delete this invoice?
            </h5>
        </x-dashboard.widgets.modals.modal>

        <x-dashboard.widgets.modals.modal
            title="Filter invoices"
            formMethod="GET"
            :url="route('dashboard.invoices')"
            action="Filter"
            id="filter"
        >
            <!-- Project ID -->
            <x-dashboard.widgets.forms.select
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
            </x-dashboard.widgets.forms.select>


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
    </x-slot>

    <x-slot name="scripts">
        <script type="text/javascript">
            function populateFields(that) {
                // Fetch resource data and parse the JSON string
                const invoice = $(that).parent().parent().data('resource')

                // Extract necessary fields from parsed data
                const {id, paid_on, amount} = invoice

                // Update form URLs accordingly
                const editUrl = $('#editResourceModalForm').attr('action');
                $('#editResourceModalForm').attr('action', editUrl.replace(':id', id));
                const deleteUrl = $('#deleteResourceModalForm').attr('action');
                $('#deleteResourceModalForm').attr('action', deleteUrl.replace(':id', id));

                // Populate update input fields with data
                $('#editInvoiceAmountInputField').val(amount);
                $('#editInvoicePaymentDateInputField').val(paid_on);
            }
        </script>
    </x-slot>
</x-dashboard>
