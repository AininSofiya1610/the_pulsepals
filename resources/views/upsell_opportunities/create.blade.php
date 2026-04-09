@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Record New Upsell</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('upsell-opportunities.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="customer_name">Customer Name</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                </div>
                <div class="form-group">
                    <label for="item_bought">What they bought extra</label>
                    <input type="text" class="form-control" id="item_bought" name="item_bought" required>
                </div>
                <div class="form-group">
                    <label for="amount">How much (RM)</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('upsell-opportunities.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

</div>
@endsection
