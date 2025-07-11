<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function getStudentAttendance($studentId)
    {
        $attendance = DB::table('attendancerecords')
            ->join('teachers', 'attendancerecords.marked_by', '=', 'teachers.teacher_id')
            ->where('attendancerecords.student_id', $studentId)
            ->select(
                'attendancerecords.attendance_id',
                'attendancerecords.date',
                'attendancerecords.status',
                'teachers.first_name as teacher_first_name',
                'teachers.last_name as teacher_last_name'
            )
            ->orderBy('attendancerecords.date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'attendance' => $attendance
        ]);
    }

    public function getAttendanceSummary($studentId)
    {
        $summary = DB::table('attendancerecords')
            ->where('student_id', $studentId)
            ->select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get();

        $total = DB::table('attendancerecords')
            ->where('student_id', $studentId)
            ->count();

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'total_days' => $total
        ]);
    }
}
