@include('admin.include.header')
@include('admin.include.sidebar')
@include('admin.include.searchbar')

<div class="container mt-4">
    <h2 class="mb-4">Admin Dashboard</h2>

    <div class="row">
        <!-- Booking Trends Chart -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    Booking Trends (This Year)
                </div>
                <div class="card-body">
                    <canvas id="bookingTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Most Booked Room Type -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    Most Booked Room Type
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        @if($mostBookedRoomType)
                            {{ $mostBookedRoomType->type }} ({{ $mostBookedRoomType->count }} bookings)
                        @else
                            No bookings yet.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Upcoming vs Past Bookings -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    Upcoming vs Past Bookings
                </div>
                <div class="card-body">
                    <canvas id="upcomingPastChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Total Rooms vs Total Bookings -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    Total Rooms & Bookings
                </div>
                <div class="card-body">
                    <canvas id="totalRoomsBookingsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

@include('admin.include.logout')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Booking Trends Chart
    const bookingTrendsCtx = document.getElementById('bookingTrendsChart').getContext('2d');
    const bookingTrendsLabels = {!! json_encode($bookingTrends->pluck('month')->map(fn($m) => \Carbon\Carbon::parse($m . '-01')->format('M Y'))) !!};
    const bookingTrendsData = {!! json_encode($bookingTrends->pluck('count')) !!};

    new Chart(bookingTrendsCtx, {
        type: 'line',
        data: {
            labels: bookingTrendsLabels,
            datasets: [{
                label: 'Bookings',
                data: bookingTrendsData,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, precision: 0 } }
        }
    });

    // Upcoming vs Past Bookings
    new Chart(document.getElementById('upcomingPastChart'), {
        type: 'doughnut',
        data: {
            labels: ['Upcoming', 'Past'],
            datasets: [{
                data: [{{ $upcomingBookingsCount }}, {{ $pastBookingsCount }}],
                backgroundColor: ['#36A2EB', '#FF6384'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Total Rooms vs Bookings Chart
    new Chart(document.getElementById('totalRoomsBookingsChart'), {
        type: 'bar',
        data: {
            labels: ['Rooms', 'Bookings'],
            datasets: [{
                label: 'Count',
                data: [{{ $totalRooms }}, {{ $totalBookings }}],
                backgroundColor: ['#4BC0C0', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, precision: 0 } }
        }
    });
</script>

@include('admin.include.footer')
