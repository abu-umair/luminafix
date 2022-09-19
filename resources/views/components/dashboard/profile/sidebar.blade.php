@props(['user'])
<div
    class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg toggle-screen-lg"
    data-content="userAside"
    data-toggle-screen="lg"
    data-toggle-overlay="true"
>
    <div class="card-inner-group">

        <!-- User details-->
        <div class="card-inner">
            <div class="user-card">

                <!-- Avatar -->
                <div class="user-avatar bg-primary">
                    <span>{{ App\Helpers\Initials::generate($user->name) }}</span>
                </div>

                <!-- User details -->
                <div class="user-info">
                    <span class="lead-text">{{ $user->name }}</span>
                    <span class="sub-text">{{ $user->email }}</span>
                </div>

                <!-- Profile actions -->
                <div class="user-action">
                    <div class="dropdown">
                        <a class="btn btn-icon btn-trigger mr-n2" data-toggle="dropdown" href="#">
                            <em class="icon ni ni-more-v"></em>
                        </a>
                        <!-- Avatar actions dropdown -->
                        <div class="dropdown-menu dropdown-menu-right">
                            <ul class="link-list-opt no-bdr">
                                <li>
                                    <a href="#">
                                        <em class="icon ni ni-camera-fill"></em>
                                        <span>Change Photo</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <em class="icon ni ni-edit-fill"></em>
                                        <span>Update Profile</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Setting tabs -->
        <div class="card-inner p-0">
            <ul class="link-list-menu">
                <!-- Personal information -->
                {{-- <li>
                    <a class="active" href="profile_personal.html">
                        <em class="icon ni ni-user-fill-c"></em>
                        <span>Personal Infomation</span>
                    </a>
                </li> --}}
            </ul>
        </div>

    </div>
</div>
