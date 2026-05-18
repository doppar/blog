<?php

namespace App\Models;

use Phaseolies\Database\Entity\Model;

class Category extends Model
{
    protected $creatable = ["name", "excerpt", "status"];
}
