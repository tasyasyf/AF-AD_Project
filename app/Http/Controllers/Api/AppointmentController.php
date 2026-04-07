<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * GET /api/appointments
     * List own appointments (AF/AD).
     */
    public function index(Request $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $appointments = $profile->appointments()
            ->latest('start_date')
            ->get()
            ->map(fn($a) => [
                'id'               => $a->id,
                'course_code'      => $a->course_code,
                'course_name'      => $a->course_name,
                'role_type'        => $a->role_type,
                'semester'         => $a->semester,
                'academic_session' => $a->academic_session,
                'start_date'       => $a->start_date->toDateString(),
                'end_date'         => $a->end_date->toDateString(),
                'venue'            => $a->venue,
                'student_count'    => $a->student_count,
                'is_active'        => $a->is_active,
            ]);

        return response()->json(['data' => $appointments]);
    }

    /**
     * GET /api/appointments/{id}
     * Show single appointment.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $profile = $request->user()->profile;
        $appointment = $profile?->appointments()->find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }

        return response()->json([
            'id'               => $appointment->id,
            'course_code'      => $appointment->course_code,
            'course_name'      => $appointment->course_name,
            'role_type'        => $appointment->role_type,
            'semester'         => $appointment->semester,
            'academic_session' => $appointment->academic_session,
            'start_date'       => $appointment->start_date->toDateString(),
            'end_date'         => $appointment->end_date->toDateString(),
            'venue'            => $appointment->venue,
            'student_count'    => $appointment->student_count,
            'is_active'        => $appointment->is_active,
            'notes'            => $appointment->notes,
            'claims_count'     => $appointment->claims()->count(),
        ]);
    }
}
