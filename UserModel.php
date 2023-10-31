<?php

namespace thecodeholic\phpmvc;

use thecodeholic\phpmvc\db\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}
