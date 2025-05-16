<table class="table table-bordered text-center">
    <thead class="table-dark">
        <tr>
            <th>Sl.No</th>
            <th>User Name</th>
            <th>Room Number</th>
            <th>Room Type</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @php $i = ($bookings->currentPage() - 1) * $bookings->perPage() + 1; @endphp
        @foreach ($bookings as $booking)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $booking->user->name ?? 'N/A' }}</td>
                <td>{{ $booking->room->room_number ?? 'N/A' }}</td>
                <td>{{ $booking->room->type ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</td>
                <td>
                    <span class="badge bg-{{ 
                                $booking->status === 'confirmed' ? 'success' :
            ($booking->status === 'pending' ? 'warning' :
                ($booking->status === 'cancelled' ? 'danger' : 'secondary')) 
                                }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>
                <td>
                    <button onclick="confirmDelete({{ $booking->id }})" class="btn btn-sm btn-danger">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-center mt-3">
    {{ $bookings->links() }}
</div>