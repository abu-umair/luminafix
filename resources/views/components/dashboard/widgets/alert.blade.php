@if ($errors->any())
    <div class="alert alert-danger alert-dismissible mb-4" role="alert"
        style="background-color: #f8d7da; border-color:#f5c6cb; color:#721c24; margin-bottom: 0px;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close"
            style="text-decoration: none;"></a>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('message'))
    <div class="alert alert-danger alert-dismissible mb-4" role="alert"
        style="background-color: #f8d7da; border-color:#f5c6cb; color:#721c24; margin-bottom: 0px;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close"
            style="text-decoration: none;"></a>
        {{ session()->get('message') }}
    </div>
@endif
@if (session('updated'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert"
        style="background-color: #d4edda; border-color:#c3e6cb; color:#155724; margin-bottom: 0px;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close"
            style="text-decoration: none;"></a>
        {{ session()->get('updated') }}
    </div>
@endif
@if (session('created'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert"
        style="background-color: #d4edda; border-color:#c3e6cb; color:#155724; margin-bottom: 0px;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close"
            style="text-decoration: none;"></a>
        {{ session()->get('created') }}
    </div>
@endif
@if (session('deleted'))
    <div
        class="alert alert-success alert-dismissible mb-4"
        role="alert"
        style="background-color: #d4edda; border-color:#c3e6cb; color:#155724; margin-bottom: 0px;"
    >
        <a
            href="#"
            class="close"
            data-dismiss="alert"
            aria-label="close"
            style="text-decoration: none;"
        ></a>
        {{ session()->get('deleted') }}
    </div>
@endif
@if (session('revoked'))
    <div class="alert alert-warning mb-4">
        {{ session('revoked') }}
    </div>
@endif
