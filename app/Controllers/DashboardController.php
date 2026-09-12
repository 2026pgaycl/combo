<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        if (Auth::role() === 'tenant') {
            $this->redirect('/portal');
        }

        $stats = [
            'buildings' => Database::selectOne('SELECT COUNT(*) AS c FROM buildings')['c'],
            'units_total' => Database::selectOne('SELECT COUNT(*) AS c FROM units')['c'],
            'units_occupied' => Database::selectOne('SELECT COUNT(*) AS c FROM units WHERE status = "occupied"')['c'],
            'active_leases' => Database::selectOne('SELECT COUNT(*) AS c FROM leases WHERE status = "active"')['c'],
            'open_tickets' => Database::selectOne('SELECT COUNT(*) AS c FROM maintenance_tickets WHERE status IN ("open","in_progress")')['c'],
            'leases_expiring_soon' => Database::selectOne(
                'SELECT COUNT(*) AS c FROM leases WHERE status = "active" AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)'
            )['c'],
            'units_without_photos' => Database::selectOne(
                'SELECT COUNT(*) AS c FROM units u WHERE NOT EXISTS (SELECT 1 FROM unit_photos p WHERE p.unit_id = u.id)'
            )['c'],
            'lease_documents_expiring_soon' => Database::selectOne(
                'SELECT COUNT(*) AS c FROM lease_documents WHERE expiry_date IS NOT NULL AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)'
            )['c'],
        ];

        $occupancyRate = $stats['units_total'] > 0
            ? round(($stats['units_occupied'] / $stats['units_total']) * 100, 1)
            : 0;

        $this->view('dashboard.index', [
            'user' => Auth::user(),
            'stats' => $stats,
            'occupancyRate' => $occupancyRate,
        ]);
    }
}
