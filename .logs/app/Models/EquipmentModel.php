<?php
namespace App\Models;

use CodeIgniter\Model;

class EquipmentModel extends Model
{
    protected $table = 'tbequipment';
    protected $primaryKey = 'idequipment';
    protected $returnType = 'array';
    protected $allowedFields = [
        'equipment_id',
        'name',
        'description',
        'category',
        'status',
        'location',
        'last_updated'
    ];

    // helper to normalize DB rows to view-friendly structure
    public function normalize(array $row)
    {
        // if row already has id, leave as-is
        if (isset($row['id'])) return $row;

        $normalized = $row;
        if (isset($row['idequipment'])) {
            $normalized['id'] = $row['idequipment'];
        }
        return $normalized;
    }
}
