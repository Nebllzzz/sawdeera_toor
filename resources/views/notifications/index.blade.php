@extends('layouts.main')

@section('title', 'Notifikasi')

@section('content')
    <div class="content-wrapper notifications-page">
        <div class="content-header notif-header">
            <div class="container-fluid">
                <div class="notif-titlebar">
                    <div>
                        <h1>Notifikasi</h1>
                        <small>Semua pembaruan akun, dokumen, dan pembayaran.</small>
                    </div>
                    @if($notifications->count())
                        <form action="{{ route('notifications.readAll') }}" method="POST" style="display:inline">
                            @csrf
                            <button class="btn btn-sm btn-primary">
                                <i class="fas fa-check-double mr-1"></i>
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="notif-card">
                    @forelse ($notifications as $n)
                        <div class="notif-row {{ $n->read_at ? 'is-read' : 'is-unread' }}">
                            <div class="notif-icon">
                                <img src="{{ asset('img/logo-kecil.png') }}" alt="Sawdeera Toor">
                            </div>

                            <div class="notif-body">
                                <div class="notif-main">
                                    <h5>{{ $n->data['title'] ?? class_basename($n->type) }}</h5>
                                    <p>{{ $n->data['message'] ?? '' }}</p>
                                    <span>{{ $n->created_at->diffForHumans() }}</span>
                                </div>

                                <div class="notif-actions">
                                    @if(!empty($n->data['url']))
                                        <a href="{{ $n->data['url'] }}" class="btn btn-sm btn-light-primary">Lihat Detail</a>
                                    @endif

                                    @if (!$n->read_at)
                                        <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Tandai Dibaca</button>
                                        </form>
                                    @else
                                        <span class="notif-status"><i class="fas fa-check mr-1"></i>Dibaca</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="notif-empty">
                            <i class="far fa-bell"></i>
                            <b>Belum ada notifikasi</b>
                            <small>Notifikasi baru akan tampil di halaman ini.</small>
                        </div>
                    @endforelse

                    @if($notifications->hasPages())
                        <div class="notif-pagination">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <style>
        .notifications-page {
            background: #fbfaf8;
            min-height: calc(100vh - 96px);
            padding: 24px;
        }

        .notif-header {
            padding: 0 0 18px;
        }

        .notif-titlebar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .notif-titlebar h1 {
            margin: 0 0 4px;
            font-size: 24px;
            font-weight: 800;
            color: #1f2937;
        }

        .notif-titlebar small {
            color: #6b7280;
        }

        .notif-card {
            background: #fff;
            border: 1px solid #eee8dd;
            border-radius: 10px;
            box-shadow: 0 6px 22px rgba(44, 31, 17, .05);
            overflow: hidden;
        }

        .notif-row {
            display: flex;
            gap: 14px;
            padding: 16px 18px;
            border-bottom: 1px solid #f0ece4;
        }

        .notif-row:last-child {
            border-bottom: 0;
        }

        .notif-row.is-unread {
            background: #fffaf1;
        }

        .notif-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #f8efe0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .notif-icon img {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
        }

        .notif-body {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            width: 100%;
            min-width: 0;
        }

        .notif-main {
            min-width: 0;
        }

        .notif-main h5 {
            margin: 0 0 5px;
            font-size: 15px;
            font-weight: 800;
            color: #1f2937;
        }

        .notif-main p {
            margin: 0 0 7px;
            color: #4b5563;
            line-height: 1.45;
        }

        .notif-main span,
        .notif-status {
            color: #6b7280;
            font-size: 12px;
        }

        .notif-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
        }

        .notif-pagination {
            padding: 14px 18px;
            border-top: 1px solid #f0ece4;
        }

        .notif-empty {
            min-height: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #6b7280;
            text-align: center;
        }

        .notif-empty i {
            font-size: 34px;
            color: #b88735;
            margin-bottom: 6px;
        }

        .notif-empty b {
            color: #1f2937;
        }

        @media (max-width: 767.98px) {
            .notifications-page {
                padding: 16px;
            }

            .notif-titlebar,
            .notif-body,
            .notif-actions {
                align-items: flex-start;
                flex-direction: column;
            }

            .notif-actions {
                width: 100%;
            }
        }
    </style>
@endsection
