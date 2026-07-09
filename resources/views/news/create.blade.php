@extends('layouts.app')

@section('content')
<div class="glass" style="max-width: 700px; margin: 40px auto; border-top: 4px solid var(--primary-color);">
    <h1 style="font-family: var(--font-serif); font-size: 2.2rem; margin-top: 0; margin-bottom: 8px;">Draft a Dispatch</h1>
    <p style="color: var(--text-secondary); margin-bottom: 30px; font-size: 0.95rem; font-style: italic;">
        Submit your article to the editorial board. Please ensure all dispatches adhere strictly to the community guidelines.
    </p>

    <form action="{{ url('/news') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Article Headline</label>
            <input type="text" id="title" name="title" class="form-control" required placeholder="Enter a compelling headline...">
        </div>

        <div class="form-group">
            <label for="channel">Editorial Department</label>
            <select id="channel" name="channel" class="form-control" required>
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
            <label for="content">Dispatch Content</label>
            <textarea id="content" name="content" class="form-control" rows="12" required placeholder="Write your news article here..."></textarea>
        </div>

        <div class="flex gap-2" style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-feather"></i> Publish Dispatch</button>
            <a href="{{ url('/home') }}" class="btn btn-secondary" style="text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>
@endsection
