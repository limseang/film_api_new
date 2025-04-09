@extends('layouts.master')
@section('title')
Dashboard
@endsection
@section('content')
<!-- Main charts -->
<div class="row">

    <!-- Welcome card -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{__('global.welcome_dashboard')}} </h5>
            </div>

            <div class="card-body">
                <p class="mb-3">
                    {{__('global.welcome')}} <strong>{{Auth::user()->name}}</strong> {{__('global.come_back')}} CinemagicKH Admin..!
                </p>
                
                <!-- User Stats Overview -->
                <div class="row mt-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body py-2 text-center">
                                <h3 class="mb-0">{{ $onlineUsersCount }}</h3>
                                <small>Users Online Now</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card bg-success text-white">
                            <div class="card-body py-2 text-center">
                                <h3 class="mb-0">{{ $activeUsersCount }}</h3>
                                <small>Active in Last 30 Days</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card bg-info text-white">
                            <div class="card-body py-2 text-center">
                                <h3 class="mb-0">{{ $totalUsersCount }}</h3>
                                <small>Total Registered Users</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card bg-secondary text-white">
                            <div class="card-body py-2 text-center">
                                <h3 class="mb-0">{{ number_format(($onlineUsersCount / max(1, $totalUsersCount)) * 100, 1) }}%</h3>
                                <small>Online Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Online Users Card -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Online Users</h5>
                <span class="badge bg-success">{{ $onlineUsersCount }} Online</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Last Active</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($onlineUserDetails as $userDetail)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                @if($userDetail['user']->avatar)
                                                    <img src="{{ asset($userDetail['user']->avatar) }}" class="rounded-circle" width="32" height="32" alt="User avatar">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        {{ strtoupper(substr($userDetail['user']->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>{{ $userDetail['user']->name }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $userDetail['user']->email }}</td>
                                    <td>
                                        @if($userDetail['user']->role_id == 1)
                                            <span class="badge bg-danger">Super Admin</span>
                                        @elseif($userDetail['user']->role_id == 2)
                                            <span class="badge bg-warning">Admin</span>
                                        @else
                                            <span class="badge bg-info">User</span>
                                        @endif
                                    </td>
                                    <td>{{ $userDetail['last_active_time'] }}</td>
                                    <td>
                                        @if(isset($userDetail['status']['is_admin']) && $userDetail['status']['is_admin'])
                                            <span class="badge bg-primary">Admin Portal</span>
                                        @else
                                            <span class="badge bg-success">Mobile App</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No users currently online</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Income/Expense Summary -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Financial Summary</h5>
                <a href="{{ route('report_income_expense.index') }}" class="btn btn-primary btn-sm">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Currency</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totals = ['USD' => ['income' => 0, 'expense' => 0], 'KHR' => ['income' => 0, 'expense' => 0]];
                            @endphp
                            
                            @forelse($incomeExpenseSummary as $summary)
                                <tr>
                                    <td>
                                        @if($summary->type == 1)
                                            <span class="badge bg-success">Income</span>
                                            @php $totals[$summary->currency]['income'] += $summary->total; @endphp
                                        @elseif($summary->type == 2)
                                            <span class="badge bg-danger">Expense</span>
                                            @php $totals[$summary->currency]['expense'] += $summary->total; @endphp
                                        @endif
                                    </td>
                                    <td>{{ $summary->currency }}</td>
                                    <td class="text-end">{{ number_format($summary->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No financial data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            @foreach($totals as $currency => $values)
                                @if($values['income'] > 0 || $values['expense'] > 0)
                                    <tr class="table-active">
                                        <th>Balance</th>
                                        <th>{{ $currency }}</th>
                                        <th class="text-end {{ ($values['income'] - $values['expense']) >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($values['income'] - $values['expense'], 2) }}
                                        </th>
                                    </tr>
                                @endif
                            @endforeach
                        </tfoot>
                    </table>
                </div>
                
                <div class="mt-3">
                    <h6>Monthly Report ({{ date('Y') }})</h6>
                    <div class="chart-container" style="height: 200px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Request Films -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Film Requests</h5>
                <a href="{{ route('request_film.index') }}" class="btn btn-primary btn-sm">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Film Name</th>
                                <th>Requested By</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $requestFilms = \App\Models\RequestFilm::with('user')->orderBy('created_at', 'desc')->take(5)->get();
                            @endphp
                            
                            @forelse($requestFilms as $requestFilm)
                                <tr>
                                    <td>{{ $requestFilm->film_name }}</td>
                                    <td>{{ $requestFilm->user ? $requestFilm->user->name : 'Unknown' }}</td>
                                    <td>
                                        @if($requestFilm->status == 1)
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($requestFilm->status == 2)
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($requestFilm->status == 3)
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $requestFilm->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('request_film.edit', $requestFilm->id) }}" class="btn btn-sm btn-info">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No film requests found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Request Status Summary -->
                <div class="mt-3">
                    <h6>Request Status Summary</h6>
                    <div class="row">
                        @php
                            $pendingCount = \App\Models\RequestFilm::where('status', 1)->count();
                            $completedCount = \App\Models\RequestFilm::where('status', 2)->count();
                            $rejectedCount = \App\Models\RequestFilm::where('status', 3)->count();
                            $totalRequests = $pendingCount + $completedCount + $rejectedCount;
                        @endphp
                        
                        <div class="col-4 text-center">
                            <div class="card bg-warning-subtle">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $pendingCount }}</h3>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="card bg-success-subtle">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $completedCount }}</h3>
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="card bg-danger-subtle">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $rejectedCount }}</h3>
                                    <small>Rejected</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly chart data
        const monthlyData = @json($monthlyReport);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        // Prepare datasets
        const incomeUSD = Array(12).fill(0);
        const expenseUSD = Array(12).fill(0);
        const incomeKHR = Array(12).fill(0);
        const expenseKHR = Array(12).fill(0);
        
        // Fill in the data
        monthlyData.forEach(item => {
            const monthIndex = item.month - 1; // Month is 1-indexed
            if (item.currency === 'USD') {
                if (item.type === 1) {
                    incomeUSD[monthIndex] = parseFloat(item.total);
                } else if (item.type === 2) {
                    expenseUSD[monthIndex] = parseFloat(item.total);
                }
            } else if (item.currency === 'KHR') {
                if (item.type === 1) {
                    incomeKHR[monthIndex] = parseFloat(item.total);
                } else if (item.type === 2) {
                    expenseKHR[monthIndex] = parseFloat(item.total);
                }
            }
        });
        
        // Create the chart
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Income (USD)',
                        data: incomeUSD,
                        backgroundColor: 'rgba(40, 167, 69, 0.5)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Expense (USD)',
                        data: expenseUSD,
                        backgroundColor: 'rgba(220, 53, 69, 0.5)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                    // Uncomment if you want to display KHR data as well
                    // {
                    //     label: 'Income (KHR)',
                    //     data: incomeKHR,
                    //     backgroundColor: 'rgba(23, 162, 184, 0.5)',
                    //     borderColor: 'rgba(23, 162, 184, 1)',
                    //     borderWidth: 1
                    // },
                    // {
                    //     label: 'Expense (KHR)',
                    //     data: expenseKHR,
                    //     backgroundColor: 'rgba(255, 193, 7, 0.5)',
                    //     borderColor: 'rgba(255, 193, 7, 1)',
                    //     borderWidth: 1
                    // }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Initialize Firebase
        const firebaseConfig = {
            apiKey: "AIzaSyBaxepK8F4o8XiWIB3xPdwZ6JCjZSzT4Os",
            authDomain: "popcornnews-31b43.firebaseapp.com",
            databaseURL: "https://popcornnews-31b43-default-rtdb.firebaseio.com",
            projectId: "popcornnews-31b43",
            storageBucket: "popcornnews-31b43.appspot.com",
            messagingSenderId: "982166447765",
            appId: "1:982166447765:web:f2c7b44e99a9992e9ca49e"
        };
        
        // Initialize Firebase
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
        
        // Update user status as online
        updateUserStatus();
        
        // Set up a heartbeat to periodically update the online status
        setInterval(updateUserStatus, 60000); // Every minute
        
        // Update online status on page visibility changes
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                updateUserStatus();
            }
        });
        
        // Function to update user online status
        function updateUserStatus() {
            fetch('{{ route("update.online.status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .catch(error => console.error('Error updating online status:', error));
        }
        
        // Listen for online users changes
        const onlineUsersRef = firebase.database().ref('users_online');
        onlineUsersRef.on('value', function(snapshot) {
            // This will auto-refresh when Firebase data changes
            // You can add code here to update the UI without a full page reload
            // For simplicity, we're not implementing real-time updates in this example
        });
    });
</script>
@endpush
@endsection