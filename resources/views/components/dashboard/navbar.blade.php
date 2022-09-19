@php
    $user = auth()->user();
@endphp
<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <!-- Sidebar toggler -->
            <div class="nk-menu-trigger d-xl-none ml-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu">
                    <em class="icon ni ni-menu"></em>
                </a>
            </div>

            <!-- Main content -->
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    <!-- User settings (dropdown) -->
                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <div class="user-toggle">
                                <!-- Avatar -->
                                <div class="user-avatar sm">
                                    <em class="icon ni ni-user-alt"></em>
                                </div>
                                <!-- Details -->
                                <div class="user-info d-none d-md-block">
                                    <div class="user-status">
                                        {{ $user?->isAdmin ? "Administrator" : "User" }}
                                    </div>
                                    <div class="user-name dropdown-indicator">
                                        {{ $user?->name }}
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Additional details (dropdown content) -->
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right dropdown-menu-s1">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <!-- Avatar -->
                                    <div class="user-avatar">
                                        <span>AB</span>
                                    </div>

                                    <!-- Name + Email -->
                                    <div class="user-info">
                                        <span class="lead-text">{{ $user?->name }}</span>
                                        <span class="sub-text">{{ $user?->email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <!-- Profile editing -->
                                    <li>
                                        <a href="{{ route('dashboard.profile') }}">
                                            <em class="icon ni ni-user-alt"></em>
                                            <span>View Profile</span>
                                        </a>
                                    </li>

                                    <!-- Dark mode toggler -->
                                    <li>
                                        <a class="dark-switch" href="#">
                                            <em class="icon ni ni-moon"></em>
                                            <span>Dark Mode</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Logout link -->
                            <form action="{{ route('logout') }}" method="POST" class="dropdown-inner">
                                @csrf
                                <ul class="link-list">
                                    <li>
                                        <em class="icon ni ni-signout"></em>
                                        <button type="submit" style="
                                                color: #526484;
                                                background: transparent;
                                                font-size: 13px;
                                                font-weight: 500;
                                                line-height: 1.4rem;
                                                display: inline;
                                                margin: 0 0 0 0.25rem;
                                                padding: 0.575rem 0;
                                                border: none;
                                            ">
                                            Sign out
                                        </button>
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
