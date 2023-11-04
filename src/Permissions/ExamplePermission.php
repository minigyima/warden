<?php

namespace App\Permissions;

use Minigyima\Warden\Interfaces\Permission;

class ExamplePermission extends Permission
{
    public function __construct()
    {
        $this->name = 'Example Permission';
        $this->description = 'An example permission';
        $this->defaultErrorMessage = 'You do not have permission to view this resource.';
    }
}
