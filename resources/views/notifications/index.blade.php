@extends('layouts.main')

@section('title', 'Notifikasi')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Notifikasi</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <form action="{{ route('notifications.readAll') }}" method="POST" style="display:inline">
                            @csrf
                            <button class="btn btn-sm btn-primary">Tandai Semua Dibaca</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        @foreach ($notifications as $n)
                            <div class="media p-3"
                                style="background: {{ $n->read_at ? '#fff' : '#f8f9fa' }}; border-radius:6px; margin-bottom:8px;">
                                <img src="{{ asset('img/logo-kecil.png') }}" class="mr-3 img-size-50 img-circle"
                                    alt="">
                                <div class="media-body">
                                    <h5 style="margin:0">{{ $n->data['title'] ?? class_basename($n->type) }}</h5>
                                    <p style="margin:0">{{ $n->data['message'] ?? '' }}</p>
                                    <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="ml-3">
                                    @if (!$n->read_at)
                                        <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Tandai Dibaca</button>
                                        </form>
                                    @else
                                        <span class="text-success">Dibaca</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
