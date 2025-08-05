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
        $tasks = Task::all()->groupBy('list_title');
        return view('todoapp.todoapp', compact('tasks'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'discreaption' => 'required',
            'list_title' => 'required',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        // یافتن تسکی که دسکریپشنش برابر با list_title باشه
        $possibleParent = Task::where('discreaption', $request->list_title)->first();

        $task = Task::create([
            'discreaption' => $request->discreaption,
            'list_title' => $request->list_title,
            'progress' => $request->progress,
            'complete' => $request->progress == 100 ? 1 : 0,
            'parent_id' => $possibleParent?->id, // اگر یافت نشد null میشه
        ]);

        $this->updateParentProgress($task);
        $this->updateChildrenProgress($task);

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
        $this->updateChildrenProgress($task);

        return redirect()->route('todo.index');
    }

    public function complete(Task $task, Request $request)
    {
        $task->update(['complete' => 1]);
        return redirect()->route('todo.index');
    }

    private function updateParentProgress(Task $task)
    {
        if ($task->parent) {
            $children = $task->parent->children;
            $total = $children->count();
            if ($total > 0) {
                $avgProgress = (int) round($children->avg('progress'));
                $complete = $avgProgress === 100 ? 1 : 0;
                $task->parent->update([
                    'progress' => $avgProgress,
                    'complete' => $complete,
                ]);
                $this->updateParentProgress($task->parent);
            }
        }
    }

    private function updateChildrenProgress(Task $task)
    {
        if ($task->children->count() > 0) {
            foreach ($task->children as $child) {
                $child->update([
                    'progress' => $task->progress,
                    'complete' => $task->progress === 100 ? 1 : 0,
                ]);
                $this->updateChildrenProgress($child);
            }
        }
    }

    
}
