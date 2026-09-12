-- ============================================================
-- Combo — seed data
-- Default login after seeding: admin@example.com / password
-- CHANGE THIS PASSWORD IMMEDIATELY after first login in production.
-- ============================================================

INSERT INTO roles (slug, name) VALUES
    ('super_admin', 'Super Admin'),
    ('property_manager', 'Property Manager'),
    ('accountant', 'Accountant / Finance'),
    ('maintenance_staff', 'Maintenance Staff'),
    ('tenant', 'Tenant')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Password hash below is for the plaintext "password" (bcrypt).
-- Generated with PHP's password_hash('password', PASSWORD_DEFAULT).
INSERT INTO users (role_id, name, email, password_hash, status)
SELECT id, 'System Administrator', 'admin@example.com',
       '$2b$10$HZvAkXU21WSOHfOI1Z8ezuFZMwMHZGCD9w3H7x/ZWgQYDuZcZeT8K', 'active'
FROM roles WHERE slug = 'super_admin'
ON DUPLICATE KEY UPDATE name = VALUES(name);
