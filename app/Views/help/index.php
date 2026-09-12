<h1>Help &amp; Guide</h1>
<p class="text-muted">A walkthrough of every module, what it's for, and how to use it — including bulk CSV import/export, available on every module below except Documents.</p>

<div class="help-toc">
    <a href="#buildings">Buildings</a>
    <a href="#units">Units</a>
    <a href="#tenants">Tenants</a>
    <a href="#leases">Leases</a>
    <a href="#invoices">Invoices</a>
    <a href="#maintenance">Maintenance</a>
    <a href="#documents">Documents</a>
    <a href="#notifications">Notifications</a>
    <a href="#csv">CSV import/export</a>
</div>

<div class="help-section" id="buildings">
    <h2>Buildings</h2>
    <p>The top of the hierarchy: a building holds floors, which hold units. Add a building first, then add its floors from the building's own page before you can create units on them.</p>

    <div class="help-screenshot">
        <div class="help-screenshot-bar">combo_wip/buildings</div>
        <div class="help-screenshot-body">
            <div class="page-header">
                <h1 style="margin:0;">Buildings</h1>
                <a class="btn btn-primary" style="pointer-events:none;">+ Add Building</a>
            </div>
            <table class="data-table">
                <thead><tr><th>Name</th><th>City</th><th>Units</th><th>Occupied</th><th></th></tr></thead>
                <tbody>
                    <tr><td>Menara Combo</td><td>Kuala Lumpur</td><td>18</td><td>14</td><td>Edit</td></tr>
                    <tr><td>Plaza Sentosa</td><td>Petaling Jaya</td><td>9</td><td>6</td><td>Edit</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <p><strong>To get started:</strong> click <em>+ Add Building</em>, fill in the address, then open the building and use the inline form to add floors (e.g. "Level 12", with a sort order). Buildings can also carry general documents (leases, permits, insurance) via the Documents section on their own page.</p>
</div>

<div class="help-section" id="units">
    <h2>Units</h2>
    <p>Individual rentable spaces on a floor — offices, retail, or storage. A unit's status (vacant, occupied, reserved, under maintenance) updates automatically as leases are created or terminated.</p>

    <div class="help-screenshot">
        <div class="help-screenshot-bar">combo_wip/units</div>
        <div class="help-screenshot-body">
            <div class="page-header">
                <h1 style="margin:0;">Units</h1>
                <a class="btn btn-primary" style="pointer-events:none;">+ Add Unit</a>
            </div>
            <table class="data-table">
                <thead><tr><th>Unit</th><th>Building</th><th>Type</th><th>Base Rent</th><th>Status</th><th>Tenant</th></tr></thead>
                <tbody>
                    <tr><td>12-03</td><td>Menara Combo</td><td>office</td><td>RM 5,500.00</td><td><span class="badge badge-occupied">occupied</span></td><td>Acme Sdn Bhd</td></tr>
                    <tr><td>12-04</td><td>Menara Combo</td><td>office</td><td>RM 4,200.00</td><td><span class="badge badge-vacant">vacant</span></td><td>—</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <p><strong>To get started:</strong> click <em>+ Add Unit</em>, pick a building (its floors load automatically), then fill in the unit number and rent. Open a unit to attach a photo gallery or documents, or to report a maintenance issue for it directly.</p>
</div>

<div class="help-section" id="tenants">
    <h2>Tenants</h2>
    <p>The companies renting your units. A tenant record holds company and contact details; its lease history and any tenant-specific documents show on its own page.</p>

    <div class="help-screenshot">
        <div class="help-screenshot-bar">combo_wip/tenants</div>
        <div class="help-screenshot-body">
            <div class="page-header">
                <h1 style="margin:0;">Tenants</h1>
                <a class="btn btn-primary" style="pointer-events:none;">+ Add Tenant</a>
            </div>
            <table class="data-table">
                <thead><tr><th>Company</th><th>Contact</th><th>Unit</th><th>Lease Ends</th></tr></thead>
                <tbody>
                    <tr><td>Acme Sdn Bhd</td><td>Jane Tan</td><td>Menara Combo — 12-03</td><td>2027-12-31</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <p><strong>To get started:</strong> click <em>+ Add Tenant</em> before creating a lease for them — leases link an existing tenant to an existing (vacant) unit, they don't create either.</p>
</div>

<div class="help-section" id="leases">
    <h2>Leases</h2>
    <p>Ties one tenant to one unit for a period at a monthly rent. Creating a lease marks its unit occupied; terminating one frees the unit back to vacant. Leases due to end within 90 days are flagged.</p>

    <div class="help-screenshot">
        <div class="help-screenshot-bar">combo_wip/leases</div>
        <div class="help-screenshot-body">
            <div class="page-header">
                <h1 style="margin:0;">Leases</h1>
                <a class="btn btn-primary" style="pointer-events:none;">+ Create Lease</a>
            </div>
            <table class="data-table">
                <thead><tr><th>Tenant</th><th>Unit</th><th>End</th><th>Rent</th><th>Status</th></tr></thead>
                <tbody>
                    <tr class="row-warning"><td>Acme Sdn Bhd</td><td>Menara Combo — 12-03</td><td>2026-11-30 <span class="tag-alert">62d left</span></td><td>RM 5,500.00</td><td><span class="badge badge-active">active</span></td></tr>
                    <tr><td>Bright Ideas Sdn Bhd</td><td>Plaza Sentosa — 3-01</td><td>2028-06-30</td><td>RM 3,200.00</td><td><span class="badge badge-active">active</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <p><strong>To get started:</strong> click <em>+ Create Lease</em>, choose a vacant unit and a tenant, and set the dates and rent. From a lease's page you can attach typed lease documents (contract, KYC, insurance — each with its own expiry date), terminate the lease, or jump straight to creating its first invoice.</p>
</div>

<div class="help-section" id="invoices">
    <h2>Invoices</h2>
    <p>Billing for a lease's period — rent plus any extra line items (service charge, utilities) and tax. Recording a payment updates the balance automatically and flips the status to partially paid or paid once fully settled.</p>

    <div class="help-screenshot">
        <div class="help-screenshot-bar">combo_wip/invoices</div>
        <div class="help-screenshot-body">
            <div class="page-header">
                <h1 style="margin:0;">Invoices</h1>
                <a class="btn btn-primary" style="pointer-events:none;">+ Create Invoice</a>
            </div>
            <table class="data-table">
                <thead><tr><th>Invoice #</th><th>Tenant</th><th>Due</th><th>Total</th><th>Paid</th><th>Status</th></tr></thead>
                <tbody>
                    <tr><td>INV-2026-00012</td><td>Acme Sdn Bhd</td><td>2026-09-15</td><td>RM 5,830.00</td><td>RM 5,830.00</td><td><span class="badge badge-paid">paid</span></td></tr>
                    <tr class="row-warning"><td>INV-2026-00013</td><td>Bright Ideas Sdn Bhd</td><td>2026-08-15</td><td>RM 3,400.00</td><td>RM 0.00</td><td><span class="badge badge-unpaid">unpaid</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <p><strong>To get started:</strong> click <em>+ Create Invoice</em> (or use the same shortcut from a lease's page, which pre-selects it), fill in the billing period and at least one line item, then record payments against it as they come in from the invoice's own page.</p>
</div>

<div class="help-section" id="maintenance">
    <h2>Maintenance</h2>
    <p>Tenant-reported issues against a unit. A ticket's tenant is filled in automatically from the unit's active lease. Assigning a ticket or marking it resolved/closed notifies the assignee or tenant respectively (see Notifications below).</p>

    <div class="help-screenshot">
        <div class="help-screenshot-bar">combo_wip/maintenance</div>
        <div class="help-screenshot-body">
            <div class="page-header">
                <h1 style="margin:0;">Maintenance Tickets</h1>
                <a class="btn btn-primary" style="pointer-events:none;">+ Report Issue</a>
            </div>
            <table class="data-table">
                <thead><tr><th>#</th><th>Category</th><th>Unit</th><th>Assigned</th><th>Status</th></tr></thead>
                <tbody>
                    <tr class="row-warning"><td>#41</td><td>Aircond</td><td>Menara Combo — 12-03</td><td>Unassigned</td><td><span class="badge badge-open">open</span></td></tr>
                    <tr><td>#40</td><td>Plumbing</td><td>Plaza Sentosa — 3-01</td><td>System Administrator</td><td><span class="badge badge-resolved">resolved</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <p><strong>To get started:</strong> click <em>+ Report Issue</em> (or use the shortcut from a unit's page), pick a category, describe the problem, and optionally attach a photo. From the ticket's page, assign it to staff, add a cost once repaired, and leave activity notes as it progresses.</p>
</div>

<div class="help-section" id="documents">
    <h2>Documents</h2>
    <p>A general-purpose attachment library shared across buildings, tenants, leases, and units — separate from the typed lease documents and unit photo galleries on those pages. This page lists every document uploaded anywhere in the system.</p>

    <div class="help-screenshot">
        <div class="help-screenshot-bar">combo_wip/documents</div>
        <div class="help-screenshot-body">
            <div class="page-header">
                <h1 style="margin:0;">Documents</h1>
            </div>
            <table class="data-table">
                <thead><tr><th>Title</th><th>Attached to</th><th>Type</th><th>Expiry</th></tr></thead>
                <tbody>
                    <tr><td>Fire Safety Certificate</td><td>Menara Combo</td><td>Building</td><td>2027-01-01</td></tr>
                    <tr><td>SSM Registration</td><td>Acme Sdn Bhd</td><td>Tenant</td><td>—</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <p><strong>To get started:</strong> there's no upload form here — open the relevant building, tenant, lease, or unit page and use its own "Documents" section to attach a file (with a title and optional expiry date). This page is only for browsing and exporting everything at once.</p>
    <div class="help-note">This module has no CSV import: every row references an uploaded file, so bulk-creating rows from a spreadsheet wouldn't have anything to link to. Export still works, for an audit list of what's on file.</div>
</div>

<div class="help-section" id="notifications">
    <h2>Notifications</h2>
    <p>A delivery log, not a live inbox — this app doesn't send real email/SMS/push. Lease, invoice, and maintenance events queue an entry here automatically (e.g. "Invoice due", "Payment received", "Ticket assigned"); you deliver it yourself through your own channel and then mark it sent or failed.</p>

    <div class="help-screenshot">
        <div class="help-screenshot-bar">combo_wip/notifications</div>
        <div class="help-screenshot-body">
            <div class="page-header">
                <h1 style="margin:0;">Notifications</h1>
                <a class="btn btn-primary" style="pointer-events:none;">+ Queue Notification</a>
            </div>
            <table class="data-table">
                <thead><tr><th>Recipient</th><th>Channel</th><th>Subject</th><th>Status</th></tr></thead>
                <tbody>
                    <tr><td>Acme Sdn Bhd</td><td>EMAIL</td><td>Invoice INV-2026-00013 — payment due</td><td><span class="badge badge-queued">queued</span></td></tr>
                    <tr><td>System Administrator</td><td>EMAIL</td><td>Maintenance ticket #41 assigned to you</td><td><span class="badge badge-sent">sent</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <p><strong>To get started:</strong> most entries appear on their own as you use the app. Use <em>+ Queue Notification</em> only for one-off messages you want to track manually; open any entry to mark it sent (records the time) or failed once you've actually delivered it.</p>
</div>

<div class="help-section" id="csv">
    <h2>CSV import/export</h2>
    <p>Every module above except Documents has an <strong>Export CSV</strong> / <strong>Import CSV</strong> toolbar under its title. A few rules apply everywhere:</p>
    <ul>
        <li><strong>Export first as your template.</strong> The columns exactly match what import expects, including an <code>id</code> column — leave it blank (or ignore it) when re-importing, since import always creates new rows and never updates existing ones by id.</li>
        <li><strong>Foreign keys are numeric ids, not names.</strong> A units import needs a real <code>floor_id</code>; a leases import needs a real <code>unit_id</code> and <code>tenant_id</code>. Export the parent module first to find the id you need.</li>
        <li><strong>Bad rows are skipped, not fatal.</strong> A row with a missing required field or an id that doesn't exist is counted and skipped; the rest of the file still imports, and you'll see a summary like "Imported 8, skipped 2".</li>
        <li><strong>Imports don't trigger notifications.</strong> Bulk-loading historical leases, invoices, or tickets from a spreadsheet won't spam the Notifications log the way creating them one at a time in the UI does.</li>
    </ul>
</div>
