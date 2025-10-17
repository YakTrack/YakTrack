<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTestResultEvidenceRequest;
use App\Http\Requests\UpdateTestResultRequest;
use App\Models\TestResult;
use App\Models\TestResultEvidence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class TestResultController extends Controller
{
    public function update(UpdateTestResultRequest $request, TestResult $testResult): RedirectResponse
    {
        $testResult->update($request->validated());

        return redirect()->back()
            ->with('success', 'Test result has been updated.');
    }

    public function addEvidence(StoreTestResultEvidenceRequest $request, TestResult $testResult): RedirectResponse
    {
        $validated = $request->validated();
        $data = [
            'test_result_id' => $testResult->id,
            'type'           => $validated['type'],
            'sort_order'     => $testResult->evidence()->max('sort_order') + 1,
        ];

        if ($validated['type'] === 'image' && $request->hasFile('file')) {
            $file = $request->file('file');
            $filename = uniqid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('test-evidence', $filename, 'public');
            $data['file_path'] = $path;
            $data['content'] = $path; // Also store in content for consistency
        }

        if ($validated['type'] === 'text') {
            $data['content'] = $validated['content'];
        }

        TestResultEvidence::create($data);

        return redirect()->back()
            ->with('success', 'Evidence has been added.');
    }

    public function removeEvidence(TestResultEvidence $evidence): RedirectResponse
    {
        // Delete file if it's an image
        if ($evidence->isImage()) {
            $filePath = $evidence->file_path ?? $evidence->content;
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
        }

        $evidence->delete();

        return redirect()->back()
            ->with('success', 'Evidence has been removed.');
    }
}
