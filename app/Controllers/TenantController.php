<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csv;
use App\Core\Database;
use App\Core\Request;
use App\Models\Document;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\User;

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

    public function export(): void
    {
        Csv::export(
            'tenants.csv',
            ['id', 'company_name', 'reg_no', 'contact_name', 'contact_email', 'contact_phone', 'billing_address'],
            Tenant::all('company_name')
        );
    }

    /** Bulk-creates tenants from a CSV in the same shape export() produces. Never updates existing rows. */
    public function import(): void
    {
        $this->verifyCsrf();

        $imported = 0;
        $skipped = 0;

        foreach (Csv::parseUpload(Request::file('csv')) as $row) {
            $companyName = trim((string) ($row['company_name'] ?? ''));
            $contactName = trim((string) ($row['contact_name'] ?? ''));
            if ($companyName === '' || $contactName === '') {
                $skipped++;
                continue;
            }

            Tenant::create([
                'company_name' => $companyName,
                'reg_no' => $row['reg_no'] ?: null,
                'contact_name' => $contactName,
                'contact_email' => $row['contact_email'] ?: null,
                'contact_phone' => $row['contact_phone'] ?: null,
                'billing_address' => $row['billing_address'] ?: null,
            ]);
            $imported++;
        }

        $this->flash('success', "Imported {$imported} tenant(s)." . ($skipped ? " Skipped {$skipped} row(s) missing a company or contact name." : ''));
        $this->redirect('/tenants');
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
            'portalUser' => User::findByTenant((int) $id),
        ]);
    }

    /** Creates a portal login for this tenant, using their contact email. The temporary password is shown once. */
    public function createPortalUser(string $id): void
    {
        $this->verifyCsrf();

        $tenant = Tenant::find((int) $id);
        if (!$tenant) {
            $this->redirect('/tenants');
        }

        if (empty($tenant['contact_email'])) {
            $this->flash('error', 'Add a contact email before creating a portal login.');
            $this->redirect("/tenants/{$id}");
        }

        if (User::findByTenant((int) $id)) {
            $this->flash('error', 'A portal login already exists for this tenant.');
            $this->redirect("/tenants/{$id}");
        }

        if (Database::selectOne('SELECT id FROM users WHERE email = ?', [$tenant['contact_email']])) {
            $this->flash('error', 'That email is already used by another account.');
            $this->redirect("/tenants/{$id}");
        }

        $role = Database::selectOne('SELECT id FROM roles WHERE slug = "tenant"');
        $password = bin2hex(random_bytes(4));

        User::create([
            'role_id' => $role['id'],
            'name' => $tenant['contact_name'],
            'email' => $tenant['contact_email'],
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'tenant_id' => (int) $id,
            'status' => 'active',
        ]);

        $this->flash('success', "Portal login created for {$tenant['contact_email']} — temporary password: {$password}. Share this with the tenant now; it won't be shown again.");
        $this->redirect("/tenants/{$id}");
    }

    /** Resets an existing portal login's password. The new temporary password is shown once. */
    public function resetPortalPassword(string $id): void
    {
        $this->verifyCsrf();

        $user = User::findByTenant((int) $id);
        if (!$user) {
            $this->flash('error', 'No portal login exists for this tenant yet.');
            $this->redirect("/tenants/{$id}");
        }

        $password = bin2hex(random_bytes(4));
        User::update((int) $user['id'], ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);

        $this->flash('success', "Password reset — new temporary password: {$password}. Share this with the tenant now; it won't be shown again.");
        $this->redirect("/tenants/{$id}");
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
