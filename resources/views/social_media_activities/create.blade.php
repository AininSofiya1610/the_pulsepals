@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Record Social Media Activity</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('social-media-activities.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="platform">Platform</label>
                    <select class="form-control" id="platform" name="platform" required>
                        <option value="Facebook">Facebook</option>
                        <option value="Instagram">Instagram</option>
                        <option value="Twitter">Twitter</option>
                        <option value="LinkedIn">LinkedIn</option>
                        <option value="TikTok">TikTok</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="activity_type">Activity Type</label>
                    <input type="text" class="form-control" id="activity_type" name="activity_type" placeholder="e.g., Post, Story, Reel" required>
                </div>
                <div class="form-group">
                    <label for="likes">Likes</label>
                    <input type="number" class="form-control" id="likes" name="likes" required>
                </div>
                <div class="form-group">
                    <label for="comments">Comments</label>
                    <input type="number" class="form-control" id="comments" name="comments" required>
                </div>
                <div class="form-group">
                    <label for="shares">Shares</label>
                    <input type="number" class="form-control" id="shares" name="shares" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('social-media-activities.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

</div>
@endsection
