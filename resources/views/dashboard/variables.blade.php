<x-dashboard
    title="Variables"
    pageTitle="Variables"
    description="Set variables to calculate fee index of different months"
>

    <div class="card py-3 w-50 mx-auto mt-3">
        <div class="card-inner card-inner-md">
            <form
                action="{{ route('dashboard.variables.update') }}"
                method="POST"
                class="entry"
            >
                @csrf
                @method('PUT')

                <!-- Amount -->
                <x-dashboard.widgets.forms.input
                    label="Safe"
                    type="number"
                    name="safe"
                    value="{{ $safe }}"
                    placeholder="Enter paid amount"
                    required
                />

                <!-- Span -->
                <x-dashboard.widgets.forms.input
                    label="Span"
                    type="number"
                    name="span"
                    value="{{ $span }}"
                    placeholder="Enter span"
                    required
                />

                <!-- Fee theta -->
                <x-dashboard.widgets.forms.input
                    label="Fee-Theta Percentage"
                    type="number"
                    name="fee_theta"
                    value="{{ $fee_theta }}"
                    placeholder="Enter fee-theta"
                    required
                />

                <div class="d-flex justify-content-end mx-2">
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-dashboard>
