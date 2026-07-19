@if ($errors->any())
    <div {{ $attributes->class(['alert alert-danger']) }} role="alert" aria-live="polite">
        <div class="d-flex gap-2">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <div>
                <strong>Revise os campos informados.</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
