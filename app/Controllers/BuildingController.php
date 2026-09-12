<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Building;
use App\Models\Document;
use App\Models\Floor;

class BuildingController extends Controller
{
    public function index(): void
    {
        $this->view('buildings.index', [
            'buildings' => Building::withStats(),
        ]);
    }

    public function create(): void
    {
        $this->view('buildings.create');
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $name = trim((string) Request::input('name'));
        if ($name === '') {
            $this->flash('error', 'Building name is required.');
            $this->redirect('/buildings/create');
        }

        $id = Building::create([
            'name' => $name,
            'address_line1' => Request::input('address_line1', ''),
            'address_line2' => Request::input('address_line2') ?: null,
            'city' => Request::input('city', ''),
            'state' => Request::input('state') ?: null,
            'postcode' => Request::input('postcode') ?: null,
            'country' => Request::input('country', 'Malaysia'),
        ]);

        $this->flash('success', 'Building created.');
        $this->redirect("/buildings/{$id}");
    }

    public function show(string $id): void
    {
        $building = Building::find((int) $id);
        if (!$building) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $this->view('buildings.show', [
            'building' => $building,
            'floors' => Floor::forBuilding((int) $id),
            'documents' => Document::forRelated('building', (int) $id),
        ]);
    }

    public function edit(string $id): void
    {
        $building = Building::find((int) $id);
        if (!$building) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }
        $this->view('buildings.edit', ['building' => $building]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();

        Building::update((int) $id, [
            'name' => Request::input('name', ''),
            'address_line1' => Request::input('address_line1', ''),
            'address_line2' => Request::input('address_line2') ?: null,
            'city' => Request::input('city', ''),
            'state' => Request::input('state') ?: null,
            'postcode' => Request::input('postcode') ?: null,
            'country' => Request::input('country', 'Malaysia'),
        ]);

        $this->flash('success', 'Building updated.');
        $this->redirect("/buildings/{$id}");
    }

    public function destroy(string $id): void
    {
        $this->verifyCsrf();
        Building::delete((int) $id);
        $this->flash('success', 'Building deleted.');
        $this->redirect('/buildings');
    }

    /** Adds a floor to a building — kept here to avoid a separate controller for one action. */
    public function storeFloor(string $buildingId): void
    {
        $this->verifyCsrf();

        Floor::create([
            'building_id' => (int) $buildingId,
            'name' => Request::input('name', ''),
            'level_order' => (int) Request::input('level_order', 0),
        ]);

        $this->redirect("/buildings/{$buildingId}");
    }
}
