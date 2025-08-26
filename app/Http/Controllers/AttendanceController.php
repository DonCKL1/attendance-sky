<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    // Show the main page
    public function index()
    {
        return view('index');
    }

    // Fetch today's attendance records (AJAX)
    public function getAttendance()
    {
        try {
            $today = now()->format('Y-m-d');
            $records = Attendance::whereDate('sign_in_time', $today)
                        ->orderBy('sign_in_time', 'desc')
                        ->get();

            return response()->json($records);
        } catch (\Exception $e) {
            Log::error('Error fetching attendance: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch records'], 500);
        }
    }

    // Store new attendance (AJAX)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $today = now()->format('Y-m-d');

            // Check if the person already signed in today
            $existing = Attendance::where('name', $request->name)
                                ->whereDate('sign_in_time', $today)
                                ->first();

            if ($existing) {
                return response()->json(['error' => 'You have already signed in today.'], 409);
            }

            $attendance = Attendance::create([
                'name' => $request->name,
                'sign_in_time' => now()
            ]);

            return response()->json($attendance);
        } catch (\Exception $e) {
            \Log::error('Error saving attendance: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save record'], 500);
        }
    }


    // Export attendance to PDF
    public function export(Request $request)
    {
        try {
            $request->validate([
                'date' => 'nullable|date',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date'
            ]);

            $query = Attendance::query();

            if ($request->has('date')) {
                $query->whereDate('sign_in_time', $request->date);
            }

            if ($request->has(['start_date', 'end_date'])) {
                $query->whereBetween('sign_in_time', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            $records = $query->orderBy('sign_in_time', 'desc')->get();

            // Generate PDF
            $pdf = Pdf::loadView('attendance.pdf', [
                'records' => $records,
                'start_date' => $request->start_date ?? null,
                'end_date' => $request->end_date ?? null,
                'date' => $request->date ?? null,
            ]);

            $fileName = 'attendance_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error exporting attendance: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to export records'], 500);
        }
    }
}
