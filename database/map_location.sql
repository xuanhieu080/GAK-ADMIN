UPDATE districts d
    JOIN provinces p ON d.province_code = p.code
    SET d.province_id = p.id;

UPDATE wards w
    JOIN districts d ON w.district_code = d.code
    SET w.district_id = d.id;