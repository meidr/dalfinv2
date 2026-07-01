<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentToken;
use Barryvdh\DomPDF\Facade\Pdf;

class VerifyDocumentController extends Controller
{
    /**
     * Verify a document by its token (public, no auth required).
     */
    public function verify(string $token)
    {
        $doc = DocumentToken::where('token', $token)->first();

        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan atau token tidak valid.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $doc->token,
                'document_type' => $doc->document_type,
                'nomor_surat' => $doc->nomor_surat,
                'nama_penandatangan' => $doc->nama_penandatangan,
                'jabatan_penandatangan' => $doc->jabatan_penandatangan,
                'nama_berkas' => $doc->nama_berkas,
                'metadata' => $doc->metadata,
                'tanggal_terbit' => $doc->created_at->translatedFormat('d F Y, H:i'),
                'pdf_url' => url("/api/verify/{$doc->token}/pdf"),
            ],
        ]);
    }

    /**
     * Stream the PDF inline for a verified document (renders in iframe/browser).
     */
    public function pdf(string $token)
    {
        $doc = DocumentToken::where('token', $token)->first();

        if (!$doc) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        // Re-generate the PDF on-the-fly based on document type
        // We use stream mode so the PDF renders inline in the browser
        $pdfController = app(\App\Http\Controllers\Api\Admin\PdfController::class);
        $request = request();

        // Force signature mode to 'qr' so the PDF includes the QR
        // Pass the existing token to avoid creating a new one
        // Pass _stream_mode so we can intercept the response
        $request->merge([
            '_existing_token' => $doc->token,
            '_force_qr' => true,
        ]);

        // Get the response from PdfController (returns $pdf->download())
        $response = null;
        switch ($doc->document_type) {
            case 'sk_tugas':
                $skripsi = \App\Models\Skripsi::findOrFail($doc->document_id);
                $response = $pdfController->skTugas($request, $skripsi);
                break;

            case 'berita_acara':
                $seminar = \App\Models\Seminar::findOrFail($doc->document_id);
                $response = $pdfController->beritaAcaraSeminar($request, $seminar);
                break;

            case 'sk_penguji':
                $seminar = \App\Models\Seminar::findOrFail($doc->document_id);
                $response = $pdfController->skPenguji($request, $seminar);
                break;

            case 'nota_bimbingan':
                $skripsi = \App\Models\Skripsi::findOrFail($doc->document_id);
                $response = $pdfController->notaBimbingan($request, $skripsi);
                break;

            case 'sk_yudisium':
                $skripsi = \App\Models\Skripsi::findOrFail($doc->document_id);
                $response = $pdfController->skYudisium($request, $skripsi);
                break;

            case 'lembar_pengesahan':
                $seminar = \App\Models\Seminar::findOrFail($doc->document_id);
                $response = $pdfController->lembarPengesahan($request, $seminar);
                break;

            default:
                abort(404, 'Jenis dokumen tidak dikenali.');
        }

        if (!$response) {
            abort(500, 'Gagal generate PDF.');
        }

        // Extract PDF content from the download response and return as inline stream
        // DomPDF's download() wraps content in a Response with Content-Disposition: attachment
        // We need to get the raw PDF and return it with Content-Disposition: inline
        $content = $response->getContent();

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . ($doc->nama_berkas ?: 'document.pdf') . '"',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
