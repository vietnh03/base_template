@extends('admin.layouts.app')

@section('title', 'Users')

@section('page-title', 'Users')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-item active">Users</span>
@endsection

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters and Actions -->
    <div class="page-header">
        <div class="page-actions">
            <a href="{{ route('admin.user.create') }}" class="btn btn-primary">+ Create User</a>
        </div>
    </div>

    <!-- Filters Form -->
    <div class="filters-card">
        <form method="GET" action="{{ route('admin.user.index') }}" class="filters-form">
            <div class="filter-row">
                <div class="filter-group">
                    <input
                        type="text"
                        name="search"
                        class="form-input"
                        placeholder="Search by name or email..."
                        value="{{ $filters['search'] ?? '' }}"
                    >
                </div>
                <div class="filter-group">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ ($filters['role'] ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ ($filters['role'] ?? '') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="user" {{ ($filters['role'] ?? '') === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                    <a href="{{ route('admin.user.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="table-card">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <strong>{{ $user->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge badge-primary">Admin</span>
                                @elseif($user->role === 'manager')
                                    <span class="badge badge-info">Manager</span>
                                @else
                                    <span class="badge badge-secondary">User</span>
                                @endif
                            </td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.user.edit', $user->id) }}" class="btn-icon" title="Edit">✏️</a>
                                    <form method="POST" action="{{ route('admin.user.destroy', $user->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon" title="Delete">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            {{ $users->links() }}
        </div>
    </div>
@endsection

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-actions {
        display: flex;
        gap: 12px;
    }

    .filters-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    .filter-row {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 150px;
    }

    .filter-group:last-child {
        flex: 0;
        display: flex;
        gap: 8px;
    }

    .table-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .pagination-container {
        padding: 20px;
        border-top: 1px solid #e0e0e0;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
</style>
@endpush
