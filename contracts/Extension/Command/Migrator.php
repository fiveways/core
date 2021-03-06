<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
 namespace Antares\Contracts\Extension\Command;

use Illuminate\Support\Fluent;
use Antares\Contracts\Extension\Listener\Migrator as Listener;

interface Migrator
{
    /**
     * Update/migrate an extension.
     *
     * @param  \Antares\Contracts\Extension\Listener\Migrator  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function migrate(Listener $listener, Fluent $extension);
}
