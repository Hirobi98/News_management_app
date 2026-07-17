@extends('layouts.app')

@section('content')
<div class="auth-card" style="max-width: 800px; padding: 40px; margin: 40px auto; text-align: left;">
    <h1 style="font-family: var(--font-serif); font-size: 2rem; text-transform: uppercase; border-bottom: 2px solid var(--primary-color); padding-bottom: 15px; margin-bottom: 30px;">
        Edit Dispatch
    </h1>

    <form action="{{ url('/news/' . $news['id'] . '/update') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Headline</label>
            <input type="text" name="title" class="form-control" value="{{ $news['title'] }}" required style="font-weight: 700; font-size: 1.2rem;">
        </div>
        <div class="form-group" style="display: flex; gap: 10px;">
            <div style="flex: 1;">
                <label>Category</label>
                <select name="category" class="form-control" required style="font-family: var(--font-sans); text-transform: uppercase;">
                    <option value="World" {{ $news['category'] == 'World' ? 'selected' : '' }}>World</option>
                    <option value="Opinion" {{ $news['category'] == 'Opinion' ? 'selected' : '' }}>Opinion</option>
                    <option value="Culture" {{ $news['category'] == 'Culture' ? 'selected' : '' }}>Culture</option>
                    <option value="Sports" {{ $news['category'] == 'Sports' ? 'selected' : '' }}>Sports</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label>Target News Channel</label>
                <select name="target_channel" class="form-control" required style="font-family: var(--font-sans); text-transform: uppercase;">
                    <option value="">Select News Channel</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel->id ?? $channel->ID }}" {{ $news['target_channel'] == ($channel->id ?? $channel->ID) ? 'selected' : '' }}>{{ $channel->name ?? $channel->NAME }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Story Content</label>
            <textarea name="content" class="form-control" rows="8" required style="font-style: italic; line-height: 1.6;">{{ $news['content'] }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Resubmit Dispatch</button>
    </form>
</div>
@endsection
