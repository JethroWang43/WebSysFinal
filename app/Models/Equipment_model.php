<?php
namespace App\Models;

use CodeIgniter\Model;

class Users_model extends Model {
    protected $table      = 'tbequipment';

    protected $primaryKey = 'idequipment';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = [
        'equipment_id',
        'name',
        'description',
        'category',
        'status',
        'location',
        'last_updated'
    ];

    protected bool $allowEmptyInserts = false;
}
?>