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


namespace Antares\Asset;

use Symfony\Component\Finder\Finder;
use Assetic\Factory\LazyAssetManager;
use Assetic\AssetWriter;
use Assetic\AssetManager;
use Assetic\Extension\Twig\TwigResource;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\FileViewFinder;

class Dumper
{

    /**
     * @var AssetManager
     */
    protected $am;

    /**
     * @var AssetWriter
     */
    protected $writer;

    /**
     * @var LazyAssetManager
     */
    protected $lam;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Twig_Loader_Filesystem
     */
    protected $loader;

    /** @var  FileViewFinder */
    protected $viewfinder;

    /**
     * Ctor
     *
     * @param AssetManager     $am
     * @param LazyAssetManager $lam
     * @param AssetWriter      $writer
     * @param FileViewFinder   $viewfinder
     */
    public function __construct(AssetManager $am, LazyAssetManager $lam, AssetWriter $writer, FileViewFinder $viewfinder)
    {
        $this->viewfinder = $viewfinder;
        $this->am         = $am;
        $this->lam        = $lam;
        $this->writer     = $writer;
    }

    /**
     * @param \Twig_Environment       $twig
     * @param \Twig_Loader_Filesystem $loader
     */
    public function setTwig(\Twig_Environment $twig, \Twig_LoaderInterface $loader)
    {
        $this->twig   = $twig;
        $this->loader = $loader;
    }

    /**
     * Locates twig templates and adds their defined assets to the lazy asset manager
     */
    public function addTwigAssets()
    {
        if (!$this->twig instanceof \Twig_Environment) {
            throw new \LogicException('Twig environment not set');
        }
        $finder     = new Finder();
        $viewfinder = $this->viewfinder;
        if (count($viewfinder->getPaths()) > 0) {
            $iterator = $finder->files()->in($viewfinder->getPaths());
            foreach ($iterator as $file) {
                $resource = new TwigResource($this->loader, $file->getRelativePathname());
                $this->lam->addResource($resource, 'twig');
            }
        }
    }

    /**
     * Dumps all the assets
     */
    public function dumpAssets()
    {
        $this->dumpManagerAssets($this->am);
        $this->dumpManagerAssets($this->lam);
    }

    /**
     * Dumps the assets of given manager
     *
     * Doesn't use AssetWriter::writeManagerAssets since we also want to dump non-combined assets
     * (for example, when using twig extension in debug mode).
     *
     * @param AssetManager $am
     */
    protected function dumpManagerAssets(AssetManager $am)
    {
        foreach ($am->getNames() as $name) {
            $asset = $am->get($name);
            if ($am instanceof LazyAssetManager) {
                $formula = $am->getFormula($name);
            }
            $this->writer->writeAsset($asset);
            if (!isset($formula[2])) {
                continue;
            }
            $debug   = isset($formula[2]['debug']) ? $formula[2]['debug'] : $am->isDebug();
            $combine = isset($formula[2]['combine']) ? $formula[2]['combine'] : null;
            if (null !== $combine ? !$combine : $debug) {
                foreach ($asset as $leaf) {
                    $this->writer->writeAsset($leaf);
                }
            }
        }
    }

}
