<?php

namespace League\CommonMark\Util;

/**
 * Implement this class to inject the configuration where needed
 */
interface ConfigurationAwareInterface
{
    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration);
}
