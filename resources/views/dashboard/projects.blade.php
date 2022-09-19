<x-dashboard title="Projects" pageTitle="Projects">
    <x-slot name="header">
        <x-dashboard.widgets.modals.button
            class="btn-secondary mr-3"
            label="Filter"
            for="filter"
            icon="filter-alt"
        />

        <x-dashboard.widgets.modals.button
            label="Add Project"
        />
    </x-slot>

    <table class="nk-tb-list is-separate nk-tb-ulist">
        <thead>
            <tr class="nk-tb-item nk-tb-head">
                <!-- Name -->
                <th class="nk-tb-col">
                    <span class="sub-text">Name</span>
                </th>

                <!-- Lead -->
                <th class="nk-tb-col">
                    <span class="sub-text">Lead</span>
                </th>

                <!-- Budget -->
                <th class="nk-tb-col">
                    <span class="sub-text">Contract Fee</span>
                </th>

                <!-- Invoices -->
                <th class="nk-tb-col">
                    <span class="sub-text">Paid Invoices</span>
                </th>

                <!-- Progress -->
                <th class="nk-tb-col">
                    <span class="sub-text">Progress</span>
                </th>

                <!-- Payment status -->
                <th class="nk-tb-col">
                    <span class="sub-text">Payment Status</span>
                </th>


                <!-- Actions -->
                <th class="nk-tb-col nk-tb-col-tools text-right"></th>
            </tr>
        </thead>
        <tbody>  
            @if ($projects != null)
            @forelse ($projects as $project)
                <tr class="nk-tb-item {{ $loop->even ? 'even' : 'odd' }}">
                    <!-- Avatar + Name -->
                    <td class="nk-tb-col">
                        <div class="user-card">
                            <div class="project-title">
                                <div class="user-avatar sq bg-purple">
                                    <span>
                                        {{ App\Helpers\Initials::generate($project->name) }}
                                    </span>
                                </div>
                                <div class="project-info">
                                    <h6 class="title">
                                        {{ $project->name }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Lead -->
                    <td class="nk-tb-col tb-col-lg">
                        <span>{{ $project->leader->name }}</span>
                    </td>

                    <!-- Budget -->
                    <td class="nk-tb-col tb-col-lg">
                        <strong>{{ number_format($project->budget) }}</strong>
                    </td>

                    <!-- Paid Invoices -->
                    <td class="nk-tb-col tb-col-mb text-center">
                        <strong>{{ number_format($project->paidInvoices) }}</strong>
                    </td>

                    <!-- Progress -->
                    @php
                        if ($newDatabase->document($project->id_project_firebase)->snapshot()) {
                            $id_project_firebase = $newDatabase->document($project->id_project_firebase)->snapshot();
                        
                            $progress_firestore = $id_project_firebase->data()['progress'] ?? 0;
                            $commissioning_firestore = $id_project_firebase->data()['commissioning'] ?? 0;
                            $concept_firestore = $id_project_firebase->data()['concept'] ?? 0;
                            $development_firestore = $id_project_firebase->data()['development'] ?? 0;
                            $documentation_firestore = $id_project_firebase->data()['documentation'] ?? 0;
                            $workload_firestore = $id_project_firebase->data()['workload'] ?? 0;
                            $status_firestore = $id_project_firebase->data()['status'] ?? 0;

                            // update progress
                            // if ($project->progress != $progress_firestore) {
                                $project->update(["progress" => $progress_firestore]);
                            // }
                            // update commissioning
                            // if ($project->commissioning != $commissioning_firestore) {
                                $project->update(["commissioning" => $commissioning_firestore]);
                            // }
                             // update concept
                            // if ($project->concept != $concept_firestore) {
                                $project->update(["concept" => $concept_firestore]);
                            // }
                             // update development
                            // if ($project->development != $development_firestore) {
                                $project->update(["development" => $development_firestore]);
                            // }
                             // update documentation
                            // if ($project->documentation != $documentation_firestore) {
                                $project->update(["documentation" => $documentation_firestore]);
                            // }
                             // update workload
                            // if ($project->workload != $workload_firestore) {
                                $project->update(["workload" => $workload_firestore]);
                            // }
                            //  update status
                            // if ($project->status != $status_firestore) {
                            //     $project->update(["status" => $status_firestore]);
                            // }
                        }
                        

                        
                    @endphp
                    <td class="nk-tb-col tb-col-md">
                        <div class="project-list-progress">
                            <div class="progress progress-pill progress-md bg-light">
                                <div
                                    class="progress-bar"
                                    data-progress="{{ $project->progress }}"
                                    style="width: {{ $project->progress }}%;"
                                    {{-- data-progress="{{ $progress_firestore }}"
                                    style="width: {{ $progress_firestore }}%;" --}}
                                ></div>
                            </div>
                            {{-- <div class="project-progress-percent">{{ $project->progress }}%</div> --}}
                            
                            <div class="project-progress-percent">{!! $progress_firestore !!}%</div>
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="nk-tb-col tb-col-mb">
                        <span class="badge badge-dim badge-warning">
                            <em class="icon ni ni-clock"></em>
                            <span>
                                {{ config('lumina.project.status')[$project->status] }}
                            </span>
                        </span>
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
                                    data-resource="{{ $project->toJson() }}"
                                >
                                    <!-- Details -->
                                    <li>
                                        <a
                                            href="#"
                                            data-toggle="modal"
                                            data-target="#previewResourceModal"
                                            onclick="populateFields(this)"
                                        >
                                            <em class="icon ni ni-eye"></em>
                                            <span>View Details</span>
                                        </a>
                                    </li>

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

    @if ($projects != null)
    <div class="d-flex justify-content-end mt-2">
        {{ $projects->links() }}
    </div>
    @endif

    <x-slot name="modals">
        <!-- Preivew resource details modal -->
        <x-dashboard.widgets.modals.modalviews
            title="Project Details"
            id="preview"
        >
            <!-- Project Name -->
            <div class="form-group col-md-6">
                <span class="form-label">Name</span>
                <div class="form-control-wrap">
                    <span id="previewProjectNameField"></span>
                </div>
            </div>

            <!-- Project Leader -->
            <div class="form-group col-md-6">
                <span class="form-label">Leader</span>
                <div class="form-control-wrap">
                    <span id="previewProjectLeaderField"></span>
                </div>
            </div>

            <!-- Project Progress -->
            <div class="form-group col-md-6">
                <span class="form-label">Progress</span>
                <div class="form-control-wrap">
                    <span id="previewProjectProgressField"></span>
                </div>
            </div>

            <!-- Project Budget -->
            <div class="form-group col-md-6">
                <span class="form-label">Contract Fee</span>
                <div class="form-control-wrap">
                    <span id="previewProjectBudgetField"></span>
                </div>
            </div>

            <!-- Project Status -->
            <div class="form-group col-md-6">
                <span class="form-label">Status</span>
                <div class="form-control-wrap">
                    <span id="previewProjectStatusField"></span>
                </div>
            </div>

            <!-- Project Workload -->
            <div class="form-group col-md-6">
                <span class="form-label">Workload</span>
                <div class="form-control-wrap">
                    <span id="previewProjectWorkloadField"></span>
                </div>
            </div>

            <!-- Project Concept -->
            <div class="form-group col-md-6">
                <span class="form-label">Concept</span>
                <div class="form-control-wrap">
                    <span id="previewProjectConceptField"></span>
                </div>
            </div>

            <!-- Project Development -->
            <div class="form-group col-md-6">
                <span class="form-label">Development</span>
                <div class="form-control-wrap">
                    <span id="previewProjectDevelopmentField"></span>
                </div>
            </div>

            <!-- Project Documentation -->
            <div class="form-group col-md-6">
                <span class="form-label">Documentation</span>
                <div class="form-control-wrap">
                    <span id="previewProjectDocumentationField"></span>
                </div>
            </div>

            <!-- Project Commissioning -->
            <div class="form-group col-md-6">
                <span class="form-label">Commissioning</span>
                <div class="form-control-wrap">
                    <span id="previewProjectCommissioningField"></span>
                </div>
            </div>

            <!-- Project Phase -->
            <div class="form-group col-md-6">
                <span class="form-label">Phase</span>
                <div class="form-control-wrap">
                    <span id="previewProjectPhaseField"></span>
                </div>
            </div>

            <!-- Project BAs -->
            <div class="form-group col-md-6">
                <span class="form-label">BAs</span>
                <div class="form-control-wrap">
                    <span id="previewProjectBAsField"></span>
                </div>
            </div>

            <!-- Project DAs -->
            <div class="form-group col-md-6">
                <span class="form-label">DAs</span>
                <div class="form-control-wrap">
                    <span id="previewProjectDAsField"></span>
                </div>
            </div>

            <!-- Project LDs -->
            <div class="form-group col-md-6">
                <span class="form-label">LDs</span>
                <div class="form-control-wrap">
                    <span id="previewProjectLDsField"></span>
                </div>
            </div>
        </x-dashboard.widgets.modals.modalviews>

        <!-- Create resource modal -->
        <x-dashboard.widgets.modals.modal
            title="Add Project"
            :url="route('dashboard.projects.create')"
        >
            {{-- Generate fake data if not in production --}}
            @production
                @php
                    $__defaults = [
                        'name'     => old('name'),
                        'budget'   => old('budget'),
                        'phase'    => old('phase'),
                        'status'   => old('status'),
                    ];
                @endphp
            @else
                @php
                    $faker = Faker\Factory::create();

                    $__defaults = [
                        'name'     => ucfirst($faker->words(random_int(2, 4), true)),
                        'budget'   => random_int(1000, 10000),
                        'phase'    => ucfirst($faker->sentences(random_int(1, 3), true)),
                        'status'   => $faker->randomElement(array_keys(config('lumina.project.status'))),
                    ];
                @endphp
            @endproduction

            <!-- Project Name -->
            <x-dashboard.widgets.forms.input
                label="Name"
                name="name"
                id="projectName"
                wrapperClass="col-md-6"
                value="{{ $__defaults['name'] }}"
                placeholder="Enter project name"
                required
            />

            <!-- Project Leader -->
            <x-dashboard.widgets.forms.select
                label="Leader"
                name="leader"
                wrapperClass="col-md-6"
                id="projectLeader"
                required
            >
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </x-dashboard.widgets.forms.select>

            <!-- Contract Fee (Budget) -->
            <x-dashboard.widgets.forms.input
                label="Contract Fee"
                type="number"
                name="budget"
                wrapperClass="col-md-6"
                id="projectBudget"
                value="{{ $__defaults['budget'] }}"
                placeholder="Enter contract fee"
                required
            />

            <!-- BA Users -->
            <x-dashboard.widgets.forms.select
                label="BAs"
                name="bas[]"
                multiple="multiple"
                wrapperClass="col-md-6"
                multiple="multiple"
                id="projectBA"
            >
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </x-dashboard.widgets.forms.select>

            <!-- LD Users -->
            <x-dashboard.widgets.forms.select
                label="LDs"
                name="lds[]"
                multiple="multiple"
                wrapperClass="col-md-6"
                id="projectLD"
            >
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </x-dashboard.widgets.forms.select>

            <!-- DA Users -->
            <x-dashboard.widgets.forms.select
                label="DAs"
                name="das[]"
                wrapperClass="col-md-6"
                multiple="multiple"
                id="projectDA"
            >
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </x-dashboard.widgets.forms.select>

            <!-- Phase -->
            <x-dashboard.widgets.forms.input
                label="Project Phase"
                type="textarea"
                name="phase"
                wrapperClass="col-md-6"
                id="projectPhase"
                placeholder="Enter project phase"
                required
            >{{ $__defaults['phase'] }}</x-dashboard.widgets.forms.input>

            <!-- Status -->
            <x-dashboard.widgets.forms.select
                label="Project Status"
                name="status"
                wrapperClass="col-md-6"
                id="projectStatus"
                value="{{ $__defaults['status'] }}"
                required
            >
                @foreach (config('lumina.project.status') as $status => $label)
                    <option value="{{ $status }}">{{ $label }}</option>
                @endforeach
            </x-dashboard.widgets.forms.select>
        </x-dashboard.widgets.modals.modal>

        <!-- Update resource modal -->
        <x-dashboard.widgets.modals.modal
            title="Update Project"
            :url="route('dashboard.projects.update', ':id')"
            method="PUT"
            action="Save"
            id="edit"
        >
            <div class="text-sm text-muted col-12 mb-2">
                Last Updated: <span id="projectLastUpdatedDate"></span>
            </div>
            <!-- Project Name -->
            <x-dashboard.widgets.forms.input
                label="Name"
                name="name"
                id="editProjectName"
                wrapperClass="col-md-6"
                placeholder="Enter project name"
                required
            />

            <!-- Project Leader -->
            <x-dashboard.widgets.forms.select
                label="Leader"
                name="leader"
                wrapperClass="col-md-6"
                id="editProjectLeader"
                required
            >
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </x-dashboard.widgets.forms.select>

            <!-- Contract Fee (Budget) -->
            <x-dashboard.widgets.forms.input
                label="Contract Fee"
                type="number"
                name="budget"
                wrapperClass="col-md-6"
                id="editProjectBudget"
                placeholder="Enter contract fee"
                required
            />

            <!-- BA Users -->
            <x-dashboard.widgets.forms.select
                label="BAs"
                name="bas[]"
                multiple="multiple"
                wrapperClass="col-md-6"
                multiple="multiple"
                id="editProjectBA"
            >
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </x-dashboard.widgets.forms.select>

            <!-- LD Users -->
            <x-dashboard.widgets.forms.select
                label="LDs"
                name="lds[]"
                multiple="multiple"
                wrapperClass="col-md-6"
                id="editProjectLD"
            >
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </x-dashboard.widgets.forms.select>

            <!-- DA Users -->
            <x-dashboard.widgets.forms.select
                label="DAs"
                name="das[]"
                wrapperClass="col-md-6"
                multiple="multiple"
                id="editProjectDA"
            >
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </x-dashboard.widgets.forms.select>

            <!-- Phase -->
            <x-dashboard.widgets.forms.input
                label="Project Phase"
                type="textarea"
                name="phase"
                wrapperClass="col-md-6"
                id="editProjectPhase"
                placeholder="Enter project phase"
                required
            />

            <!-- Status -->
            <x-dashboard.widgets.forms.select
                label="Project Status"
                name="status"
                wrapperClass="col-md-6"
                id="editProjectStatus"
                required
            >
                @foreach (config('lumina.project.status') as $status => $label)
                    <option value="{{ $status }}">{{ $label }}</option>
                @endforeach
            </x-dashboard.widgets.forms.select>
        </x-dashboard.widgets.modals.modal>

        <!-- Delete resource modal -->
        <x-dashboard.widgets.modals.modal
            title="Delete User"
            method="DELETE"
            :url="route('dashboard.projects.delete', ':id')"
            action="Delete"
            id="delete"
            noHeader
        >
            <h5 class="text-center col-12 my-5">
                Are you sure want to delete this project?
            </h5>
        </x-dashboard.widgets.modals.modal>

        {{-- filter --}}
        <x-dashboard.widgets.modals.modal
            title="Filter project"
            formMethod="GET"
            :url="route('dashboard.projects')"
            action="Filter"
            id="filter"
        >

            <!-- Status -->
            <x-dashboard.widgets.forms.select
                label="Payment status"
                wrapperClass="col-md-12"
                name="status"
                id="filterStatus"
                value="{{ old('status') }}"
            >
                <option selected value="">Select Status</option>
                <option value="invoiced">Invoiced</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="start">Start</option>
                <option value="finished">Finished</option>
            </x-dashboard.widgets.forms.select>

            <!-- Progress -->
            <x-dashboard.widgets.forms.input
            label="Min Progress"
            name="minProgress"
            type="number"
            wrapperClass="col-md-6"
            id="filterProgress"
            value="{{ old('minProgress') }}"
            {{-- placeholder="Enter progress" --}}
            />

            <x-dashboard.widgets.forms.input
            label="Max Progress"
            name="maxProgress"
            type="number"
            wrapperClass="col-md-6"
            id="filterProgress"
            value="{{ old('maxProgress') }}"
            {{-- placeholder="Enter progress" --}}
            />

            <!-- Paind Invoices -->
            <x-dashboard.widgets.forms.input
            label="Min Paid Invoice"
            name="minPaidInvoices"
            type="number"
            wrapperClass="col-md-6"
            id="filterInvoices"
            value="{{ old('minPaidInvoices') }}"
            {{-- placeholder="Enter progress" --}}
            />
            
            <!-- Progress -->
            <x-dashboard.widgets.forms.input
            label="Max Paid Invoice "
            name="maxPaidInvoices"
            type="number"
            wrapperClass="col-md-6"
            id="filterInvoices"
            value="{{ old('maxPaidInvoices') }}"
            {{-- placeholder="Enter progress" --}}
            />

        </x-dashboard.widgets.modals.modal>
        {{-- filter --}}

    </x-slot>

    <x-slot name="scripts">
        <script
            type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"
        ></script>
        <script type="text/javascript">
            function populateFields(that) {
                // Fetch resource data and parse the JSON string
                const project = $(that).parent().parent().data('resource')

                // Extract necessary fields from parsed data
                const {id, name, progress, budget, status, phase, workload} = project
                const {concept, development, documentation, commissioning} = project
                const {leader, participants, updated_at} = project

                // Update form URLs accordingly
                const editUrl = $('#editResourceModalForm').attr('action');
                $('#editResourceModalForm').attr('action', editUrl.replace(':id', id));
                const deleteUrl = $('#deleteResourceModalForm').attr('action');
                $('#deleteResourceModalForm').attr('action', deleteUrl.replace(':id', id));

                const lastUpdated = moment.unix(updated_at).fromNow()
                $('#projectLastUpdatedDate').text(lastUpdated);

                const bas = [];
                const das = [];
                const lds = [];

                participants.forEach((member) => {
                    const {id, pivot: {role}} = member

                    if (role === 'ba') bas.push(member)
                    else if (role === 'da') das.push(member)
                    else if (role === 'ld') lds.push(member)
                })

                const addCommas = (x) => x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                const handleMultiSelect = (id, members) => {
                    const elem = document.getElementById(id)
                    const values = members.map(({id}) => id.toString());

                    if (! elem || ! elem.options) return;

                    for (let i = 0; i < elem.options.length; i++)
                        elem.options[i].selected = values.indexOf(elem.options[i].value) > -1;
                }

                const populateMembersMakrup = (container, members) => {
                    const elem = $(`#${container}`);

                    if (! elem) return;

                    console.log({elem, container, members});

                    const items = [];

                    members.forEach(({name}) => {
                        const item = $('<div></div>').html(name);
                        items.push(item);
                    });

                    elem.html(items);
                    
                    //Label
                    // const label = elem.parent().parent().find('.form-label');
                    //       label.text(`${label.text()} (${members.length})`);
                }

                // Populate update input fields with data
                $('#editProjectNameInputField').val(name);
                $('#editProjectBudgetInputField').val(budget);
                $('#editProjectPhaseTextareaField').val(phase);

                $('#editProjectLeaderSelectField').val(leader.id);
                $('#editProjectStatusSelectField').val(status);

                handleMultiSelect('editProjectBASelectField', bas);
                handleMultiSelect('editProjectLDSelectField', lds);
                handleMultiSelect('editProjectDASelectField', das);

                const all_status = {!! json_encode(config('lumina.project.status')) !!};

                // Populate preview fields
                $('#previewProjectNameField').text(name);
                $('#previewProjectProgressField').text(`${progress || 0}%`);
                $('#previewProjectBudgetField').text(addCommas(budget));
                $('#previewProjectPhaseField').text(phase);
                $('#previewProjectStatusField').text(all_status[status]);
                $('#previewProjectWorkloadField').text(`${workload || 0}%`);
                $('#previewProjectConceptField').text(`${concept || 0}%`);
                $('#previewProjectDevelopmentField').text(`${development || 0}%`);
                $('#previewProjectDocumentationField').text(`${documentation || 0}%`);
                $('#previewProjectCommissioningField').text(`${commissioning || 0}%`);

                // Populate members fiels
                $('#previewProjectLeaderField').text(leader.name);
                populateMembersMakrup('previewProjectBAsField', bas)
                populateMembersMakrup('previewProjectDAsField', das)
                populateMembersMakrup('previewProjectLDsField', lds)
            }
        </script>
    </x-slot>
</x-dashboard>
