<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CourseAdminController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isTeacher = $user && $user->isTeacher() && !$user->isSuperAdmin();
        $levelFilter = (int) $request->get('level_id', 0);
        $search = trim((string) $request->get('search', ''));
        $statusFilter = strtoupper((string) $request->get('status', 'ALL'));

        if ($isTeacher) {
            // A teacher can ONLY see his own courses and materials
            $query = Course::where('instructor_id', $user->id)
                ->with(['category', 'sections.modules'])
                ->latest();

            // Filter categories to ONLY those the teacher registered with or admin approved
            $authorizedCategoryIds = $user->getAuthorizedCategoryIds();
            $categories = CourseCategory::whereIn('id', $authorizedCategoryIds)->orderBy('id', 'asc')->get();
            $allFilterLevels = $categories;
        } else {
            // Administrators see all courses with instructor association
            $query = Course::with(['category', 'instructor', 'sections.modules'])
                ->latest();
            $categories = CourseCategory::orderBy('id', 'asc')->get();
            $allFilterLevels = $categories;
        }

        if ($levelFilter > 0) {
            $query->where('category_id', $levelFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhereHas('instructor', fn($iq) => $iq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($statusFilter === 'PENDING') {
            $query->where('is_published', false)->where('material_approval_status', '!=', 'REJECTED');
        } elseif ($statusFilter === 'LIVE' || $statusFilter === 'PUBLISHED') {
            $query->where('is_published', true);
        } elseif ($statusFilter === 'REJECTED') {
            $query->where('material_approval_status', 'REJECTED');
        }

        $courses = $query->paginate(12)->withQueryString();
        $pendingDeletionsCount = Course::where('deletion_status', Course::DELETION_REQUESTED)->count();

        // Scope summary stats for filter tabs
        $statsBaseQuery = $isTeacher ? Course::where('instructor_id', $user->id) : Course::query();
        $pendingApprovalCount = (clone $statsBaseQuery)->where('is_published', false)->where('material_approval_status', '!=', 'REJECTED')->count();
        $liveCoursesCount = (clone $statsBaseQuery)->where('is_published', true)->count();
        $rejectedCoursesCount = (clone $statsBaseQuery)->where('material_approval_status', 'REJECTED')->count();
        $totalCoursesCount = (clone $statsBaseQuery)->count();

        return view('admin.courses.index', compact(
            'courses',
            'categories',
            'allFilterLevels',
            'levelFilter',
            'search',
            'statusFilter',
            'isTeacher',
            'pendingDeletionsCount',
            'pendingApprovalCount',
            'liveCoursesCount',
            'rejectedCoursesCount',
            'totalCoursesCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isTeacher = $user && $user->isTeacher() && !$user->isSuperAdmin();

        // Enforce: Admin must approve teacher before they can create courses or upload materials
        if ($isTeacher && !$user->isApprovedTeacher()) {
            return redirect()->route('admin.dashboard')->with('error', 'Your instructor account is currently pending administrator verification. An administrator must approve your faculty profile before you can create courses or upload learning materials.');
        }

        // If teacher is approved, verify category is among their authorized Cambridge levels
        if ($isTeacher) {
            $authorizedCategoryIds = $user->getAuthorizedCategoryIds();
            if (!empty($authorizedCategoryIds) && !in_array((int)$request->input('category_id'), $authorizedCategoryIds, true)) {
                return back()->withInput()->with('error', 'You are only authorized to upload courses and materials for your assigned Cambridge levels. You can request additional levels from your faculty dashboard.');
            }
        }

        $rules = [
            // When teacher is logged in, he MUST select a specific Cambridge level/category
            'category_id' => [$isTeacher ? 'required' : 'nullable', 'exists:course_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:courses,slug'],
            'description' => ['nullable', 'string'],
            'target_audience' => ['required', 'in:CORPORATE,PUBLIC,BOTH'],
            'format' => ['required', 'in:WEEKLY,TOPICS,SELF_PACED'],
            'passing_grade' => ['required', 'integer', 'min:1', 'max:100'],
            'recertification_days' => ['nullable', 'integer', 'min:1'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'special_pricing_tag' => ['nullable', 'string', 'max:100'],
            'currency' => ['required', 'string', 'max:3'],
            'is_published' => ['nullable', 'boolean'],
            'public_preview_content' => ['nullable', 'string'],
            'thumbnail_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'thumbnail_url' => ['nullable', 'string', 'max:500'],
            'preserved_thumbnail_file' => ['nullable', 'string'],
            'video_file' => ['nullable', 'file', 'mimes:mp4,mov,ogg,qt,webm', 'max:51200'], // Up to 50MB
            'video_url' => ['nullable', 'string', 'max:500'],
            'preserved_video_file' => ['nullable', 'string'],
            'material_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,zip,txt', 'max:51200'],
            'preserved_material_file' => ['nullable', 'string'],
            'material_notes' => ['nullable', 'string'],
        ];

        // Check for PHP upload level errors (e.g. php.ini upload_max_filesize or post_max_size exceeded on hosting)
        if ($request->hasFile('thumbnail_file')) {
            $thumbUpload = $request->file('thumbnail_file');
            if (!$thumbUpload->isValid()) {
                $errCode = $thumbUpload->getError();
                $errMsg = match ($errCode) {
                    UPLOAD_ERR_INI_SIZE => 'The uploaded thumbnail file exceeds the server\'s upload_max_filesize limit configured on this hosting server. Please select an image under 5MB or compress it.',
                    UPLOAD_ERR_FORM_SIZE => 'The thumbnail file exceeds the maximum allowed form upload size.',
                    UPLOAD_ERR_PARTIAL => 'The thumbnail file was only partially uploaded. Please check your network and try again.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Server error: missing temporary upload folder on hosting.',
                    UPLOAD_ERR_CANT_WRITE => 'Server error: failed to write uploaded thumbnail file to disk on hosting.',
                    default => 'The thumbnail file could not be uploaded (' . $thumbUpload->getErrorMessage() . '). Please verify file format and size.',
                };
                return back()->withInput()->withErrors(['thumbnail_file' => $errMsg]);
            }
        }

        if ($request->hasFile('video_file') && !$request->file('video_file')->isValid()) {
            $videoUpload = $request->file('video_file');
            $errMsg = match ($videoUpload->getError()) {
                UPLOAD_ERR_INI_SIZE => 'The uploaded video file exceeds the server\'s upload_max_filesize limit configured on this hosting server. For large video lectures, please paste the video URL or compress the video.',
                default => 'The video file failed to upload (' . $videoUpload->getErrorMessage() . ').',
            };
            return back()->withInput()->withErrors(['video_file' => $errMsg]);
        }

        if ($request->hasFile('material_file') && !$request->file('material_file')->isValid()) {
            $matUpload = $request->file('material_file');
            $errMsg = match ($matUpload->getError()) {
                UPLOAD_ERR_INI_SIZE => 'The attached material file exceeds the server\'s upload_max_filesize limit configured on this hosting server. Please compress or link the material.',
                default => 'The material file failed to upload (' . $matUpload->getErrorMessage() . ').',
            };
            return back()->withInput()->withErrors(['material_file' => $errMsg]);
        }

        // If files were uploaded in this request, pre-store them so they aren't lost if validation fails
        $preStoredThumbnail = null;
        if ($request->hasFile('thumbnail_file') && $request->file('thumbnail_file')->isValid()) {
            $preStoredThumbnail = $request->file('thumbnail_file')->store('courses', 'public');
            // If successfully saved to disk, remove from validation array so 'uploaded' failure isn't re-triggered
            $rules['thumbnail_file'] = ['nullable'];
        } elseif ($request->filled('preserved_thumbnail_file')) {
            $preStoredThumbnail = $request->input('preserved_thumbnail_file');
            $rules['thumbnail_file'] = ['nullable'];
        }

        $preStoredVideo = null;
        if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $preStoredVideo = $request->file('video_file')->store('courses/videos', 'public');
            $rules['video_file'] = ['nullable'];
        } elseif ($request->filled('preserved_video_file')) {
            $preStoredVideo = $request->input('preserved_video_file');
            $rules['video_file'] = ['nullable'];
        }

        $preStoredMaterial = null;
        if ($request->hasFile('material_file') && $request->file('material_file')->isValid()) {
            $preStoredMaterial = $request->file('material_file')->store('courses/materials', 'public');
            $rules['material_file'] = ['nullable'];
        } elseif ($request->filled('preserved_material_file')) {
            $preStoredMaterial = $request->input('preserved_material_file');
            $rules['material_file'] = ['nullable'];
        }

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Flash the successfully uploaded file paths to session so the form picks them back up
            if ($preStoredThumbnail) session()->flash('preserved_thumbnail_file', $preStoredThumbnail);
            if ($preStoredVideo) session()->flash('preserved_video_file', $preStoredVideo);
            if ($preStoredMaterial) session()->flash('preserved_material_file', $preStoredMaterial);
            throw $e;
        }

        // Thumbnail resolution
        $thumbnailPath = $preStoredThumbnail ?: (!empty($validated['thumbnail_url']) ? $validated['thumbnail_url'] : null);


        // Lecture Video resolution
        $videoPath = $preStoredVideo ?: (!empty($validated['video_url']) ? $validated['video_url'] : null);

        // Material Document file resolution
        $materialFilePath = $preStoredMaterial;

        $course = Course::create([
            'instructor_id' => $isTeacher ? $user->id : ($request->input('instructor_id') ?: $user?->id),
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'thumbnail' => $thumbnailPath,
            'video_url' => $videoPath,
            'material_file' => $materialFilePath,
            'material_notes' => $validated['material_notes'] ?? null,
            'description' => $validated['description'] ?? null,
            'public_preview_content' => $validated['public_preview_content'] ?? null,
            'target_audience' => $validated['target_audience'],
            'format' => $validated['format'],
            'passing_grade' => $validated['passing_grade'],
            'recertification_days' => $validated['recertification_days'] ?? null,
            'price_cents' => $isTeacher ? 0 : $validated['price_cents'],
            'proposed_price_cents' => $validated['price_cents'],
            'special_pricing_tag' => $validated['special_pricing_tag'] ?? null,
            'price_approval_status' => $isTeacher ? 'PENDING' : 'APPROVED',
            'material_approval_status' => $isTeacher ? 'PENDING' : 'APPROVED',
            'currency' => strtoupper($validated['currency']),
            // For teachers, new course MUST NOT be live directly; strictly requires administrator approval
            'is_published' => $isTeacher ? false : $request->boolean('is_published'),
            'deletion_status' => Course::DELETION_NONE,
        ]);

        // Automatically initialize default Section 1
        CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Core Module 1: Foundational Framework',
            'sort_order' => 0,
        ]);

        $successMsg = $isTeacher
            ? sprintf('Course "%s" submitted successfully and queued for Academic Board moderation. It will become live to the public once approved by an administrator.', $course->title)
            : sprintf('Course "%s" created under selected level with uploaded video & materials.', $course->title);

        return redirect()->route('admin.courses.index')->with('success', $successMsg);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $user = Auth::user();
        $isTeacher = $user && $user->isTeacher() && !$user->isSuperAdmin();

        // Enforce ownership: Teacher can only edit his own course
        if ($isTeacher && $course->instructor_id !== $user->id) {
            abort(403, 'Unauthorized. You can only manage courses and materials assigned to your account.');
        }

        $rules = [
            'category_id' => [$isTeacher ? 'required' : 'nullable', 'exists:course_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:courses,slug,' . $course->id],
            'description' => ['nullable', 'string'],
            'target_audience' => ['required', 'in:CORPORATE,PUBLIC,BOTH'],
            'format' => ['required', 'in:WEEKLY,TOPICS,SELF_PACED'],
            'passing_grade' => ['required', 'integer', 'min:1', 'max:100'],
            'recertification_days' => ['nullable', 'integer', 'min:1'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'special_pricing_tag' => ['nullable', 'string', 'max:100'],
            'currency' => ['required', 'string', 'max:3'],
            'is_published' => ['nullable', 'boolean'],
            'public_preview_content' => ['nullable', 'string'],
            'thumbnail_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'thumbnail_url' => ['nullable', 'string', 'max:500'],
            'video_file' => ['nullable', 'file', 'mimes:mp4,mov,ogg,qt,webm', 'max:51200'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'material_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,zip,txt', 'max:51200'],
            'material_notes' => ['nullable', 'string'],
        ];

        // Check for PHP upload level errors
        if ($request->hasFile('thumbnail_file') && !$request->file('thumbnail_file')->isValid()) {
            $errCode = $request->file('thumbnail_file')->getError();
            $errMsg = match ($errCode) {
                UPLOAD_ERR_INI_SIZE => 'The uploaded thumbnail file exceeds the server\'s upload_max_filesize limit configured on this hosting server. Please select an image under 5MB or compress it.',
                default => 'The thumbnail file failed to upload (' . $request->file('thumbnail_file')->getErrorMessage() . ').',
            };
            return back()->withInput()->withErrors(['thumbnail_file' => $errMsg]);
        }

        if ($request->hasFile('video_file') && !$request->file('video_file')->isValid()) {
            $videoUpload = $request->file('video_file');
            $errMsg = match ($videoUpload->getError()) {
                UPLOAD_ERR_INI_SIZE => 'The uploaded video file exceeds the server\'s upload_max_filesize limit configured on this hosting server. Please paste a video URL or compress the video.',
                default => 'The video file failed to upload (' . $videoUpload->getErrorMessage() . ').',
            };
            return back()->withInput()->withErrors(['video_file' => $errMsg]);
        }

        if ($request->hasFile('material_file') && !$request->file('material_file')->isValid()) {
            $matUpload = $request->file('material_file');
            $errMsg = match ($matUpload->getError()) {
                UPLOAD_ERR_INI_SIZE => 'The attached material file exceeds the server\'s upload_max_filesize limit configured on this hosting server. Please compress or link the material.',
                default => 'The material file failed to upload (' . $matUpload->getErrorMessage() . ').',
            };
            return back()->withInput()->withErrors(['material_file' => $errMsg]);
        }

        $validated = $request->validate($rules);

        $data = [
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'public_preview_content' => $validated['public_preview_content'] ?? null,
            'material_notes' => $validated['material_notes'] ?? null,
            'target_audience' => $validated['target_audience'],
            'format' => $validated['format'],
            'passing_grade' => $validated['passing_grade'],
            'recertification_days' => $validated['recertification_days'] ?? null,
            'special_pricing_tag' => $validated['special_pricing_tag'] ?? null,
            'currency' => strtoupper($validated['currency']),
        ];

        if ($isTeacher) {
            // Teachers cannot self-publish. Only administrators can publish a course.
            $data['is_published'] = $course->is_published;
            $data['proposed_price_cents'] = $validated['price_cents'];
            // If teacher modified price or tag, set pending approval
            if ($course->price_cents !== $validated['price_cents'] || $course->special_pricing_tag !== ($validated['special_pricing_tag'] ?? null)) {
                $data['price_approval_status'] = 'PENDING';
                $data['rejection_reason'] = null;
            }
            // If the course was previously rejected or material was updated, queue for moderation
            if ($course->material_approval_status === 'REJECTED' || $request->hasFile('material_file')) {
                $data['material_approval_status'] = 'PENDING';
                $data['material_rejection_reason'] = null;
                $data['is_published'] = false;
            }
        } else {
            $data['price_cents'] = $validated['price_cents'];
            $data['proposed_price_cents'] = $validated['price_cents'];
            $data['price_approval_status'] = 'APPROVED';
            $data['rejection_reason'] = null;
            $data['is_published'] = $request->boolean('is_published');
            if ($data['is_published']) {
                $data['material_approval_status'] = 'APPROVED';
                $data['material_rejection_reason'] = null;
            }
        }

        if ($request->hasFile('thumbnail_file')) {
            $data['thumbnail'] = $request->file('thumbnail_file')->store('courses', 'public');
        } elseif ($request->filled('thumbnail_url')) {
            $data['thumbnail'] = $validated['thumbnail_url'];
        }

        if ($request->hasFile('video_file')) {
            $data['video_url'] = $request->file('video_file')->store('courses/videos', 'public');
        } elseif ($request->filled('video_url')) {
            $data['video_url'] = $validated['video_url'];
        }

        if ($request->hasFile('material_file')) {
            $data['material_file'] = $request->file('material_file')->store('courses/materials', 'public');
            if ($isTeacher) {
                $data['material_approval_status'] = 'PENDING';
                $data['material_rejection_reason'] = null;
            }
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', sprintf('Course "%s" and materials updated successfully.', $course->title));
    }

    public function destroy(Course $course): RedirectResponse
    {
        $user = Auth::user();
        $isTeacher = $user && $user->isTeacher() && !$user->isSuperAdmin();

        // Enforce deletion rule:
        // When a course has been approved/published or belongs to a teacher,
        // teacher cannot delete directly without admin approval.
        if ($isTeacher) {
            if ($course->instructor_id !== $user->id) {
                abort(403, 'Unauthorized.');
            }

            return redirect()->route('admin.courses.index')->with('error', 'Teacher courses cannot be deleted directly. Please click "Request Deletion" to submit an administrator approval request.');
        }

        $title = $course->title;

        DB::transaction(function () use ($course) {
            $course->sections()->each(function ($section) {
                $section->modules()->delete();
                $section->delete();
            });
            $course->enrollments()->delete();
            $course->delete();
        });

        return redirect()->route('admin.courses.index')->with('success', sprintf('Course "%s" deleted successfully.', $title));
    }

    /**
     * Teacher submits a request to delete a course/material.
     */
    public function requestDelete(Request $request, Course $course): RedirectResponse
    {
        $user = Auth::user();
        $isTeacher = $user && $user->isTeacher() && !$user->isSuperAdmin();

        if ($isTeacher && $course->instructor_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $course->update([
            'deletion_status' => Course::DELETION_REQUESTED,
            'deletion_reason' => $request->input('reason', 'Teacher requested deletion of this course and materials.'),
        ]);

        return redirect()->route('admin.courses.index')->with('success', sprintf('Deletion request for "%s" has been submitted. An administrator must approve it before removal.', $course->title));
    }

    /**
     * Administrator approves course deletion request and purges records.
     */
    public function approveDelete(Course $course): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Administrator privileges required to approve course deletions.');
        }

        $title = $course->title;

        DB::transaction(function () use ($course) {
            $course->sections()->each(function ($section) {
                $section->modules()->delete();
                $section->delete();
            });
            $course->enrollments()->delete();
            $course->delete();
        });

        return redirect()->route('admin.courses.index')->with('success', sprintf('Deletion approved: Course "%s" and associated materials have been purged.', $title));
    }

    /**
     * Administrator approves course special pricing and tag.
     */
    public function approvePricing(Course $course): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Administrator privileges required.');
        }

        if ($course->proposed_price_cents !== null) {
            $course->price_cents = $course->proposed_price_cents;
        }
        $course->price_approval_status = 'APPROVED';
        $course->rejection_reason = null;
        $course->save();

        \App\Services\AuditService::log('COURSE_PRICING_APPROVED', $course, null, [
            'approved_by' => $user->name,
            'approved_price_cents' => $course->price_cents,
            'special_pricing_tag' => $course->special_pricing_tag,
        ]);

        return redirect()->route('admin.courses.index')->with('success', sprintf('Special pricing and tag approved for course "%s".', $course->title));
    }

    /**
     * Administrator rejects course special pricing and tag with compliance reason and notification email.
     */
    public function rejectPricing(Request $request, Course $course): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Administrator privileges required.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $course->price_approval_status = 'REJECTED';
        $course->rejection_reason = $request->input('reason');
        $course->save();

        // Dispatch formal email to teacher with direct link requiring authentication
        if ($course->instructor && $course->instructor->email) {
            $directAuthLink = route('login', ['redirect' => route('admin.courses.index')]);
            try {
                \Illuminate\Support\Facades\Mail::send(
                    'emails.compliance_notice',
                    [
                        'recipientName' => $course->instructor->name,
                        'heading' => 'Course Pricing Review Notice',
                        'introText' => "Your proposed course pricing/special pricing tag for \"{$course->title}\" was reviewed and could not be approved at this time.",
                        'reasonText' => $course->rejection_reason,
                        'actionUrl' => $directAuthLink,
                        'actionText' => 'View & Revise Pricing Proposal',
                        'subject' => "Pricing Review & Non-Compliance Notice: {$course->title}",
                    ],
                    function ($m) use ($course) {
                        $m->to($course->instructor->email)->subject("Pricing Review & Non-Compliance Notice: {$course->title}");
                    }
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed sending pricing rejection email: ' . $e->getMessage());
            }
        }

        \App\Services\AuditService::log('COURSE_PRICING_REJECTED', $course, null, [
            'rejected_by' => $user->name,
            'reason' => $course->rejection_reason,
        ]);

        return redirect()->route('admin.courses.index')->with('info', sprintf('Pricing proposal for course "%s" was rejected and notification dispatched to instructor.', $course->title));
    }

    public function approve(Course $course): RedirectResponse
    {
        $course->is_published = true;
        $course->material_approval_status = 'APPROVED';
        $course->material_rejection_reason = null;
        if ($course->price_approval_status === 'PENDING') {
            $course->price_cents = $course->proposed_price_cents ?: $course->price_cents;
            $course->price_approval_status = 'APPROVED';
        }
        $course->save();

        \App\Services\AuditService::log('COURSE_APPROVED_AND_PUBLISHED', $course, null, [
            'approved_by' => auth()->user()->name ?? 'Administrator',
            'course_title' => $course->title,
            'level' => $course->category?->name ?? 'Unassigned',
        ]);

        return back()->with('success', sprintf('Course "%s" has been approved and is now live to the public.', $course->title));
    }

    public function reject(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $reason = $validated['reason'] ?? $request->input('reason', 'Course content or syllabus does not comply with curriculum standards.');

        $course->is_published = false;
        $course->material_approval_status = 'REJECTED';
        $course->material_rejection_reason = $reason;
        $course->save();

        if ($course->instructor && $course->instructor->email) {
            $directAuthLink = route('login', ['redirect' => route('admin.courses.index')]);
            try {
                \Illuminate\Support\Facades\Mail::send(
                    'emails.compliance_notice',
                    [
                        'recipientName' => $course->instructor->name,
                        'heading' => 'Curriculum Review Notice',
                        'introText' => "Your course submission for \"{$course->title}\" was reviewed by the academic board and requires revision before publication.",
                        'reasonText' => $course->material_rejection_reason,
                        'actionUrl' => $directAuthLink,
                        'actionText' => 'View & Update Course',
                        'subject' => "Curriculum Non-Compliance Notice: {$course->title}",
                    ],
                    function ($m) use ($course) {
                        $m->to($course->instructor->email)->subject("Curriculum Non-Compliance Notice: {$course->title}");
                    }
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed sending course rejection email: ' . $e->getMessage());
            }
        }

        \App\Services\AuditService::log('COURSE_REJECTED', $course, null, [
            'rejected_by' => auth()->user()->name ?? 'Administrator',
            'course_title' => $course->title,
            'reason' => $course->material_rejection_reason,
        ]);

        return back()->with('info', sprintf('Course "%s" rejected and returned to draft with compliance feedback dispatched to author.', $course->title));
    }
}

