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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Installation;

use Antares\Contracts\Installation\Installation as InstallationContract;
use Antares\Contracts\Installation\Requirement as RequirementContract;
use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Installation\Scripts\WatchDog;

class InstallerServiceProvider extends ModuleServiceProvider
{

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Installation\Http\Controllers';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InstallationContract::class, function ($app) {
            return new Installation($app);
        });

        $this->app->bind(RequirementContract::class, function ($app) {
            return new Requirement($app);
        });
        $this->app->singleton('antares.watchdog', function($app) {
            return new WatchDog($app->make('config'));
        });
    }

    /**
     * Boot extension components.
     *
     * @return void
     */
    public function bootExtensionComponents()
    {
        $path = realpath(__DIR__ . '/../resources');
        $this->addViewComponent('installer', 'antares/installer', "{$path}/views");
        $this->addLanguageComponent('antares/installer', 'antares/installer', "{$path}/lang");
        $this->addConfigComponent('antares/installer', 'antares/installer', $path . '/config');
    }

    /**
     * Load extension routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $path = realpath(__DIR__ . '/../');
        $this->loadBackendRoutesFrom("{$path}/src/routes.php");
    }

}
