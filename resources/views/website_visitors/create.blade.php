@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Record Website Visitor</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('website-visitors.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="page_visited">Page Visited</label>
                    <input type="text" class="form-control" id="page_visited" name="page_visited" placeholder="e.g., /home, /pricing" required>
                </div>
                <div class="form-group">
                    <label for="visitor_ip">Visitor IP</label>
                    <input type="text" class="form-control" id="visitor_ip" name="visitor_ip" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('website-visitors.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

</div>
@endsection
