<?php

namespace App\Http\Controllers;

use App\Models\WorkerDocument;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminWorkerDocumentDownloadController extends Controller
{
    public function __invoke(WorkerDocument $workerDocument): StreamedResponse|Response
    {
        abort_unless(auth()->user()?->can('admin.people.documents.download'), 403);
        $media = $workerDocument->getFirstMedia('worker_documents');
        abort_unless($media, 404);

        activity()->causedBy(auth()->user())->performedOn($workerDocument)->withProperties([
            'document_type' => $workerDocument->document_type, 'worker_document_id' => $workerDocument->id,
            'worker_application_id' => $workerDocument->worker_application_id, 'worker_profile_id' => $workerDocument->worker_profile_id,
            'media_id' => $media->id, 'filename' => $media->file_name, 'timestamp' => now()->toIso8601String(),
        ])->event('worker_document.downloaded')->log('Private worker document downloaded');

        return response()->streamDownload(fn () => print($media->stream()->getContents()), $media->file_name, ['Content-Type' => $media->mime_type ?? 'application/octet-stream']);
    }
}
