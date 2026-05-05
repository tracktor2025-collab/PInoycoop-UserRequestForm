@extends('pinoycoop_admin.layouts.app', ['title' => 'Admin Users'])

@section('content')
    <div class="top">
        <h2>Users</h2>
    </div>
    <div class="card">
        <div class="head">User Accounts</div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ optional($user->created_at)->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
