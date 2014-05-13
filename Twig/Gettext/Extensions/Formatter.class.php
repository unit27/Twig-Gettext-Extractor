<?php
/*******************************************************************************
 * Name: Twig extension: formatter
 * Version: 1.0
 * Author: Przemyslaw Ankowski (przemyslaw.ankowski@gmail.com)
 ******************************************************************************/


// Default namespace
namespace Twig\Gettext\Extensions;


/**
 * Twig formatter
 */
class Formatter extends \Twig_Extension
{
    /**
     * Get Twig extension name
     *
     * @return string
     */
    public function getName() {
        return "formatter";
    }

    /**
     * Twig extension filters definition
     *
     * @return array
     */
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter("format_bytes", array($this, "formatBytes"))
        );
    }

    /**
     * Format bytes to human readable format
     *
     * @param $bytes
     * @return string
     */
    public function formatBytes($bytes) {
        // JEDEC memory standard
        $units = array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB");

        // 0 bytes - doh!
        if ($bytes == 0) {
            return $bytes . " " . $units[0];
        }

        // Temporary value
        $i = (int)\floor(\log($bytes) / \log(1024));

        // Return space usage in good format + prefix
        return \round($bytes / \pow(1024, $i), 2) . " " . $units[$i];
    }
}