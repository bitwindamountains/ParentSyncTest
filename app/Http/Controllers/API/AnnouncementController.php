<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function getParentAnnouncements($parentId)
    {
        $announcements = DB::table('announcements')
            ->join('announcementrecipients', 'announcements.announcement_id', '=', 'announcementrecipients.announcement_id')
            ->join('parentstudentlink', 'announcementrecipients.student_id', '=', 'parentstudentlink.student_id')
            ->where('parentstudentlink.parent_id', $parentId)
            ->select(
                'announcements.announcement_id',
                'announcements.title',
                'announcements.content',
                'announcements.scope',
                'announcements.created_at',
                'announcementrecipients.student_id'
            )
            ->orderBy('announcements.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'announcements' => $announcements
        ]);
    }

    public function getStudentAnnouncements($studentId)
    {
        $announcements = DB::table('announcements')
            ->join('announcementrecipients', 'announcements.announcement_id', '=', 'announcementrecipients.announcement_id')
            ->where('announcementrecipients.student_id', $studentId)
            ->select(
                'announcements.announcement_id',
                'announcements.title',
                'announcements.content',
                'announcements.scope',
                'announcements.created_at'
            )
            ->orderBy('announcements.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'announcements' => $announcements
        ]);
    }

    public function getAnnouncement($announcementId)
    {
        $announcement = DB::table('announcements')
            ->where('announcement_id', $announcementId)
            ->first();

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'announcement' => $announcement
        ]);
    }
}
