<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csv;
use App\Core\Request;
use App\Core\Upload;
use App\Models\Building;
use App\Models\Document;
use App\Models\Floor;
use App\Models\Unit;
use App\Models\UnitPhoto;

class UnitController extends Controller
{
    private const PHOTO_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private const PHOTO_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function index(): void
    {
        $buildingId = Request::input('building_id');
        $this->view('units.index', [
            'units' => Unit::withDetails($buildingId ? (int) $buildingId : null),
            'buildings' => Building::all('name'),
            'selectedBuildingId' => $buildingId,
        ]);
    }

    public function create(): void
    {
        $this->view('units.create', [
            'buildings' => Building::all('name'),
        ]);
    }

    /** AJAX-style endpoint: floors for a given building, used to populate the floor dropdown. */
    public function floorsForBuilding(string $buildingId): void
    {
        $this->json(Floor::forBuilding((int) $buildingId));
    }

    public function export(): void
    {
        Csv::export(
            'units.csv',
            ['id', 'floor_id', 'unit_number', 'unit_type', 'size_sqft', 'base_rent', 'status'],
            Unit::all('unit_number')
        );
    }

    /**
     * Bulk-creates units from a CSV in the same shape export() produces.
     * floor_id must reference an existing floor (export a building's units first
     * to see valid ids) — rows with an unknown floor_id are skipped.
     */
    public function import(): void
    {
        $this->verifyCsrf();

        $imported = 0;
        $skipped = 0;

        foreach (Csv::parseUpload(Request::file('csv')) as $row) {
            $floorId = (int) ($row['floor_id'] ?? 0);
            $unitNumber = trim((string) ($row['unit_number'] ?? ''));
            if ($floorId === 0 || $unitNumber === '' || !Floor::find($floorId)) {
                $skipped++;
                continue;
            }

            Unit::create([
                'floor_id' => $floorId,
                'unit_number' => $unitNumber,
                'unit_type' => $row['unit_type'] ?: 'office',
                'size_sqft' => (float) ($row['size_sqft'] ?? 0),
                'base_rent' => (float) ($row['base_rent'] ?? 0),
                'status' => $row['status'] ?: 'vacant',
            ]);
            $imported++;
        }

        $this->flash('success', "Imported {$imported} unit(s)." . ($skipped ? " Skipped {$skipped} row(s) with a missing unit number or unknown floor_id." : ''));
        $this->redirect('/units');
    }

    public function store(): void
    {
        $this->verifyCsrf();

        Unit::create([
            'floor_id' => (int) Request::input('floor_id'),
            'unit_number' => Request::input('unit_number', ''),
            'unit_type' => Request::input('unit_type', 'office'),
            'size_sqft' => (float) Request::input('size_sqft', 0),
            'base_rent' => (float) Request::input('base_rent', 0),
            'status' => Request::input('status', 'vacant'),
        ]);

        $this->flash('success', 'Unit added.');
        $this->redirect('/units');
    }

    public function show(string $id): void
    {
        $unit = Unit::findWithDetails((int) $id);
        if (!$unit) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }
        $this->view('units.show', [
            'unit' => $unit,
            'documents' => Document::forRelated('unit', (int) $id),
            'photos' => UnitPhoto::forUnit((int) $id),
        ]);
    }

    public function storePhoto(string $id): void
    {
        $this->verifyCsrf();

        $filePath = Upload::store(
            Request::file('photo'),
            'unit_photos',
            self::PHOTO_EXTENSIONS,
            self::PHOTO_MIME_TYPES,
            $error
        );

        if ($filePath === null) {
            $this->flash('error', $error);
            $this->redirect("/units/{$id}");
        }

        UnitPhoto::create([
            'unit_id' => (int) $id,
            'file_path' => $filePath,
        ]);

        $this->flash('success', 'Photo added.');
        $this->redirect("/units/{$id}");
    }

    public function destroyPhoto(string $photoId): void
    {
        $this->verifyCsrf();

        $photo = UnitPhoto::find((int) $photoId);
        if (!$photo) {
            $this->redirect('/units');
        }

        Upload::delete($photo['file_path']);
        UnitPhoto::delete((int) $photoId);

        $this->flash('success', 'Photo deleted.');
        $this->redirect("/units/{$photo['unit_id']}");
    }

    public function edit(string $id): void
    {
        $unit = Unit::findWithDetails((int) $id);
        if (!$unit) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }
        $this->view('units.edit', [
            'unit' => $unit,
            'buildings' => Building::all('name'),
            'floors' => Floor::forBuilding((int) $unit['building_id']),
        ]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();

        Unit::update((int) $id, [
            'floor_id' => (int) Request::input('floor_id'),
            'unit_number' => Request::input('unit_number', ''),
            'unit_type' => Request::input('unit_type', 'office'),
            'size_sqft' => (float) Request::input('size_sqft', 0),
            'base_rent' => (float) Request::input('base_rent', 0),
            'status' => Request::input('status', 'vacant'),
        ]);

        $this->flash('success', 'Unit updated.');
        $this->redirect("/units/{$id}");
    }

    public function destroy(string $id): void
    {
        $this->verifyCsrf();
        Unit::delete((int) $id);
        $this->flash('success', 'Unit deleted.');
        $this->redirect('/units');
    }
}
