<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use App\Models\Report;
use Carbon\Carbon;

class ReportsAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = Report::where('created_at', '>=', Carbon::now()->subMinutes(60))
            ->orderBy("created_at", "desc")
            // ->where('counts', '<', 60)
            ->get();

        $reports = $reports->map(function ($report) {
            return [
                "uuid" => $report->uuid,
                "counts" => $report->counts,
                "name" => $report->name,
                "status" => $report->status,
                "image" =>  asset($report->image),
                "accident_type" => $report->accident_type,
                "created_at" => $report->created_at->diffForHumans(),
            ];
        });

        return response()->json($reports);
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $reports = Report::where('uuid', $uuid)
            ->firstOrFail();

        if ($reports->image) {
            $reports->image = asset($reports->image);
        }


        return response()->json($reports);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request)
    {

        // Check if a successful report already exists for this phone number
        $existingReport = Report::where('phone', $request->phone_number)
            ->where('status', '0')
            ->first();

        if ($existingReport) {
            return response()->json(['message' => 'A successful report already exists for this phone number.'], 400);
        } else {
            $report = Report::create($request->validated());
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reports/images', 'public');
            $report->image = 'storage/' . $imagePath; // Assuming 'image' is a column in your 'reports' table
            $report->save(); // Save the path of the image in the database
        }

        $message = 'Thank You ' . $report->name . ' for reporting a ' . $report->accident_type . ' accident. We will get back to you shortly.';

        return response()->json(['message' => $message, 'report' => $report]);
    }

    public function solved(Request $request, $uuid)
    {
        // Find the report by UUID
        $report = Report::where('uuid', $uuid)->firstOrFail();

        // Update the status attribute with the value from the request
        $report->update([
            'status' => 1,  // 1 means solved and 0 means pending 3 means rejected
        ]);

        // Return a JSON response indicating success
        return response()->json(['message' => 'Report status updated successfully', 'status' => $report->status]);
    }

    public function canceled(Request $request, $uuid)
    {
        // Find the report by UUID
        $report = Report::where('uuid', $uuid)->firstOrFail();


        // when requesting increeament of counts by 1
        $report->increment('counts');
        // $report->status = 0;  // 1 means solved and 0 means pending 3 means rejected

        if ($report->counts >= 60) {
            $report->status = 3;
        } else {
            $report->status = 0;
        }

        // Return a JSON response indicating success
        return response()->json(['message' => 'Report status updated successfully', 'counts' => $report->counts]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $uuid)
    // {
    //     // Find the report by UUID
    //     $report = Report::where('uuid', $uuid)->firstOrFail();

    //     // Update the status attribute with the value from the request
    //     $report->update([
    //         'status' => $request->input('status')
    //     ]);

    //     // Return a JSON response indicating success
    //     return response()->json(['message' => 'Report status updated successfully', 'status' => $report->status]);
    // }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $report = Report::where('uuid', $uuid)->firstOrFail();
        $report->delete();
        return response()->json(['message' => 'Report deleted successfully!']);
    }
}
