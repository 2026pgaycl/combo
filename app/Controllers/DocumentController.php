<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csv;
use App\Core\Request;
use App\Core\Upload;
use App\Models\Document;

class DocumentController extends Controller
{
    private const ALLOWED_TYPES = ['building', 'tenant', 'lease', 'unit'];

    private const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
    ];

    public function index(): void
    {
        $this->view('documents.index', [
            'documents' => Document::withLabels(),
        ]);
    }

    /**
     * Export only — every row references an uploaded file, so there's no
     * sensible bulk-import format at the CSV level (files still have to be
     * uploaded individually from the relevant building/tenant/lease/unit page).
     */
    public function export(): void
    {
        Csv::export(
            'documents.csv',
            ['id', 'related_type', 'related_id', 'title', 'file_path', 'expiry_date', 'created_at'],
            Document::all('id DESC')
        );
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $relatedType = (string) Request::input('related_type', '');
        $relatedId = (int) Request::input('related_id', 0);
        $title = trim((string) Request::input('title', ''));
        $expiryDate = Request::input('expiry_date') ?: null;

        $redirectBack = $this->redirectTarget($relatedType, $relatedId);

        if (!in_array($relatedType, self::ALLOWED_TYPES, true) || $relatedId === 0 || $title === '') {
            $this->flash('error', 'Enter a title and choose a file.');
            $this->redirect($redirectBack);
        }

        $filePath = Upload::store(
            Request::file('document'),
            'documents',
            self::ALLOWED_EXTENSIONS,
            self::ALLOWED_MIME_TYPES,
            $error
        );

        if ($filePath === null) {
            $this->flash('error', $error);
            $this->redirect($redirectBack);
        }

        Document::create([
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'title' => $title,
            'file_path' => $filePath,
            'expiry_date' => $expiryDate,
        ]);

        $this->flash('success', 'Document uploaded.');
        $this->redirect($redirectBack);
    }

    public function destroy(string $id): void
    {
        $this->verifyCsrf();

        $document = Document::find((int) $id);
        if (!$document) {
            $this->redirect('/documents');
        }

        Upload::delete($document['file_path']);
        Document::delete((int) $id);

        $this->flash('success', 'Document deleted.');
        $this->redirect($this->redirectTarget($document['related_type'], (int) $document['related_id']));
    }

    private function redirectTarget(string $type, int $id): string
    {
        return match ($type) {
            'building' => "/buildings/{$id}",
            'tenant' => "/tenants/{$id}",
            'lease' => "/leases/{$id}",
            'unit' => "/units/{$id}",
            default => '/documents',
        };
    }
}
