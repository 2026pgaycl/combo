<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Document;
use App\Models\Lease;
use App\Models\Tenant;

class TenantController extends Controller
{
    public function index(): void
    {
        $this->view('tenants.index', [
            'tenants' => Tenant::withActiveLease(),
        ]);
    }

    public function create(): void
    {
        $this->view('tenants.create');
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $id = Tenant::create([
            'company_name' => Request::input('company_name', ''),
            'reg_no' => Request::input('reg_no') ?: null,
            'contact_name' => Request::input('contact_name', ''),
            'contact_email' => Request::input('contact_email') ?: null,
            'contact_phone' => Request::input('contact_phone') ?: null,
            'billing_address' => Request::input('billing_address') ?: null,
        ]);

        $this->flash('success', 'Tenant added.');
        $this->redirect("/tenants/{$id}");
    }

    public function show(string $id): void
    {
        $tenant = Tenant::find((int) $id);
        if (!$tenant) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $leases = array_filter(Lease::withDetails(), fn($l) => (int) $l['tenant_id'] === (int) $id);

        $this->view('tenants.show', [
            'tenant' => $tenant,
            'leases' => $leases,
            'documents' => Document::forRelated('tenant', (int) $id),
        ]);
    }

    public function edit(string $id): void
    {
        $tenant = Tenant::find((int) $id);
        if (!$tenant) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }
        $this->view('tenants.edit', ['tenant' => $tenant]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();

        Tenant::update((int) $id, [
            'company_name' => Request::input('company_name', ''),
            'reg_no' => Request::input('reg_no') ?: null,
            'contact_name' => Request::input('contact_name', ''),
            'contact_email' => Request::input('contact_email') ?: null,
            'contact_phone' => Request::input('contact_phone') ?: null,
            'billing_address' => Request::input('billing_address') ?: null,
        ]);

        $this->flash('success', 'Tenant updated.');
        $this->redirect("/tenants/{$id}");
    }

    public function destroy(string $id): void
    {
        $this->verifyCsrf();
        Tenant::delete((int) $id);
        $this->flash('success', 'Tenant deleted.');
        $this->redirect('/tenants');
    }
}
