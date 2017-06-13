<?php

/**
 * This file is part of the Twig Gettext utility.
 *
 *  (c) Saša Stamenković <umpirsky@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Default namespace
namespace Twig\Gettext;

// Include extensions
require_once "Extensions/Formatter.class.php";

// Use filesystem
use Symfony\Component\Filesystem\Filesystem;

/**
 * Extracts translations from twig templates.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Extractor
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * Template cached file names.
     *
     * @var string[]
     */
    protected $templates;

    /**
     * Gettext parameters.
     *
     * @var string[]
     */
    protected $parameters;

    public function __construct(\Twig_Environment $environment)
    {
        // Set twig environment
        $this->environment = $environment;

        // Add extension to twig environment
        $this->environment->addExtension(new \Twig\Gettext\Extensions\Formatter());

        // Reset
        $this->reset();
    }

    protected function reset()
    {
        $this->templates = array();
        $this->parameters = array();
    }

    public function addTemplate($path)
    {
        $this->environment->loadTemplate($path);
        $this->templates[] = $this->environment->getCacheFilename($path);
    }

    public function addGettextParameter($parameter)
    {
        $this->parameters[] = $parameter;
    }

    public function setGettextParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function extract()
    {
        $command = 'xgettext';
        $command .= ' '.implode(' ', $this->parameters);
        $command .= ' '.implode(' ', $this->templates);

        $error = 0;
        $output = system($command, $error);
        if (0 !== $error) {
            throw new \RuntimeException(sprintf(
                'Gettext command "%s" failed with error code %s and output: %s',
                $command,
                $error,
                $output
            ));
        }

        $this->reset();
    }

    public function __destruct()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->environment->getCache());
    }
}
