<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimAudit;
use App\Models\ClaimDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClaimDocumentController extends Controller
{
    public function upload(Request $request, Claim $claim, ClaimDocument $document): RedirectResponse
    {
        abort_if($claim->profile->user_id !== auth()->id(), 403);
        abort_if(!in_array($claim->status, ['draft', 'returned']), 403);
        abort_if($document->claim_id !== $claim->id, 404);

        $request->validate([
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        // Delete old file if exists
        if ($document->file_path) {
            Storage::disk('local')->delete($document->file_path);
        }

        $file = $request->file('document_file');
        $path = $file->store('claim_documents', 'local');

        $document->update([
            'file_path'          => $path,
            'file_original_name' => $file->getClientOriginalName(),
            'file_mime'          => $file->getMimeType(),
            'file_size'          => $file->getSize(),
            'is_uploaded'        => true,
            'uploaded_at'        => now(),
        ]);

        ClaimAudit::record($claim, 'document_uploaded', $claim->status, $claim->status,
            "Uploaded: {$document->label}");

        return back()->with('success', "'{$document->label}' uploaded successfully.");
    }

    public function remove(Claim $claim, ClaimDocument $document): RedirectResponse
    {
        abort_if($claim->profile->user_id !== auth()->id(), 403);
        abort_if(!in_array($claim->status, ['draft', 'returned']), 403);
        abort_if($document->claim_id !== $claim->id, 404);

        if ($document->file_path) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->update([
            'file_path'          => null,
            'file_original_name' => null,
            'file_mime'          => null,
            'file_size'          => null,
            'is_uploaded'        => false,
            'uploaded_at'        => null,
        ]);

        ClaimAudit::record($claim, 'document_removed', $claim->status, $claim->status,
            "Removed: {$document->label}");

        return back()->with('success', "'{$document->label}' removed.");
    }
}
