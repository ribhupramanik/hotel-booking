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

<div class="container">
    <h1> ADD NEW ROOMS </h1>

    <form method="post" action="{{ route('add_room_form_submit') }}">
        @csrf

        <!-- Test Name Input -->
        <div class="form-group">
            <label for="name">Enter Room Number</label>
            <input type="number" class="form-control" name="room_number" placeholder="Room Number">
        </div>

        <div class="form-group">
            <label for="type">Room Type</label>
            <select name="room_type" class="form-control" required>
                <option value="">-- Select Type --</option>
                <option value="Single">Single</option>
                <option value="Double">Double</option>
                <option value="Deluxe">Deluxe</option>
                <option value="Suite">Suite</option>
            </select>
        </div>

        <!-- Price Input -->
        <div class="form-group">
            <label for="price">Price</label>
            <input type="text" class="form-control" name="price" placeholder="Enter Price">
        </div>


        <!-- Submit Button -->
        <button type="submit" class="btn btn-outline-success">Submit</button>
    </form>
</div>

<!-- Scroll to Top Button -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

@include('admin.include.logout')
@include('admin.include.footer')