<?php

namespace Hi\Attributes;

use Attribute;
use Hi\Enums\Role;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class AllowAccess
{
    public function __construct(
        public Role $role
    ) {
    }
}
