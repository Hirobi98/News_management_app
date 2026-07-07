@extends('layouts.app')

@section('content')
<div class="glass" style="max-width: 800px; margin: 40px auto; padding: 40px;">
    <h1 style="margin-top: 0;">Write an Article</h1>
    <p style="color: var(--text-secondary); margin-bottom: 30px;">Publish your news to the world. Please adhere to the community guidelines.</p>

    <form action="{{ url('/news') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Article Title</label>
            <input type="text" id="title" name="title" class="form-control" required placeholder="Enter a catchy title...">
        </div>

        <div class="form-group">
            <label for="channel">News Channel</label>
            <select id="channel" name="channel" class="form-control" required style="appearance: none;">
                <option value="" disabled selected>Select a category...</option>
                <option value="Technology">Technology</option>
                <option value="Finance">Finance</option>
                <option value="Science">Science</option>
                <option value="Politics">Politics</option>
                <option value="Sports">Sports</option>
                <option value="Entertainment">Entertainment</option>
            </select>
        </div>

        <div class="form-group">
            <label for="content">Article Content</label>
            <textarea id="content" name="content" class="form-control" rows="10" required placeholder="Write your news article here..."></textarea>
        </div>

        <div class="flex gap-2" style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Publish News</button>
            <a href="{{ url('/home') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
