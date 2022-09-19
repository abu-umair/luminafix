@php
    $__months = [
        1  => 'January',
        2  => 'Feburary',
        3  => 'March',
        4  => 'April',
        5  => 'May',
        6  => 'June',
        7  => 'July',
        8  => 'August',
        9  => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    ];
    if (isset($current))
        $__title = sprintf('Fee Index (%s %s)', $__months[$current->month], $current->year);
    else
        $__title = sprintf(
            'Fee Index (%s %s)',
            $__months[request('month') ?: (int) date('m')],
            request('year') ?: (int) date('Y')
        );
@endphp
<x-dashboard :title="$__title" :pageTitle="$__title">
    <x-slot name="header">
        <x-dashboard.widgets.modals.button
            class="btn-secondary"
            label="Filter"
            for="filter"
            icon="filter-alt"
        />
    </x-slot>

    @if(CStr::isValidString($error))
        <div
            class="alert alert-danger alert-dismissible mb-4"
            role="alert"
            style="
                color:#721c24;
                background-color: #f8d7da;
                margin-bottom: 0px;
                border-color: #f5c6cb;
            "
        >
            <a
                href="#"
                class="close"
                data-dismiss="alert"
                aria-label="close"
                style="text-decoration: none;"
            ></a>
            {{ $error }}
        </div>
    @endif

    @if (isset($previous_months) && isset($current) && isset($calculations))
        <table class="nk-tb-list is-separate nk-tb-ulist">
            @php $previous = $previous_months @endphp
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <!-- Empty -->
                    <th class="nk-tb-col text-center"></th>

                    <!-- MONTH - 3 -->
                    <th class="nk-tb-col text-center">
                        <span class="sub-text">{{ $__months[$previous_months->get(0)->month] }}</span>
                    </th>

                    <!-- MONTH - 2 -->
                    <th class="nk-tb-col text-center">
                        <span class="sub-text">{{ $__months[$previous_months->get(1)->month] }}</span>
                    </th>

                    <!-- MONTH - 1 -->
                    <th class="nk-tb-col text-center">
                        <span class="sub-text">{{ $__months[$previous_months->get(2)->month] }}</span>
                    </th>

                    <!-- Current Month -->
                    <th class="nk-tb-col text-center">
                        <span class="sub-text">{{ $__months[$current->month] }}</span>
                    </th>

                    <!-- Average -->
                    <th class="nk-tb-col text-center">
                        <span class="sub-text">Average</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <!-- INV -->
                <tr class="nk-tb-item">
                    <!-- Label -->
                    <th class="nk-tb-col">INV</th>

                    <!-- MONTH - 3 -->
                    <td class="nk-tb-col text-center text-blue">{{ number_format($previous->get(0)->inv) }}</td>

                    <!-- MONTH - 2 -->
                    <td class="nk-tb-col text-center text-blue">{{ number_format($previous->get(1)->inv) }}</td>

                    <!-- MONTH - 1 -->
                    <td class="nk-tb-col text-center text-blue">{{ number_format($estimated ? $last_month['avg']['inv'] : $previous->get(2)->inv) }}</td>

                    <!-- Current Month -->
                    <td class="nk-tb-col text-center text-blue">{{ number_format($calculations['inv']) }}</td>

                    <!-- Average -->
                    <td class="nk-tb-col text-center text-danger">{{ number_format($calculations['avg']['inv']) }}</td>
                </tr>

                <!-- COST -->
                <tr class="nk-tb-item">
                    <!-- Label -->
                    <th class="nk-tb-col">COST</th>

                    <!-- MONTH - 3 -->
                    <td class="nk-tb-col text-center text-blue">{{ number_format($previous->get(0)->cost) }}</td>

                    <!-- MONTH - 2 -->
                    <td class="nk-tb-col text-center text-blue">{{ number_format($previous->get(1)->cost) }}</td>

                    <!-- MONTH - 1 -->
                    <td class="nk-tb-col text-center text-blue">{{ number_format($estimated ? $last_month['avg']['cost'] : $previous->get(2)->cost) }}</td>

                    <!-- Current Month -->
                    <td class="nk-tb-col text-center text-danger">{{ number_format($calculations['cost']) }}</td>

                    <!-- Average -->
                    <td class="nk-tb-col text-center text-danger">{{ number_format($calculations['avg']['cost']) }}</td>
                </tr>

                <!-- FEE-THETA -->
                <tr class="nk-tb-item">
                    <!-- Label -->
                    <th class="nk-tb-col">FEE-THETA</th>

                    <!-- MONTH - 3 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- MONTH - 2 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- MONTH - 1 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- Current Month -->
                    <td class="nk-tb-col text-center text-danger">{{ number_format($calculations['fee_theta']) }}</td>

                    <!-- Average -->
                    <td class="nk-tb-col text-center"></td>
                </tr>

                <!-- FEE -->
                <tr class="nk-tb-item">
                    <!-- FEE -->
                    <th class="nk-tb-col">FEE</th>

                    <!-- MONTH - 3 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- MONTH - 2 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- MONTH - 1 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- Current Month -->
                    <td class="nk-tb-col text-center text-danger">{{ number_format($calculations['fee']) }}</td>

                    <!-- Average -->
                    <td class="nk-tb-col text-center"></td>
                </tr>

                <!-- NETT -->
                <tr class="nk-tb-item">
                    <!-- NETT -->
                    <th class="nk-tb-col">NETT</th>

                    <!-- MONTH - 3 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- MONTH - 2 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- MONTH - 1 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- Current Month -->
                    <td class="nk-tb-col text-center text-danger">{{ number_format($calculations['nett']) }}</td>

                    <!-- Average -->
                    <td class="nk-tb-col text-center"></td>
                </tr>

                <!-- INDEX -->
                <tr class="nk-tb-item">
                    <!-- INDEX -->
                    <th class="nk-tb-col">INDEX {{ $estimated ? "(estimate)" : "" }}</th>

                    <!-- MONTH - 3 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- MONTH - 2 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- MONTH - 1 -->
                    <td class="nk-tb-col text-center"></td>

                    <!-- Current Month -->
                    <td
                        class="nk-tb-col text-center text-primary"
                        style="
                            background-color: {{
                                $calculations['index'] < 0 ? 'rgba(255, 0, 0, 0.1)' : 'rgba(0, 255, 0, 0.1)'
                            }}
                        "
                    >
                        <strong>{{ ($calculations['index']) }}%</strong>
                    </td>

                    <!-- Average -->
                    <td class="nk-tb-col text-center"></td>
                </tr>
            </tbody>
        </table>
    @endif


    <x-slot name="modals">
        <x-dashboard.widgets.modals.modal
            title="Filter Fee Index"
            formMethod="GET"
            :url="route('dashboard.fee-index')"
            action="Filter"
            id="filter"
        >
            <x-dashboard.widgets.forms.select
                label="Month"
                name="month"
                id="filterMonth"
                wrapperClass="col-md-6"
                value="{{ old('month') }}"
            >
                <option selected value="">Select Month</option>
                @for ($i = 4; $i <= count($__months); $i++)
                    <option value="{{ $i }}">{{ $__months[$i] }}</option>
                @endfor
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
                @for ((int) $i = date('Y'); $i >= $leastYear; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </x-dashboard.widgets.forms.select>
        </x-dashboard.widgets.modals.modal>
    </x-slot>
</x-dashboard>
