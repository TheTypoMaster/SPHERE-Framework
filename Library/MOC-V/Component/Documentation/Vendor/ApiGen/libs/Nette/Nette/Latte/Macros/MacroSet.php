<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Nette\Latte\Macros;

use Nette;
use Nette\Latte;
use Nette\Latte\MacroNode;

/**
 * Base IMacro implementation. Allows add multiple macros.
 *
 * @author     David Grudl
 */
class MacroSet extends Nette\Object implements Latte\IMacro
{

    /** @var Latte\Compiler */
    private $compiler;

    /** @var array */
    private $macros;


    public function __construct( Latte\Compiler $compiler )
    {

        $this->compiler = $compiler;
    }

    public static function install( Latte\Compiler $compiler )
    {

        return new static( $compiler );
    }

    public function addMacro( $name, $begin, $end = null, $attr = null )
    {

        $this->macros[$name] = array( $begin, $end, $attr );
        $this->compiler->addMacro( $name, $this );
        return $this;
    }

    /**
     * Initializes before template parsing.
     *
     * @return void
     */
    public function initialize()
    {
    }


    /**
     * Finishes template parsing.
     *
     * @return array(prolog, epilog)
     */
    public function finalize()
    {
    }


    /**
     * New node is found.
     *
     * @return bool
     */
    public function nodeOpened( MacroNode $node )
    {

        if ($this->macros[$node->name][2] && $node->htmlNode) {
            $node->isEmpty = true;
            $this->compiler->setContext( Latte\Compiler::CONTEXT_DOUBLE_QUOTED );
            $res = $this->compile( $node, $this->macros[$node->name][2] );
            $this->compiler->setContext( null );
            if (!$node->attrCode) {
                $node->attrCode = "<?php $res ?>";
            }
        } else {
            $node->isEmpty = !isset( $this->macros[$node->name][1] );
            $res = $this->compile( $node, $this->macros[$node->name][0] );
            if (!$node->openingCode) {
                $node->openingCode = "<?php $res ?>";
            }
        }
        return $res !== false;
    }

    /**
     * Generates code.
     *
     * @return string
     */
    private function compile( MacroNode $node, $def )
    {

        $node->tokenizer->reset();
        $writer = Latte\PhpWriter::using( $node, $this->compiler );
        if (is_string( $def )/*5.2* && substr($def, 0, 1) !== "\0"*/) {
            return $writer->write( $def );
        } else {
            return Nette\Callback::create( $def )->invoke( $node, $writer );
        }
    }

    /**
     * Node is closed.
     *
     * @return void
     */
    public function nodeClosed( MacroNode $node )
    {

        $res = $this->compile( $node, $this->macros[$node->name][1] );
        if (!$node->closingCode) {
            $node->closingCode = "<?php $res ?>";
        }
    }

    /**
     * @return Latte\Compiler
     */
    public function getCompiler()
    {

        return $this->compiler;
    }

}
