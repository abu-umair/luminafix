<div class="nk-sidebar nk-sidebar-fixed is-dark">
    <!-- Sidebar header -->
    <div class="nk-sidebar-element nk-sidebar-head">
        <!-- Toggler -->
        <div class="nk-menu-trigger">
            <a
                href="#"
                class="nk-nav-toggle nk-quick-nav-icon d-xl-none"
                data-target="sidebarMenu"
            >
                <em class="icon ni ni-arrow-left"></em>
            </a>
            <a
                href="#"
                class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex"
                data-target="sidebarMenu"
            >
                <em class="icon ni ni-menu"></em>
            </a>
        </div>

        <!-- Logo wrapper -->
        <div class="nk-sidebar-brand">
            <a
                href="{{ url('/') }}"
                class="logo-link nk-sidebar-logo"
            >
                <h4 style="color: #D7C5C5;">
                    {{ config('app.name') }}
                </h4>
            </a>
        </div>
    </div>

    <!-- Sidebar content -->
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar id="sidebarMenu">
                <ul class="nk-menu">
                    <!-- Dashboard -->
                    <li class="nk-menu-item">
                        <a href="{{ route('dashboard') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon ni ni-property"></em>
                            </span>
                            <span class="nk-menu-text">Dashboard</span>
                        </a>
                    </li>

                    <!-- Users management -->
                    <li class="nk-menu-item">
                        <a href="{{ route('dashboard.users') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon ni ni-users"></em>
                            </span>
                            <span class="nk-menu-text">Users</span>
                        </a>
                    </li>

                    <!-- Project management -->
                    <li class="nk-menu-item">
                        <a href="{{ route('dashboard.projects') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon ni ni-tile-thumb-fill"></em>
                            </span>
                            <span class="nk-menu-text">Projects</span>
                        </a>
                    </li>

                    <!-- Invoice management -->
                    <li class="nk-menu-item">
                        <a href="{{ route('dashboard.invoices') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon ni ni-tag-alt"></em>
                            </span>
                            <span class="nk-menu-text">Paid Invoices</span>
                        </a>
                    </li>

                    <!-- Cost Management -->
                    <li class="nk-menu-item">
                        <a href="{{ route('dashboard.costs') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon ni ni-sign-usd"></em>
                            </span>
                            <span class="nk-menu-text">Cost & Balance</span>
                        </a>
                    </li>

                    <!-- Fee Index -->
                    <li class="nk-menu-item">
                        <a href="{{ route('dashboard.fee-index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon ni ni-calender-date"></em>
                            </span>
                            <span class="nk-menu-text">Fee Indexes</span>
                        </a>
                    </li>

                    <!-- Variables management -->
                    <li class="nk-menu-item">
                        <a href="{{ route('dashboard.variables') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon ni ni-calc"></em>
                            </span>
                            <span class="nk-menu-text">Variables</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
