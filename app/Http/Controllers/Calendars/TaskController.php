<?php

namespace App\Http\Controllers\Calendars;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Task;

class TaskController extends Controller
{
    public function store(Request $request)
        {
            $validated = $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'title' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'due_date' => 'required|date',
            ]);

            $user = auth()->user(); 

            $task = Task::create([
                'lead_id' => $validated['lead_id'],
                'title'   => $validated['title'],
                'type'    => $validated['type'],
                'due_date'=> $validated['due_date'],
                'user_id' => $user->id, 
            ]);

            $lead = Lead::find($validated['lead_id']);
            $tasks = $lead->tasks ?? [];

            $tasks[] = [
                'id'         => $task->id,
                'title'      => $validated['title'],
                'type'       => $validated['type'],
                'due_date'   => $validated['due_date'],
                'user_id'    => $user->id,
                'created_at' => now()->toDateTimeString(),
                'status'     => 'pending',
            ];

            $lead->tasks = $tasks;
            $lead->save();

            return response()->json([
                'task' => $task,
                'lead' => $lead
            ], 201);
        }

    }
