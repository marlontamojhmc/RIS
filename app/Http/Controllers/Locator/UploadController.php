<?php

namespace App\Http\Controllers\Locator;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Locator\Upload;

class UploadController extends Controller
{
    /**
     * List all uploads for a given application (or user).
     */
    public function index(Request $request)
    {
        $applicationId = $request->input('application_form_id');
        if (!$applicationId) {
            return response()->json(['error' => 'application_form_id required'], 400);
        }

        $uploads = Upload::where('user_id', Auth::id())
            ->where('application_form_id', $applicationId)
            ->get();

        // Add public URLs
        $uploads->transform(fn($upload) => [
            'id' => $upload->id,
            'file_name' => $upload->file_name,
            'file_path' => $upload->file_path,
            'url' => Storage::url($upload->file_path),
            'file_type' => $upload->file_type,
            'file_size' => $upload->file_size,
        ]);

        return response()->json([
            'uploads' => $uploads
        ]);
    }

    /**
     * Store uploaded files.
     */
    public function store(Request $request)
{
    $request->validate([
        'file' => 'required_without:files|file|max:5120',
        'files.*' => 'sometimes|file|max:5120',
        'application_form_id' => 'required|exists:application_forms,id',
    ]);

    $savedFiles = [];
    $files = $request->file('files') ?? [$request->file('file')];

    foreach ($files as $file) {
        if (!$file) continue;

        // store file
        $storedPath = $file->store('loctr', 'public');

        // create DB record
        $upload = Upload::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->input('description'),
            'user_id' => Auth::id(),
            'application_form_id' => $request->application_form_id,
        ]);

        // URL only for returning
        $upload->url = Storage::url($storedPath);

        $savedFiles[] = $upload;
    }

    return response()->json([
        'message' => 'File(s) uploaded successfully',
        'files' => $savedFiles,
    ]);
}

    /**
     * Delete a specific upload.
     */
    public function destroy(string $id)
    {
        $upload = Upload::findOrFail($id);

        if ($upload->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        $upload->delete();

        return response()->json(['message' => 'Upload deleted successfully']);
    }
   
    public function verify($id)
    { dd($id);
    $upload = Upload::findOrFail($id);

    $upload->status = 'verified';
    $upload->save();

    return response()->json([
        'message' => 'Document has been verified successfully.',
        'upload' => $upload
    ]);
}
}
