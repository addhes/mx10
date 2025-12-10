<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Applications;
use App\Models\Jobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApplicationsController extends Controller
{
    /**
     * Apply for a job (Freelancer)
     * POST /api/jobs/{job}/apply
     */
    public function apply(Request $request, $jobId)
    {

        $validator = Validator::make($request->all(), [
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // cek jobs ada dan statusnya published
        $jobs = Jobs::where('id', $jobId)
            ->where('status', 'publish')
            ->first();

        if (!$jobs) {
            return response()->json([
                'message' => 'Job not found or not published'
            ], 404);
        }

        // Cek apakah sudah pernah apply untuk job ini
        $existingApplication = Applications::where('job_id', $jobId)
            ->where('freelancer_id', Auth::id())
            ->exists();

        if ($existingApplication) {
            return response()->json([
                'message' => 'You have already applied for this job'
            ], 400);
        }

        // Upload CV file
        $cvPath = null;
        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $fileName = 'cv_' . Auth::id() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $cvPath = $file->storeAs('cvs', $fileName, 'public');
        }

        // Create application
        $application = Applications::create([
            'job_id' => $jobId,
            'freelancer_id' => Auth::id(),
            'cv' => $cvPath,
        ]);

        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application->load('job')
        ], 201);
    }


    public function getJobApplications($jobId)
    {
        // dd($jobId);
        // Cek apakah job ada
        $job = Jobs::find($jobId);
        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        // Cek apakah user adalah employer pemilik job
        if (Auth::id() !== $job->user_id) {
            return response()->json([
                'message' => 'Unauthorized to view these applications'
            ], 403);
        }

        // Get applications with freelancer data
        $applications = Applications::with(['freelancer:id,name,email', 'job:id,title'])
            ->where('job_id', $jobId)
            ->orderBy('created_at', 'desc')
            ->get();

        // dd($applications);

        // Transform data untuk response
        $applications->transform(function ($app) {
            return [
                'id' => $app->id,
                'freelancer' => $app->freelancer,
                'job_title' => $app->job->title,
                'cv' => $app->cv ? Storage::url($app->cv) : null,
                'applied_at' => $app->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $app->updated_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'job_id' => $jobId,
            'job_title' => $job->title,
            'total_applications' => $applications->count(),
            'applications' => $applications
        ]);
    }


}