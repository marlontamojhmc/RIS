<?php

namespace App\Services;

use App\Services\Contracts\ISezrisService;
use App\Models\Locator\Upload;
class UploadService implements ISezrisService
{
    public function fetchData(array $filters): array
    {
        return [
            [
                'id' => 1,
                'title' => 'SEZRIS Entry A',
                'status' => 'pending',
                'uploaded_file' => "http://localhost/storage/test.jpg",
            ],
            [
                'id' => 2,
                'title' => 'SEZRIS Entry B',
                'status' => 'approved',
                'uploaded_file' => "http://localhost/storage/test2.jpg",
            ],
            [
                'id' => 3,
                'title' => 'SEZRIS Entry C',
                'status' => 'rejected',
                'uploaded_file' => "http://localhost/storage/test3.jpg",
            ],
        ];
    }

    public function store(array $data): bool
    {
        // Simulate storing upload data
        return true;
    }

    public function getById(int $id): array|null
    {
        // Simulate finding one of the uploads
        $uploads = $this->fetchData([]);
        foreach ($uploads as $upload) {
            if ($upload['id'] === $id) {
                return $upload;
            }
        }

        return null;
    }
    public function uploadFiles(array $files, $description, $applicationFormId, $userId)
{ 
    foreach ($files as $fileItem) {

        // Extract title + file object
        $title = $fileItem['title'] ?? null;
        $file = $fileItem['file'] ?? null;

        if (!$file) {
            continue; // skip rows with no file
        }

        // Upload file
        $path = $file->store('uploads', 'public');

        // Save upload record
        Upload::create([
            'file_name' => $title,                      // use title as requested
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'description' => $description,
            'user_id' => $userId,
            'application_form_id' => $applicationFormId,
        ]);
    }
}
public function uploadFile($file, $title, $applicationFormId, $userId)
    {
        if (!$file || !method_exists($file, 'store')) {
            return;
        }

        $path = $file->store('uploads', 'public');

        Upload::create([
            'file_name'           => $title ?: $file->getClientOriginalName(),
            'file_path'           => $path,
            'file_type'           => $file->getClientMimeType(),
            'file_size'           => $file->getSize(),
            'description'         => null,
            'user_id'             => $userId,
            'application_form_id' => $applicationFormId,
        ]);
    }

}
