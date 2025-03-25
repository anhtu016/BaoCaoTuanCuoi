<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('user')->get();
        return response()->json(['tasks' => $tasks], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'due_date' => 'nullable|date', 
                'status' => 'required|in:pending,in_progress,completed',
                'description' => 'nullable|string|max:255',
                'assigned_to' => 'nullable|exists:users,id', // Kiểm tra tồn tại trong bảng users
            ]);

            $task = Task::create($validatedData);

            return response()->json([
                'message' => 'Công việc đã được tạo thành công.',
                'task' => $task
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi tạo công việc.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'due_date' => 'nullable|date',
                'assigned_to' => 'nullable|exists:users,id',
                'status' => 'required|in:pending,in_progress,completed',
                'description' => 'nullable|string|max:255',
            ]);

            $task->update($validatedData);

            return response()->json([
                'message' => 'Cập nhật công việc thành công.',
                'task' => $task
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi cập nhật công việc.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Task $task)
    {
        try {
            $task->delete();
            return response()->json(['message' => 'Công việc đã được xóa thành công.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi xóa công việc.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Công việc không tồn tại.'], 404);
        }
        return response()->json(['task' => $task], 200);
    }
    public function myTasks(Request $request)
{
    $userId = $request->user()->id;

    $tasks = Task::where('assigned_to', $userId)->orderBy('due_date')->get();

    return response()->json($tasks);
}

}
