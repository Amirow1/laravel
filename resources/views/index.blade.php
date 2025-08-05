@extends('layout.app')
@section('title', "To Do App")

@section('content')

@php
    function renderTasks($tasks)
    {
        echo '<ol>';
        foreach ($tasks as $task) {
            echo '<li' . ($task->complete ? ' style="color: green;"' : '') . '>';
            echo htmlspecialchars($task->discreaption) . ' - ' . $task->progress . '%';

            // فرم و دکمه‌ها
            echo '<form class="edit-form" method="POST" action="' . route('todo.update', $task) . '">';
            echo csrf_field();
            echo method_field('PUT');
            echo '<input class="inp-edit" type="text" name="discreaption" value="' . htmlspecialchars($task->discreaption) . '">';
            echo '<input class="inp-edit" type="number" name="progress" value="' . $task->progress . '" min="0" max="100" step="1">';
            echo '<button class="btn-down" type="submit">Edit</button>';
            echo '</form>';

            echo '<form action="' . route('todo.destroy', $task->id) . '" method="POST" style="display:inline;">';
            echo csrf_field();
            echo method_field('DELETE');
            echo '<button class="btn-down" type="submit">Delete</button>';
            echo '</form>';

            if (!$task->complete) {
                echo '<form action="' . route('todo.complete', $task) . '" method="POST" style="display:inline;">';
                echo csrf_field();
                echo '<button class="btn-down" type="submit">Complete</button>';
                echo '</form>';
            } else {
                echo '✅ Completed';
            }

            // نمایش فرزندان (بازگشتی)
            if ($task->children->count() > 0) {
                renderTasks($task->children);
            }

            echo '</li>';
        }
        echo '</ol>';
    }
    @endphp

    {{-- نمایش ریشه‌ها (تسک‌هایی که والد ندارند) --}}
    @php
    $rootTasks = $tasks->whereNull('parent_id');
    renderTasks($rootTasks);
@endphp


<hr>

{{-- فرم افزودن تسک جدید --}}
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
            <input class="btn-down" type="text" name="discreaption" placeholder="Task Description" value="{{ old('discreaption') }}">
            <input class="btn-down" type="number" name="progress" placeholder="Progress %" min="0" max="100" value="{{ old('progress') }}">
            <select name="parent_id">
                <option value="">بدون والد</option>
                @foreach ($tasks->flatten() as $t)
                    <option value="{{ $t->id }}" @if(old('parent_id') == $t->id) selected @endif>{{ $t->discreaption }}</option>
                @endforeach
            </select>
            <input class="btn-down" type="submit" value="Add Task">
            @error('discreaption')
                <div style="color:red;">{{ $message }}</div>
            @enderror
        </div>
    </form>

@endsection
