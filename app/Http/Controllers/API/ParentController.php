<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{
    public function getChildren($parentId)
    {
        $children = DB::table('parents')
            ->join('parentstudentlink', 'parents.parent_id', '=', 'parentstudentlink.parent_id')
            ->join('students', 'parentstudentlink.student_id', '=', 'students.student_id')
            ->join('sections', 'students.section_id', '=', 'sections.section_id')
            ->join('grades', 'sections.grade_id', '=', 'grades.grade_id')
            ->where('parents.parent_id', $parentId)
            ->select(
                'students.student_id',
                'students.first_name',
                'students.last_name',
                'students.birthdate',
                'students.grade_level',
                'sections.section_name',
                'grades.grade_level as grade_name'
            )
            ->get();

        return response()->json([
            'success' => true,
            'children' => $children
        ]);
    }

    public function getProfile($parentId)
    {
        $parent = DB::table('parents')
            ->join('users', 'parents.user_id', '=', 'users.user_id')
            ->where('parents.parent_id', $parentId)
            ->select(
                'parents.parent_id',
                'parents.first_name',
                'parents.last_name',
                'parents.email',
                'parents.contactNo',
                'users.username'
            )
            ->first();

        if (!$parent) {
            return response()->json([
                'success' => false,
                'message' => 'Parent not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'parent' => $parent
        ]);
    }

    public function updateProfile(Request $request, $parentId)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactNo' => 'required|string|max:20'
        ]);

        $updated = DB::table('parents')
            ->where('parent_id', $parentId)
            ->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contactNo' => $request->contactNo
            ]);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update profile'
        ], 500);
    }
}
