@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="mdi mdi-check-all label-icon"></i><strong>Success</strong> - {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-center mt-3">
                            <div class="col-xl-5 col-lg-8">
                                <div class="text-center">
                                    <h5>Welcome to the "Marketing Dashboard"</h5>
                                    <p class="text-muted">Here you can manage users to access SSO PT Olefina Tifaplas Polikemindo, & You are able to manage your Master Data on the system</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Confirmation (KO)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="text-muted mb-3 lh-1 d-block text-truncate">Request / Un Post</span>
                                                <h4 class="mb-3">
                                                    <span>{{ $unposted }}</span>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-success-subtle text-success">+{{ $unpostedToday }}</span>
                                            <span class="ms-1 text-muted font-size-13">Hari Ini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="text-info mb-3 lh-1 d-block text-truncate">Posted</span>
                                                <h4 class="mb-3">
                                                    <span class="text-info">{{ $posted }}</span>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-success-subtle text-success">+{{ $postedToday }}</span>
                                            <span class="ms-1 text-muted font-size-13">Hari Ini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="text-success mb-3 lh-1 d-block text-truncate">Closed</span>
                                                <h4 class="mb-3">
                                                    <span class="text-success">{{ $closed }}</span>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-success-subtle text-success">+{{ $closedToday }}</span>
                                            <span class="ms-1 text-muted font-size-13">Hari Ini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="fw-bold text-primary mb-3 lh-1 d-block text-truncate">Total</span>
                                                <h4 class="mb-3">
                                                    <span class="text-primary">{{ $total }}</span>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-success-subtle text-success">+{{ $totalToday }}</span>
                                            <span class="ms-1 text-muted font-size-13">Hari Ini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Sales Order (SO)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="text-muted mb-3 lh-1 d-block text-truncate">Request / Un Post</span>
                                                <h4 class="mb-3">
                                                    <span>{{ $unpostedSO }}</span>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-success-subtle text-success">+{{ $unpostedTodaySO }}</span>
                                            <span class="ms-1 text-muted font-size-13">Hari Ini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="text-info mb-3 lh-1 d-block text-truncate">Posted</span>
                                                <h4 class="mb-3">
                                                    <span class="text-info">{{ $postedSO }}</span>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-success-subtle text-success">+{{ $postedTodaySO }}</span>
                                            <span class="ms-1 text-muted font-size-13">Hari Ini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="text-success mb-3 lh-1 d-block text-truncate">Closed</span>
                                                <h4 class="mb-3">
                                                    <span class="text-success">{{ $closedSO }}</span>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-success-subtle text-success">+{{ $closedTodaySO }}</span>
                                            <span class="ms-1 text-muted font-size-13">Hari Ini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="fw-bold text-primary mb-3 lh-1 d-block text-truncate">Total</span>
                                                <h4 class="mb-3">
                                                    <span class="text-primary">{{ $totalSO }}</span>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-success-subtle text-success">+{{ $totalTodaySO }}</span>
                                            <span class="ms-1 text-muted font-size-13">Hari Ini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
