<?php

/**
 * Karambit Killboard System
 *
 * Handles the creation and caching of Dwoo Templates. Adapted from a version on the Dwoo Wiki found 
 * at http://wiki.dwoo.org/index.php/ExtendingDwoo on March 1, 2009
 * 
 *
 * PHP version 5
 *
 * LICENSE: This file is part of EVE MultiPurpose Application also know as KarambitKS.
 * KarambitKS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * KarambitKS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with KarambitKS.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Michael Cummings <mgcummings@yahoo.com>
 * @author     Stephen Gulickk <stephenmg12@gmail.com>
 * @author     Andy Snowden <forumadmin@eve-razor.com>
 * @copyright  2009 (C) Michael Cummings, Stephen Gulick, and Andy Snowden 
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    KarambitKS
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/karambitks/
 * @link       http://www.eve-online.com/
 */

/**
 * TemplateHandler
 * 
 * @package Karambit
 * @subpackage Dwoo
 * @author Stephen Gulick
 * @copyright 2009
 * @version $Id$
 * @access public
 * 
 * @link http://wiki.dwoo.org/
 */
class TemplateHandler extends Dwoo
{
    // stores a dwoo instance
    protected static $instance;
    // stores a compiler instance
    protected static $cmpInstance;
 
    // singleton accessor, use it to access this object if you need to call specific Dwoo functions
    public static function getInstance()
    {
        // if it's not set, we create an object and set the default compiler factory function to the custom one
        // you have to change "MyTemplate" by this class's name if you change it or it will not work
        if(self::$instance === null)
        {
            self::$instance = new self();
            self::$instance->setCacheDir(KKS_CACHE.'Dwoo_Cache');
            self::$instance->setCompileDir(KKS_CACHE.'Dwoo_Compile');
            self::$instance->setDefaultCompilerFactory('file', array('MyTemplate','compilerFactory'));
        }
        return self::$instance;
    }
 
    // creates a template object and does some automated processing before doing so
    public static function createTemplate($name, $cache = null, $cacheId = null)
    {
        // if we are in development mode, disable the cache to prevent it from 
        // hiding changes made to the php code
        if(defined('KKS_DEV_MODE')) {
            $cache = 0;
        }
        elseif(empty($cache)) {
            
            $cache=KKS_CACHE_DWOO;        
        }
            

 
        // for example you could also tweak the cache time globally for your application here.. 
        // let's say you put a cache time of 1 2 or 3 depending on the template types in your code
        // and then here you multiply it by 60 that 1 becomes 1 minutes (as Dwoo takes cache time in seconds)
        // or you could even do if($cache == 2) { $cache = 533; } ... the point is that it allows you to 
        // change it in one place for the entire application
 
        // we automatically append the language to the cache id if it's not empty, 
        // so that every page is saved in a different cache file for each language.
        // you should of course replace User::$lang everywhere in this class by some 
        // other variable of yours that represents the language
        if(empty($cacheId) === false)
            $cacheId .= User::$lang;
 
        // set the path to your template directory
        $path = '/home/moo/mysite.com/templates/';
 
        // look if there is a custom template for the current site language
        if(file_exists($path . $name . '-' . User::$lang . '.html'))
        {
            $file = $path . $name . '-' . User::$lang . '.html';
        }
        // if not then look if the default template exists
        elseif(file_exists($path . $name . '.html'))
        {
            $file = $path . $name . '.html';
        }
        // still not found, then throw an exception
        else
        {
            throw new Exception('Unable to load template <em>'.$name.'</em> from <em>'.$path.'</em>', E_USER_ERROR);
        }
 
        // return the template object
        return new Dwoo_Template_File($file, $cache, $cacheId);
    }
 
    // this function allows you to retrieve a template output
    public static function fetch(Dwoo_Template_File $tpl, array $data, $customCompiler = null)
    {
        return self::getInstance()->get($tpl, $data, $customCompiler);
    }
 
    // this function overrides the default Dwoo one to add global values accessible through {$dwoo.VALUE} in the templates
    protected function initGlobals(Dwoo_ITemplate $tpl)
    {
        // call the parent function to allow Dwoo to fill its default values
        parent::initGlobals($tpl);
        // then add yours to the array, here we set the language as an example
        //$this->globals['LANG'] = User::$lang;
    }
 
    // provides a custom compiler object when a template needs to be compiled
    public static function compilerFactory()
    {
        if(self::$cmpInstance === null)
        {
            if(class_exists('Dwoo_Compiler', false) === false)
                include 'Dwoo/Compiler.php';
            self::$cmpInstance = Dwoo_Compiler::compilerFactory();
            // here we set custom settings to the compiler, for example adding a PreProcessor
            //self::$cmpInstance->addPreProcessor('myPreProcessorFunction');
        }
        return self::$cmpInstance;
    }
}

?>