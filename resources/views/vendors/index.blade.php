<x-app-layout>
    <h1 class="h3 mb-3 text-gray-800">Vendor List</h1>

    <a href="{{ route('vendors.create') }}" class="btn btn-primary mb-3">+ Add Vendor</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Vendor ID</th>
                <th>Vendor Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendors as $vendor)
                <tr>
                    <td>{{ $vendor->id }}</td>
                    <td>{{ $vendor->vendorName }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-app-layout>

