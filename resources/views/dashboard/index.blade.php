<x-dashboard
    title="Dashboard"
    pageTitle="Dashboard"
    description="Welcome to Lumina Digital dashboard"
>
    <x-dashboard.widgets.cards.wrapper>
        <x-dashboard.widgets.cards.counter icon="clipboard-fill" title="Active Projects" :count="$active" />
        <x-dashboard.widgets.cards.counter icon="clipboad-check-fill" title="Total Projects" :count="$total" />

        <x-dashboard.widgets.cards.counter
            icon="files-fill"
            title="Contracts"
            count="{{ number_format($contracts) }}"
        />

        <x-dashboard.widgets.cards.counter
            icon="coins"
            title="Remaining Fee"
            count="{{ number_format($remaining_fee) }}"
        />

        <x-dashboard.widgets.cards.counter icon="growth-fill" title="Progress" count="{{ $progress }}%" />

        @if ($calculations)
            <x-dashboard.widgets.cards.counter
                icon="bar-chart-fill"
                title="Fee Index"
                count="{{ $calculations['calculations']['index'] }}%"
            />
        @endif
    </x-dashboard.widgets.cards.wrapper>
</x-dashboard>
