<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
@include('admin.include.header')
@include('admin.include.sidebar')
@include('admin.include.searchbar')
@include('sweetalert::alert')

<div class="container-fluid mt-4 col-md-4">
    <h4 class="mb-4">Book a Room</h4>

    <form id="bookingForm">
        @csrf

        {{-- Select Room --}}
        <div class="form-group">
            <label for="room_id">Room</label>
            <select name="room_id" id="room_id" class="form-control" required>
                <option value="">-- Select Check-in and Check-out First --</option>
            </select>
        </div>

        {{-- Check-in --}}
        <div class="form-group">
            <label for="date_range">Select Booking Dates</label>
            <input type="text" id="date_range" class="form-control" required>
            <input type="hidden" name="check_in_date" id="check_in_date">
            <input type="hidden" name="check_out_date" id="check_out_date">
        </div>


        <button type="submit" class="btn btn-primary mt-3">Book Room</button>
    </form>
</div>

<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
@include('admin.include.logout')
@include('admin.include.footer')

{{-- AJAX and SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('bookingForm');
        const dateRangeInput = document.getElementById('date_range');
        const checkInInput = document.getElementById('check_in_date');
        const checkOutInput = document.getElementById('check_out_date');
        const roomSelect = document.getElementById('room_id');

        // ✅ Initialize Litepicker
        const picker = new Litepicker({
            element: dateRangeInput,
            singleMode: false,
            format: 'YYYY-MM-DD',
            minDate: new Date().toISOString().split('T')[0],
            setup: (picker) => {
                picker.on('selected', (start, end) => {
                    checkInInput.value = start.format('YYYY-MM-DD');
                    checkOutInput.value = end.format('YYYY-MM-DD');
                    fetchAvailableRooms(); // Refresh available rooms
                });
            }
        });

        // ✅ Fetch available rooms
        function fetchAvailableRooms() {
            const checkIn = checkInInput.value;
            const checkOut = checkOutInput.value;

            if (!checkIn || !checkOut) return;

            fetch(`{{ route('available_rooms') }}?check_in=${checkIn}&check_out=${checkOut}`)
                .then(res => res.json())
                .then(data => {
                    roomSelect.innerHTML = '<option value="">-- Select Room --</option>';
                    if (data.rooms.length === 0) {
                        roomSelect.innerHTML = '<option value="">No rooms available</option>';
                    } else {
                        data.rooms.forEach(room => {
                            const option = document.createElement('option');
                            option.value = room.id;
                            option.text = `Room ${room.room_number} - ${room.type} (₹${room.price})`;
                            roomSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Room fetch error:', error);
                });
        }

        // ✅ Form submit
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            const csrfToken = document.querySelector('input[name="_token"]').value;

            fetch("{{ route('add_bookings_submit') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: data.status === 'success' ? 'success' : 'error',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000
                    });

                    if (data.status === 'success') {
                        form.reset();
                        dateRangeInput.value = '';
                        roomSelect.innerHTML = '<option value="">-- Select Room --</option>';
                    }
                })
                .catch(error => {
                    console.error('Booking error:', error);
                });
        });
    });
</script>