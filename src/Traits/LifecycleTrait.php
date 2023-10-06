<?php

namespace App\Traits;

/**
 * Lifecycletrait
 * 
 * This trait allows to give default values to properties of 
 * an entity before his data has udated or create on database
 */
trait LifecycleTrait
{
    use PrePersistTrait;
    use PreUpdateTrait;
}
