<?php

namespace Example\Models;

use Example\Entities\Example;
use Michalsn\Uuid\UuidModel;

class ExampleModel extends UuidModel
{
    protected $table            = 'examples';
    protected $returnType       = Example::class;
    protected $allowedFields    = [];
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $uuidUseBytes     = false;
    protected $uuidVersion      = 'uuid6';

    // Validation
    protected $validationRules     = [
        // 'active'        => 'trim|permit_empty|in_list[0,1]',
        // 'title'         => 'trim|required|string|min_length[3]|max_length[32]|is_unique[permissions.title]',
        // 'slug'          => 'trim|permit_empty|max_length[32]|is_unique[permissions.slug]',
        // 'description'   => 'trim|permit_empty|max_length[255]',
    ];

    protected $validationMessages  = [];
}
