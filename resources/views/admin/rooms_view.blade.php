@include('admin.include.header')

@include('admin.include.sidebar')

@include('admin.include.searchbar')

@include('sweetalert::alert')


<div class="container mt-4">
    <h2 class="mb-4">Room List</h2>

    <!-- Search and Filter Controls -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="search" class="form-control" placeholder="Search by room number, price or status">
        </div>
        <div class="col-md-3">
            <select id="filter_type" class="form-control">
                <option value="">All Types</option>
                <option value="Single">Single</option>
                <option value="Double">Double</option>
                <option value="Deluxe">Deluxe</option>
                <option value="Suite">Suite</option>
            </select>
        </div>
    </div>

    <!-- Dynamic Room Table -->
    <div id="room_table">
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
                @php $i = 1; @endphp
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
    </div>
</div>

<a href="{{ route('add_rooms') }}">
    <button type="button" class="btn btn-outline-primary position-fixed bottom-0 start-50 translate-middle-x m-3">
        <i class="fa fa-plus"></i> Add New Room
    </button>
</a>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

@include('admin.include.logout')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(roomId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This room will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/delete-room/${roomId}`;
            }
        });
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const search = document.getElementById("search");
        const filterType = document.getElementById("filter_type");

        function fetchRooms(page = 1) {
            const query = search.value;
            const type = filterType.value;

            fetch(`{{ route('filter_rooms') }}?search=${query}&type=${type}&page=${page}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById("room_table").innerHTML = html;
                });
        }

        search.addEventListener("input", debounce(fetchRooms, 300));
        filterType.addEventListener("change", fetchRooms);

        document.addEventListener('click', function (e) {
            if (e.target.closest('.pagination a')) {
                e.preventDefault();
                const page = e.target.closest('a').getAttribute('href').split('page=')[1];
                fetchRooms(page);
            }
        });

        function debounce(func, delay) {
            let timeout;
            return function () {
                clearTimeout(timeout);
                timeout = setTimeout(func, delay);
            };
        }
    });
</script>


@include('admin.include.footer')