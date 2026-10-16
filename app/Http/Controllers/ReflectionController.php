<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\LearningReflection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReflectionController extends Controller
{
    /**
     * Store 3-state reflection response:
     * 1. UNDERSTOOD (Awards XP)
     * 2. NEED_RETURN (Adds to revision queue)
     * 3. NOT_UNDERSTOOD (Flags topic for teacher assistance)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'module_id' => ['nullable', 'exists:course_modules,id'],
            'reflection_choice' => ['required', 'in:UNDERSTOOD,NEED_RETURN,NOT_UNDERSTOOD'],
            'watch_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'student_comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = Auth::user();
        $choice = $validated['reflection_choice'];
        $watchPercentage = (float) ($validated['watch_percentage'] ?? 100.0);

        // For video/audio content, require 90% watch time before reflection
        if ($validated['module_id'] ?? null) {
            $module = CourseModule::find($validated['module_id']);
            if ($module && in_array($module->module_type, ['VIDEO', 'AUDIO', 'H5P']) && $watchPercentage < 90.0) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must complete at least 90% of the media lecture before submitting a learning reflection.',
                ], 422);
            }
        } elseif ($watchPercentage < 90.0 && ($request->input('is_media') || $request->input('resource_type') === 'video')) {
            return response()->json([
                'success' => false,
                'message' => 'You must complete at least 90% of the media lecture before submitting a learning reflection.',
            ], 422);
        }

        $isFlagged = ($choice === LearningReflection::CHOICE_NOT_UNDERSTOOD);

        $reflection = LearningReflection::create([
            'user_id' => $user->id,
            'course_id' => $validated['course_id'],
            'module_id' => $validated['module_id'] ?? null,
            'reflection_choice' => $choice,
            'watch_percentage' => $watchPercentage,
            'is_flagged_for_teacher' => $isFlagged,
            'student_comment' => $validated['student_comment'] ?? null,
        ]);

        $xpAwarded = 0;
        if ($choice === LearningReflection::CHOICE_UNDERSTOOD) {
            $idempotencyKey = 'reflection_' . $user->id . '_' . ($validated['module_id'] ?? $validated['course_id']);
            
            // Avoid duplicate XP award for the same entity
            $existingXp = \Illuminate\Support\Facades\DB::table('user_xp_ledger')
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if (!$existingXp) {
                \Illuminate\Support\Facades\DB::table('user_xp_ledger')->insert([
                    'user_id' => $user->id,
                    'xp_amount' => 50,
                    'activity_type' => 'REFLECTION_UNDERSTOOD',
                    'idempotency_key' => $idempotencyKey,
                    'description' => 'Completed and mastered learning topic reflection',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $xpAwarded = 50;
            }
        }

        return response()->json([
            'success' => true,
            'reflection' => $reflection,
            'xp_awarded' => $xpAwarded,
            'message' => match ($choice) {
                LearningReflection::CHOICE_UNDERSTOOD => 'Topic mastered! +50 XP awarded to your revision profile.',
                LearningReflection::CHOICE_NEED_RETURN => 'Topic added to your active revision queue for further practice.',
                LearningReflection::CHOICE_NOT_UNDERSTOOD => 'Topic flagged. Your tutor/teacher has been notified for assistance.',
            },
        ]);
    }
}
