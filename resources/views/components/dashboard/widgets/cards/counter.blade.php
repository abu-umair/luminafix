@props([
    'title' => 'Lorem ipsum',
    'icon'  => 'users',
    'count' => 0
])
<div {{ $attributes->merge(['class' => 'col-sm-6 col-lg-6 col-xxl-6']) }}>
    <div class="card card-bordered">
        <div class="card-inner">
            <!-- Icon -->
            <div class="float-right">
                <em class="icon ni ni-{{ $icon }}" style="font-size: 100px; color: #8CADCE;"></em>
            </div>

            <!-- Title -->
            <div class="card-title-group align-start mb-2">
                <div class="card-title">
                    <h6 class="title">{{ $title }}</h6>
                </div>
            </div>

            <!-- Count -->
            <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                <div class="nk-sale-data">
                    <span class="amount">{{ $count }}</span>
                </div>
            </div>
        </div>
    </div><!-- .card -->
</div>
