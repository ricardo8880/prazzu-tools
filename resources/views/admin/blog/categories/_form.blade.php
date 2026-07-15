@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <h2 class="h6">Revise os campos abaixo:</h2>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-7">
                <label class="form-label" for="name">Nome</label>
                <input class="form-control" id="name" name="name" required maxlength="100" value="{{ old('name', $category->name) }}" autofocus>
            </div>
            <div class="col-md-5">
                <label class="form-label" for="slug">Slug</label>
                <input class="form-control" id="slug" name="slug" maxlength="120" value="{{ old('slug', $category->slug) }}" placeholder="gerado-automaticamente">
            </div>
            <div class="col-12">
                <label class="form-label" for="description">Descrição</label>
                <textarea class="form-control" id="description" name="description" rows="4" maxlength="1000">{{ old('description', $category->description) }}</textarea>
                <div class="form-text">Uso administrativo para organizar a linha editorial do blog.</div>
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true))>
                    <label class="form-check-label" for="is_active">Categoria ativa</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-body border-0 px-4 pb-4">
        <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.blog.categories.index') }}">Cancelar</a>
            <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i> Salvar categoria</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const name = document.getElementById('name');
    const slug = document.getElementById('slug');
    let slugEdited = slug.value.trim() !== '';

    const slugify = value => value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');

    slug.addEventListener('input', () => { slugEdited = slug.value.trim() !== ''; });
    name.addEventListener('input', () => {
        if (!slugEdited) slug.value = slugify(name.value);
    });
})();
</script>
@endpush
