@extends('layouts.app')

@section('title', 'Create business — Prazzu Tools')
@section('meta_description', 'Create a business to manage Prazzu Plus access.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">Business account</span>
                        <h1 class="h3 mb-2">Create business</h1>
                        <p class="text-body-secondary mb-4">
                            The business will manage only Prazzu Plus access. Each team member will keep their own sign-in, history, favorites, and results.
                        </p>

                        <form method="POST" action="{{ route('organizations.store') }}" novalidate>
                            @csrf
                            <div class="mb-4">
                                <label class="form-label" for="name">Business name</label>
                                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" maxlength="160" required autofocus>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="alert alert-info" role="note">
                                <i class="bi bi-shield-check me-2" aria-hidden="true"></i>
                                Your account will be set as the business owner. No personal team member data will be shared.
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <button class="btn btn-primary" type="submit">Create business</button>
                                <a class="btn btn-outline-secondary" href="{{ route('account.show') }}">Cancel</a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
