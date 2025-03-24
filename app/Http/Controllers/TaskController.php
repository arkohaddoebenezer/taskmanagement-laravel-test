<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): Response|ResponseFactory
    {
        $prop_tasks = Task::when($request->project_id, function ($query) use ($request) {
            $query->where('project_id', $request->project_id);
        })->orderBy('priority')->get();

        $prop_projects = Project::all();
        $selected_project_id = $request->project_id ?? null;

        return Inertia::render('Task/Index', compact('prop_tasks', 'prop_projects', 'selected_project_id'));
    }

    /**
     * Store a new task.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());

        return to_route('tasks.index', ['project_id' => $task->project_id], 303);
    }

    /**
     * Update an existing task.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return to_route('tasks.index', ['project_id' => $task->project_id], 303);
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return to_route('tasks.index', ['project_id' => $task->project_id], 303);
    }

    /**
     * Delete all tasks.
     */
    public function destroyAll()
    {
        Task::truncate();

        return to_route('tasks.index', 303);
    }

    /**
     * Reorder tasks based on the new order.
     */
    public function order(Request $request)
    {
        $newOrder = $request->input('new_order');

        foreach ($newOrder as $index => $taskId) {
            Task::where('id', $taskId)->update(['priority' => $index + 1]);
        }

        return to_route('tasks.index', ['project_id' => $request->project_id]);
    }
}
