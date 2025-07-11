<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function getParentEvents($parentId)
    {
        $events = DB::table('events')
            ->join('eventparticipants', 'events.event_id', '=', 'eventparticipants.event_id')
            ->join('parentstudentlink', 'eventparticipants.student_id', '=', 'parentstudentlink.student_id')
            ->where('parentstudentlink.parent_id', $parentId)
            ->select(
                'events.event_id',
                'events.title',
                'events.description',
                'events.date',
                'events.time',
                'events.location',
                'events.cost',
                'events.scope',
                'events.created_at',
                'eventparticipants.student_id'
            )
            ->orderBy('events.date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }

    public function getStudentEvents($studentId)
    {
        $events = DB::table('events')
            ->join('eventparticipants', 'events.event_id', '=', 'eventparticipants.event_id')
            ->where('eventparticipants.student_id', $studentId)
            ->select(
                'events.event_id',
                'events.title',
                'events.description',
                'events.date',
                'events.time',
                'events.location',
                'events.cost',
                'events.scope',
                'events.created_at'
            )
            ->orderBy('events.date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }

    public function getEvent($eventId)
    {
        $event = DB::table('events')
            ->where('event_id', $eventId)
            ->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'event' => $event
        ]);
    }

    public function participateInEvent(Request $request, $eventId)
    {
        $request->validate([
            'student_id' => 'required|integer'
        ]);

        // Check if already participating
        $exists = DB::table('eventparticipants')
            ->where('event_id', $eventId)
            ->where('student_id', $request->student_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Student already participating in this event'
            ], 400);
        }

        $inserted = DB::table('eventparticipants')->insert([
            'event_id' => $eventId,
            'student_id' => $request->student_id
        ]);

        if ($inserted) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully registered for event'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to register for event'
        ], 500);
    }
}
