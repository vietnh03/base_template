@extends('admin.layouts.app')

@section('title', 'Components')

@section('page-title', 'Components')

@section('breadcrumb')
    <span class="breadcrumb-item active">Components</span>
@endsection

@section('content')
    <!-- Buttons Section -->
    <div class="component-section">
        <h2 class="section-title">Buttons</h2>
        <div class="component-demo">
            <button class="btn btn-primary">Primary Button</button>
            <button class="btn btn-secondary">Secondary Button</button>
            <button class="btn btn-success">Success Button</button>
            <button class="btn btn-danger">Danger Button</button>
            <button class="btn btn-warning">Warning Button</button>
            <button class="btn btn-outline">Outline Button</button>
            <button class="btn btn-primary" disabled>Disabled Button</button>
        </div>
    </div>

    <!-- Input Fields Section -->
    <div class="component-section">
        <h2 class="section-title">Input Fields</h2>
        <div class="component-demo">
            <div class="form-group">
                <label class="form-label">Text Input</label>
                <input type="text" class="form-input" placeholder="Enter text...">
            </div>
            <div class="form-group">
                <label class="form-label">Email Input</label>
                <input type="email" class="form-input" placeholder="Enter email...">
            </div>
            <div class="form-group">
                <label class="form-label">Password Input</label>
                <input type="password" class="form-input" placeholder="Enter password...">
            </div>
            <div class="form-group">
                <label class="form-label">Textarea</label>
                <textarea class="form-input" rows="4" placeholder="Enter message..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Disabled Input</label>
                <input type="text" class="form-input" placeholder="Disabled input" disabled>
            </div>
        </div>
    </div>

    <!-- Select Dropdown Section -->
    <div class="component-section">
        <h2 class="section-title">Select Dropdown</h2>
        <div class="component-demo">
            <div class="form-group">
                <label class="form-label">Select Option</label>
                <select class="form-select">
                    <option value="">Choose an option...</option>
                    <option value="1">Option 1</option>
                    <option value="2">Option 2</option>
                    <option value="3">Option 3</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Multi Select</label>
                <select class="form-select" multiple size="4">
                    <option value="1">Item 1</option>
                    <option value="2">Item 2</option>
                    <option value="3">Item 3</option>
                    <option value="4">Item 4</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Checkbox & Radio Section -->
    <div class="component-section">
        <h2 class="section-title">Checkbox & Radio</h2>
        <div class="component-demo">
            <div class="form-group">
                <label class="form-label">Checkboxes</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" class="form-checkbox">
                        <span>Option 1</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" class="form-checkbox" checked>
                        <span>Option 2 (checked)</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" class="form-checkbox">
                        <span>Option 3</span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Radio Buttons</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="radio-demo" class="form-radio" checked>
                        <span>Option A</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="radio-demo" class="form-radio">
                        <span>Option B</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="radio-demo" class="form-radio">
                        <span>Option C</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="component-section">
        <h2 class="section-title">Table</h2>
        <div class="component-demo">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>john@example.com</td>
                            <td><span class="badge badge-primary">Admin</span></td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <button class="btn-icon" title="Edit">✏️</button>
                                <button class="btn-icon" title="Delete">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>jane@example.com</td>
                            <td><span class="badge badge-secondary">User</span></td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <button class="btn-icon" title="Edit">✏️</button>
                                <button class="btn-icon" title="Delete">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Bob Johnson</td>
                            <td>bob@example.com</td>
                            <td><span class="badge badge-secondary">User</span></td>
                            <td><span class="badge badge-danger">Inactive</span></td>
                            <td>
                                <button class="btn-icon" title="Edit">✏️</button>
                                <button class="btn-icon" title="Delete">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cards Section -->
    <div class="component-section">
        <h2 class="section-title">Cards</h2>
        <div class="component-demo">
            <div class="cards-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Card Title</h3>
                    </div>
                    <div class="card-body">
                        <p>This is a basic card component with header and body sections.</p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary">Action</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Another Card</h3>
                    </div>
                    <div class="card-body">
                        <p>Cards can contain any content you want to display.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    <div class="component-section">
        <h2 class="section-title">Alerts</h2>
        <div class="component-demo">
            <div class="alert alert-success">
                <strong>Success!</strong> This is a success alert message.
            </div>
            <div class="alert alert-info">
                <strong>Info!</strong> This is an info alert message.
            </div>
            <div class="alert alert-warning">
                <strong>Warning!</strong> This is a warning alert message.
            </div>
            <div class="alert alert-danger">
                <strong>Error!</strong> This is a danger alert message.
            </div>
        </div>
    </div>

    <!-- Badges Section -->
    <div class="component-section">
        <h2 class="section-title">Badges</h2>
        <div class="component-demo">
            <span class="badge badge-primary">Primary</span>
            <span class="badge badge-secondary">Secondary</span>
            <span class="badge badge-success">Success</span>
            <span class="badge badge-danger">Danger</span>
            <span class="badge badge-warning">Warning</span>
            <span class="badge badge-info">Info</span>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .component-section {
        margin-bottom: 48px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e0e0e0;
    }

    .component-demo {
        background: white;
        padding: 32px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* Buttons */
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        margin-right: 12px;
        margin-bottom: 12px;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #059669;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
        background: #d97706;
    }

    .btn-outline {
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-outline:hover {
        background: #667eea;
        color: white;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #667eea;
    }

    .form-input:disabled {
        background: #f5f5f5;
        cursor: not-allowed;
    }

    /* Checkbox & Radio */
    .checkbox-group,
    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .checkbox-label,
    .radio-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
        color: #333;
    }

    .form-checkbox,
    .form-radio {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    /* Table */
    .table-container {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }

    .data-table th {
        background: #f5f7fa;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .data-table td {
        font-size: 14px;
        color: #666;
    }

    .data-table tbody tr:hover {
        background: #f9fafb;
    }

    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 4px 8px;
        transition: transform 0.2s;
    }

    .btn-icon:hover {
        transform: scale(1.2);
    }

    /* Cards */
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    .card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }

    .card-header {
        padding: 16px 20px;
        background: #f5f7fa;
        border-bottom: 1px solid #e0e0e0;
    }

    .card-header h3 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .card-body {
        padding: 20px;
    }

    .card-body p {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
    }

    .card-footer {
        padding: 16px 20px;
        background: #f5f7fa;
        border-top: 1px solid #e0e0e0;
    }

    /* Alerts */
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        margin-right: 8px;
        margin-bottom: 8px;
    }

    .badge-primary {
        background: #ddd6fe;
        color: #5b21b6;
    }

    .badge-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }
</style>
@endpush
