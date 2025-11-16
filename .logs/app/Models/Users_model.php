<?php
namespace App\Models;

use CodeIgniter\Model;

class Users_model extends Model {
    protected $table      = 'tblusers';

    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = [
        'StudentID',
        'username',
        'password',
        'fullname',
        'email',
        'datecreated'
    ];

    protected bool $allowEmptyInserts = false;
}
?>