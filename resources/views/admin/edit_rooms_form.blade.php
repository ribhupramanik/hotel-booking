@include('admin.include.header')
@include('admin.include.sidebar')
@include('admin.include.searchbar')
@include('sweetalert::alert')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="list-style-type: none;">
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container mt-4" style="max-width: 700px;">
    <h1 class="mb-4 text-center">EDIT ROOM DETAILS</h1>

    <form method="post" action="{{ route('update_room', ['id' => $room->id]) }}">
        @csrf
        @method('PUT')

        <input type="hidden" name="id" value="{{ $room->id }}">

        <div class="form-group mb-3">
            <label for="room_number">Enter Room Number</label>
            <input type="number" class="form-control" name="room_number" id="room_number"
                value="{{ $room->room_number }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="room_type">Room Type</label>
            <select name="room_type" class="form-control" id="room_type" required>
                <option value="">-- Select Type --</option>
                <option value="Single" {{ $room->type == 'Single' ? 'selected' : '' }}>Single</option>
                <option value="Double" {{ $room->type == 'Double' ? 'selected' : '' }}>Double</option>
                <option value="Deluxe" {{ $room->type == 'Deluxe' ? 'selected' : '' }}>Deluxe</option>
                <option value="Suite" {{ $room->type == 'Suite' ? 'selected' : '' }}>Suite</option>
            </select>
        </div>

        <div class="form-group mb-4">
            <label for="price">Price</label>
            <input type="text" class="form-control" name="price" id="price" value="{{ $room->price }}" required>
        </div>

        <div class="form-group d-flex justify-content-between">
            <button type="submit" class="btn btn-outline-primary">Update Room</button>
            <a href="{{ route('rooms') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- Scroll to Top Button -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

@include('admin.include.logout')
@include('admin.include.footer')