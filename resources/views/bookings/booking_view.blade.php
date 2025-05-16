@include('admin.include.header')
@include('admin.include.sidebar')
@include('admin.include.searchbar')
@include('sweetalert::alert')

<div class="container mt-4">
    <h2 class="mb-4">Booking List</h2>

    <!-- Search and Filter Controls -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="search" class="form-control"
                placeholder="Search by user name, room number or status">
        </div>
        <div class="col-md-3">
            <select id="filter_status" class="form-control">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Dynamic Booking Table -->
    <div id="booking_table">
        @include('bookings.booking_table', ['bookings' => $bookings])
    </div>

    <a href="{{ route('add_bookings') }}">
        <button type="button" class="btn btn-outline-primary position-fixed bottom-0 start-50 translate-middle-x m-3">
            <i class="fa fa-plus"></i> Add New Booking
        </button>
    </a>

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    @include('admin.include.logout')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(bookingId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This booking will be permanently cancelled.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/delete-booking/${bookingId}`;
                }
            });
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const search = document.getElementById("search");
            const filterStatus = document.getElementById("filter_status");

            function fetchBookings(page = 1) {
                const query = search.value;
                const status = filterStatus.value;

                const url = `/filter-bookings?search=${query}&status=${status}&page=${page}`;
                console.log("Fetching URL:", url);

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.text();
                    })
                    .then(html => {
                        document.getElementById("booking_table").innerHTML = html;
                    })
                    .catch(error => {
                        console.error("Fetch error:", error);
                    });
            }

            search.addEventListener("input", debounce(fetchBookings, 300));
            filterStatus.addEventListener("change", () => fetchBookings(1)); // reset to first page

            document.addEventListener('click', function (e) {
                const paginationLink = e.target.closest('.pagination a');
                if (paginationLink) {
                    e.preventDefault();
                    const url = new URL(paginationLink.href);
                    const page = url.searchParams.get("page");
                    fetchBookings(page);
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