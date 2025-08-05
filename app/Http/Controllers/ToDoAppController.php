<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ToDoAppController extends Controller
{
    protected   $rules = [
            'discreaption' => 'required'
        ];
    public function index()
    {
        $tasks = Task::with('children')->get();

        return view('index', compact('tasks'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'discreaption' => 'required',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $task = Task::create([
            'discreaption' => $request->discreaption,
            'list_title' => $request->list_title,
            'progress' => $request->progress,
            'complete' => $request->progress == 100 ? 1 : 0,
            'parent_id' => $request->parent_id,
        ]);

        $this->updateParentProgress($task);

        return redirect()->route('todo.index');
    }


    public function destroy($taskId) 
    {
        $task = Task::find($taskId);

        if ($task) 
            $task->delete();

        return redirect()->route('todo.index');
    }

    public function update(Task $task, Request $request) 
    {
        $validated = $request->validate([
            'discreaption' => 'required',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $validated['complete'] = $validated['progress'] === 100 ? 1 : 0;

        $task->update($validated);

        $this->updateParentProgress($task);

        return redirect()->route('todo.index');
    }

    public function complete(Task $task, Request $request)
    {
        $task->update(['complete' => 1]);
        return redirect()->route('todo.index');
    }

    private function updateParentProgress(Task $task)
    {
        if ($task->parent_id) {
            $parent = Task::with('children')->find($task->parent_id); // والد و فرزندانش را از DB لود کن

            if ($parent && $parent->children->count() > 0) {
                $avgProgress = (int) round($parent->children->avg('progress'));
                $complete = $avgProgress === 100 ? 1 : 0;

                $parent->update([
                    'progress' => $avgProgress,
                    'complete' => $complete,
                ]);

                // بازگشتی برای به‌روزرسانی والد والد
                $this->updateParentProgress($parent);
            }
        }
    }    
}
