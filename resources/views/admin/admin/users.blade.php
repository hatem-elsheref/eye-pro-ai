@extends('admin.layouts.master')

@section('title', 'Users Management - Eye Pro')
@section('page-title', 'Users Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full bg-white opacity-10"></div>
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white mb-1">Users Management</h1>
            <p class="text-sm text-blue-50 font-medium">Manage all non-admin users and their status</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Users -->
        <div class="group bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border-2 border-gray-100 hover:border-blue-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-full -mr-16 -mt-16 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center justify-center h-16 w-16 rounded-2xl shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                        <i class="fas fa-users text-3xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-bold mb-2 uppercase tracking-wide">Total Users</h3>
                <p class="text-5xl font-black text-blue-700 mb-2">{{ $totalUsers ?? 0 }}</p>
                <p class="text-xs text-gray-400 font-medium">Registered users</p>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="group bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border-2 border-gray-100 hover:border-amber-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-100 rounded-full -mr-16 -mt-16 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center justify-center h-16 w-16 rounded-2xl shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-clock text-3xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-bold mb-2 uppercase tracking-wide">Pending Approvals</h3>
                <p class="text-5xl font-black text-amber-700 mb-2">{{ $pendingUsers ?? 0 }}</p>
                <p class="text-xs text-gray-400 font-medium">Users awaiting approval</p>
            </div>
        </div>
        
        <!-- Approved Users -->
        <div class="group bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border-2 border-gray-100 hover:border-green-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-100 rounded-full -mr-16 -mt-16 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center justify-center h-16 w-16 rounded-2xl shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-check-circle text-3xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-bold mb-2 uppercase tracking-wide">Approved Users</h3>
                <p class="text-5xl font-black text-green-700 mb-2">{{ $approvedUsers ?? 0 }}</p>
                <p class="text-xs text-gray-400 font-medium">Active users</p>
            </div>
        </div>

        <!-- Rejected Users -->
        <div class="group bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border-2 border-gray-100 hover:border-red-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-100 rounded-full -mr-16 -mt-16 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center justify-center h-16 w-16 rounded-2xl shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <i class="fas fa-ban text-3xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-bold mb-2 uppercase tracking-wide">Rejected Users</h3>
                <p class="text-5xl font-black text-red-700 mb-2">{{ $rejectedUsers ?? 0 }}</p>
                <p class="text-xs text-gray-400 font-medium">Rejected accounts</p>
            </div>
        </div>
    </div>

    <!-- Users Management Table -->
    <div class="bg-white rounded-3xl shadow-xl border-2 border-gray-200 overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b-2 border-blue-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900">All Users</h2>
                        <p class="text-sm text-gray-500">Manage all non-admin users</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Status Filter -->
                    <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="p-6 bg-white">
            <div class="overflow-hidden rounded-xl border-2 border-gray-300 shadow-lg bg-white">
                <table id="usersTable" class="min-w-full bg-white" style="width:100%">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 via-gray-50 to-gray-100 border-b-2 border-gray-300">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">User</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Registered</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    /* DataTables Wrapper */
    #usersTable_wrapper {
        padding: 0;
    }
    
    /* Search Input */
    #usersTable_wrapper .dataTables_filter {
        margin-bottom: 1.5rem;
    }
    
    #usersTable_wrapper .dataTables_filter input {
        padding: 0.625rem 1rem;
        padding-left: 2.5rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s;
        background: white;
        width: 280px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    #usersTable_wrapper .dataTables_filter input:focus {
        outline: none;
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
    }
    
    #usersTable_wrapper .dataTables_filter label {
        position: relative;
    }
    
    #usersTable_wrapper .dataTables_filter label::before {
        content: '\f002';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        z-index: 1;
    }
    
    /* Length Select */
    #usersTable_wrapper .dataTables_length {
        margin-bottom: 1.5rem;
    }
    
    #usersTable_wrapper .dataTables_length select {
        padding: 0.625rem 2.5rem 0.625rem 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    #usersTable_wrapper .dataTables_length select:focus {
        outline: none;
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
    }
    
    /* Table Styling - Professional with Borders */
    #usersTable {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #usersTable tbody tr {
        transition: all 0.2s;
        border-bottom: 1px solid #e5e7eb;
    }
    
    #usersTable tbody tr:last-child {
        border-bottom: none;
    }
    
    #usersTable tbody tr:hover {
        background-color: #f8fafc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    
    #usersTable tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-right: 1px solid #e5e7eb;
        font-size: 0.875rem;
        color: #374151;
    }
    
    #usersTable tbody td:last-child {
        border-right: none;
    }
    
    #usersTable thead th {
        padding: 1rem 1.5rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #d1d5db;
        border-right: 1px solid #e5e7eb;
        background: linear-gradient(to bottom, #f9fafb, #f3f4f6);
        position: relative;
    }
    
    #usersTable thead th:last-child {
        border-right: none;
    }
    
    #usersTable thead th:first-child {
        border-top-left-radius: 0.75rem;
    }
    
    #usersTable thead th:last-child {
        border-top-right-radius: 0.75rem;
    }
    
    /* Enhanced table container */
    #usersTable_wrapper {
        background: white;
    }
    
    #usersTable_wrapper .dataTables_scrollBody {
        border: 1px solid #e5e7eb !important;
    }
    
    /* Table wrapper borders */
    #usersTable_wrapper .dataTables_scroll {
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    /* Better cell alignment and spacing */
    #usersTable tbody td {
        background-color: white;
    }
    
    #usersTable tbody tr.even td {
        background-color: #fafbfc;
    }
    
    #usersTable tbody tr.odd td {
        background-color: white;
    }
    
    /* Action buttons styling */
    #usersTable .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    
    #usersTable .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    /* Pagination */
    #usersTable_wrapper .dataTables_paginate {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 2px solid #f3f4f6;
    }
    
    #usersTable_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.875rem;
        margin: 0 0.25rem;
        border-radius: 0.5rem;
        border: 2px solid #e5e7eb;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s;
        background: white;
    }
    
    #usersTable_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f9fafb;
        border-color: #60a5fa;
        color: #60a5fa !important;
        transform: translateY(-1px);
    }
    
    #usersTable_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);
        color: white !important;
        border: none;
        box-shadow: 0 4px 12px rgba(96, 165, 250, 0.3);
    }
    
    #usersTable_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    
    /* Info Text */
    #usersTable_wrapper .dataTables_info {
        padding-top: 1rem;
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    /* Processing Indicator */
    #usersTable_processing {
        background: rgba(255, 255, 255, 0.95);
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        font-weight: 600;
        color: #60a5fa;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.users.data') }}",
            data: function (d) {
                d.status_filter = $('#statusFilter').val();
            }
        },
        columns: [
            { 
                data: null,
                render: function(data, type, row) {
                    return '<div class="flex items-center space-x-3">' + row.avatar + '<span class="text-sm font-semibold text-gray-900">' + row.name + '</span></div>';
                },
                name: 'name',
                orderable: true
            },
            { data: 'email', name: 'email' },
            { data: 'status_badge', name: 'status', orderable: true },
            { data: 'created_at', name: 'created_at', searchable: false, orderable: true },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[3, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: '<div class="flex items-center space-x-2"><div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div><span>Loading...</span></div>',
            search: "",
            searchPlaceholder: "Search users by name or email...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ users",
            infoEmpty: "No users found",
            infoFiltered: "(filtered from _MAX_ total users)",
            zeroRecords: "No matching users found",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                last: '<i class="fas fa-angle-double-right"></i>',
                next: '<i class="fas fa-chevron-right"></i>',
                previous: '<i class="fas fa-chevron-left"></i>'
            },
            emptyTable: "No users available"
        },
        dom: '<"flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6"<"flex items-center gap-4"<"text-sm text-gray-600 font-semibold">l>f>>rt<"flex flex-col md:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t-2 border-gray-200"ip>',
        processing: true,
        drawCallback: function() {
            // Ensure pagination is visible
            $('.dataTables_paginate').css('display', 'flex');
            $('.dataTables_info').css('display', 'block');
        }
    });
    
    // Status filter change handler
    $('#statusFilter').on('change', function() {
        table.ajax.reload();
    });
    
    // Handle approve/reject forms
    $(document).on('submit', 'form[action*="approve"], form[action*="reject"]', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val() || 'POST';
        var action = url.includes('approve') ? 'approve' : 'reject';
        
        if (action === 'reject' && !confirm('Are you sure you want to reject this user?')) {
            return false;
        }
        
        $.ajax({
            url: url,
            type: method,
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                var message = xhr.responseJSON?.message || 'An error occurred';
                alert(message);
            }
        });
        
        return false;
    });
});
</script>
@endpush
@endsection


