@extends('admin.layouts.app')

@section('title', 'Create User')

@section('page-title', 'Create User')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.user.index') }}" class="breadcrumb-item">Users</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-item active">Create</span>
@endsection

@section('content')
    <div class="form-card">
        <form method="POST" action="{{ route('admin.user.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Name <span class="required">*</span></label>
                <input
                    type="text"
                    name="name"
                    class="form-input @error('name') error @enderror"
                    value="{{ old('name') }}"
                    required
                >
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email <span class="required">*</span></label>
                <input
                    type="email"
                    name="email"
                    class="form-input @error('email') error @enderror"
                    value="{{ old('email') }}"
                    required
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password <span class="required">*</span></label>
                    <input
                        type="password"
                        name="password"
                        class="form-input @error('password') error @enderror"
                        required
                    >
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password <span class="required">*</span></label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-input"
                        required
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Role <span class="required">*</span></label>
                    <select name="role" class="form-select @error('role') error @enderror" required>
                        <option value="">Select Role</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                    @error('role')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status <span class="required">*</span></label>
                    <select name="status" class="form-select @error('status') error @enderror" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="{{ route('admin.user.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .form-card {
        background: white;
        padding: 32px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        max-width: 800px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #e0e0e0;
    }

    .required {
        color: #ef4444;
    }

    .error-message {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
    }

    .form-input.error,
    .form-select.error {
        border-color: #ef4444;
    }
</style>
@endpush
