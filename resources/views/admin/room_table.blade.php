<table class="table table-bordered text-center">
    <thead class="table-dark">
        <tr>
            <th>Sl.No</th>
            <th>Room Number</th>
            <th>Type</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @php $i = ($rooms->currentPage() - 1) * $rooms->perPage() + 1; @endphp
        @foreach ($rooms as $room)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $room->room_number }}</td>
                <td>{{ $room->type }}</td>
                <td>₹{{ number_format($room->price, 2) }}</td>
                <td>
                    <span class="badge bg-{{ $room->status === 'available' ? 'success' : 'secondary' }}">
                        {{ ucfirst($room->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('edit_room', ['id' => $room->id]) }}" class="btn btn-success btn-sm">Edit</a>
                    <button onclick="confirmDelete({{ $room->id }})" class="btn btn-danger btn-sm">Delete</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-center mt-3">
    {{ $rooms->links() }}
</div>