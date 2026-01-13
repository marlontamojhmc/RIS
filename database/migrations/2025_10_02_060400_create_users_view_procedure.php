<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS GetUsersWithFullDetails;
            CREATE PROCEDURE GetUsersWithFullDetails(
                IN p_status VARCHAR(50),
                IN p_department_id INT,
                IN p_division_id INT,
                IN p_offset INT,
                IN p_limit INT
            )
           BEGIN
                SELECT 
                    u.id,
                    ud.employee_id,
                    ud.first_name,
                    ud.middle_name,
                    ud.last_name,
                    ud.suffix,
                    u.email as email_address,
                    ud.status,
                    d.department_name AS department,
                    divs.division_name AS division,
                    r.name AS roles,
                    pos.position_name AS position,
                    perm.name AS permission,
                    ud.birth_date,
                    ud.sex,
                    ud.phone,
                    JSON_OBJECT(
                        'street', st.street_name,
                        'barangay', bar.code,
                        'municipality', mun.code,
                        'province', pro.code,
                        'region', reg.code
                    ) AS address,
                    u.created_at,
                    u.updated_at
                FROM users u
                LEFT JOIN user_details ud ON ud.user_id = u.id
                LEFT JOIN positions pos ON pos.id = ud.position_id
                LEFT JOIN departments d ON d.id = ud.department_id
                LEFT JOIN divisions divs ON divs.id = ud.division_id
                LEFT JOIN roles r ON r.id = ud.role_id
                LEFT JOIN permissions perm ON perm.id = ud.permission_id
                LEFT JOIN location l ON l.id = ud.user_id
                LEFT JOIN region reg ON reg.id = l.region_id
				LEFT JOIN province pro ON pro.id = l.province_id
				LEFT JOIN municipality mun ON mun.id = l.municipality_id
				LEFT JOIN barangay bar ON bar.id = l.barangay_id
				LEFT JOIN street st ON st.id = l.street_id
                WHERE (p_status IS NULL OR ud.status = p_status)
                  AND (p_department_id IS NULL OR ud.department_id = p_department_id)
                  AND (p_division_id IS NULL OR ud.division_id = p_division_id)
                ORDER BY u.created_at DESC
                LIMIT p_offset, p_limit;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetUsersWithFullDetails");
    }
};
