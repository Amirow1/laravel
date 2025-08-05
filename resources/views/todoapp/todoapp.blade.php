@extends('layout.app')
@section('title', "To Do App")

@section('content')

@foreach ($tasks as $listTitle => $listTasks)
    <h2 id="title-list">{{ $listTitle }}</h2>
    <ol>
        @foreach ($listTasks as $task)
            <li @if ($task->complete) style="color: green;" @endif>

                @foreach ($tasks as $listTitle => $listTasks)
                    <h2 id="title-list">{{ $listTitle }}</h2>

                    {{-- 🔧 محاسبه پیشرفت کل لیست --}}
                    @php
                        $avgProgress = round($listTasks->avg('progress'));
                    @endphp
                    <div id="prsbar">
                        <div id="prsbar2" style="width: {{ $avgProgress }}%;">
                            {{ $avgProgress }}%
                        </div>
                    </div>

                    <ol>
                        @foreach ($listTasks as $task)
                            ...
                        @endforeach
                    </ol>
                @endforeach

                {{-- Edit Form --}}
                <form class="edit-form" method="POST" action="{{ route('todo.update', $task) }}">
                    @csrf
                    @method('PUT')
                    <input class="inp-edit" type="text" name="discreaption" value="{{ $task->discreaption }}">
                    <input class="inp-edit" type="number" name="progress" value="{{ $task->progress }}" min="0" max="100" step="1">
                    <button class="btn-down" type="submit">Edit</button>
                </form>

                {{-- Delete --}}
                <form action="{{ route('todo.destroy', $task->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn-down" type="submit">Delete</button>
                </form>

                {{-- Complete --}}
                @if (is_object($task) && !$task->complete)
                    <form action="{{ route('todo.complete', $task) }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="btn-down" type="submit">Complete</button>
                    </form>
                @elseif (is_object($task) && $task->complete)
                    ✅ Completed
                @endif


                <hr>
            </li>
        @endforeach
    </ol>
@endforeach

<hr>

{{-- Add New Task --}}
@if ($errors->any())
    <ul style="color: red;">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('todo.store') }}">
    @csrf
    <div>
        <input  class="btn-down" type="text" name="discreaption" placeholder="Task Description">
        <input  class="btn-down" type="text" name="list_title" placeholder="List Title">
        <input  class="btn-down" type="number" name="progress" placeholder="Progress %" min="0" max="100">
        <input class="btn-down" type="submit" value="Add Task">
        @error('discreaption')
            {{ $message }}
        @enderror
    </div>
</form>

@endsection
