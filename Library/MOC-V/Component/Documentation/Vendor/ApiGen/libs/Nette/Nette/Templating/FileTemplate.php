<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Nette\Templating;

use Nette;
use Nette\Caching;

/**
 * Template stored in file.
 *
 * @author     David Grudl
 */
class FileTemplate extends Template implements IFileTemplate
{

    /** @var string */
    private $file;


    /**
     * Constructor.
     *
     * @param  string  template file path
     */
    public function __construct( $file = null )
    {

        if ($file !== null) {
            $this->setFile( $file );
        }
    }

    /**
     * Returns the path to the template file.
     *
     * @return string  template file path
     */
    public function getFile()
    {

        return $this->file;
    }

    /**
     * Sets the path to the template file.
     *
     * @param  string  template file path
     *
     * @return FileTemplate  provides a fluent interface
     */
    public function setFile( $file )
    {

        $this->file = realpath( $file );
        if (!$this->file) {
            throw new Nette\FileNotFoundException( "Missing template file '$file'." );
        }
        return $this;
    }

    /**
     * Returns template source code.
     *
     * @return string
     */
    public function getSource()
    {

        return file_get_contents( $this->file );
    }



    /********************* rendering ****************d*g**/

    /**
     * Renders template to output.
     *
     * @return void
     */
    public function render()
    {

        if ($this->file == null) { // intentionally ==
            throw new Nette\InvalidStateException( "Template file name was not specified." );
        }

        $cache = new Caching\Cache( $storage = $this->getCacheStorage(), 'Nette.FileTemplate' );
        if ($storage instanceof Caching\Storages\PhpFileStorage) {
            $storage->hint = str_replace( dirname( dirname( $this->file ) ), '', $this->file );
        }
        $cached = $compiled = $cache->load( $this->file );

        if ($compiled === null) {
            try {
                $compiled = "<?php\n\n// source file: $this->file\n\n?>".$this->compile();

            } catch( FilterException $e ) {
                $e->setSourceFile( $this->file );
                throw $e;
            }

            $cache->save( $this->file, $compiled, array(
                Caching\Cache::FILES  => $this->file,
                Caching\Cache::CONSTS => 'Nette\Framework::REVISION',
            ) );
            $cached = $cache->load( $this->file );
        }

        if ($cached !== null && $storage instanceof Caching\Storages\PhpFileStorage) {
            Nette\Utils\LimitedScope::load( $cached['file'], $this->getParameters() );
        } else {
            Nette\Utils\LimitedScope::evaluate( $compiled, $this->getParameters() );
        }
    }

}
