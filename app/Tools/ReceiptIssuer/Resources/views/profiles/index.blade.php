@extends('layouts.app')
@section('title', 'Saved Receipt Profiles | Prazzu Tools')
@section('meta_description', 'Manage reusable payer and payee profiles for the Receipt Generator.')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3 mb-1">Saved receipt profiles</h1><p class="text-body-secondary mb-0">Store reusable payer or payee details.</p></div><a class="btn btn-outline-secondary" href="{{ route('tools.receipt-generator.index') }}">Back to receipt generator</a></div>
    @if(session('profile_message'))<div class="alert alert-success">{{ session('profile_message') }}</div>@endif
    <div class="card mb-4"><div class="card-body"><form method="POST" action="{{ route('tools.receipt-generator.profiles.store') }}" class="row g-3">@csrf
        <div class="col-md-3"><x-tools.form.select name="type" label="Profile type" :options="['payer'=>'Payer','payee'=>'Payee']" :value="old('type','payer')" required /></div>
        <div class="col-md-5"><x-tools.form.input name="name" label="Name / business name" :value="old('name')" required /></div>
        <div class="col-md-4"><x-tools.form.input name="document" label="TIN / EIN / reference ID (optional)" :value="old('document')" /></div>
        <div class="col-12"><button class="btn btn-primary" type="submit">Save profile</button></div>
    </form></div></div>
    <div class="row g-3">@forelse($profiles as $profile)<div class="col-md-6"><div class="card h-100"><div class="card-body"><span class="badge text-bg-light border mb-2">{{ ucfirst($profile->type) }}</span><h2 class="h5">{{ $profile->name }}</h2><p class="text-body-secondary">{{ $profile->document ?: 'No identifier saved' }}</p><div class="d-flex gap-2"><form method="POST" action="{{ route('tools.receipt-generator.profiles.use',$profile->id) }}">@csrf<button class="btn btn-sm btn-outline-primary" type="submit">Use in receipt</button></form><form method="POST" action="{{ route('tools.receipt-generator.profiles.destroy',$profile->id) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form></div></div></div></div>@empty<div class="col-12"><div class="alert alert-light border">No saved profiles yet.</div></div>@endforelse</div>
</div>
@endsection
