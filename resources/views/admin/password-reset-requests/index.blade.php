@extends('dashboard')

@section('content')

<div class="container mt-5">
    <h2>Permintaan Reset Password</h2>

    @if($requests->isEmpty())
        <div class="alert alert-info">
            Tidak ada permintaan reset password.
        </div>
    @else
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Tanggal Diminta</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->user->name ?? 'N/A' }}</td>
                        <td>{{ $request->user->email ?? 'N/A' }}</td>
                        <td>
                            @if($request->status == 'pending')
                                <span class="badge bg-warning">Menunggu</span>
                            @else
                                <span class="badge bg-success">Diproses</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($request->status == 'pending')
                                <form method="POST" action="{{ route('admin.requests.reset', $request->id_user) }}" 
                                      style="display:inline;" onsubmit="return confirm('Reset password user ini ke default (123456)?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success">
                                        ✔ Reset
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-secondary">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $requests->links() }}
        </div>
    @endif
</div>

<style>
    .btn {
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
    }
</style>

@endsection
