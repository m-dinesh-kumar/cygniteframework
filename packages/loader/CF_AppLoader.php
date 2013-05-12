<?php if ( ! defined('CF_BASEPATH')) exit('Direct script access not allowed');
/*
         *===============================================================================================
         *  An open source application development framework for PHP 5.2 or newer
         *
         * @Package                         :
         * @Filename                       :
         * @Description                   :
         * @Autho                            : Appsntech Dev Team
         * @Copyright                     : Copyright (c) 2013 - 2014,
         * @License                         : http://www.appsntech.com/license.txt
         * @Link	                          : http://appsntech.com
         * @Since	                          : Version 1.0
         * @Filesource
         * @Warning                      : Any changes in this library can cause abnormal behaviour of the framework
         * ===============================================================================================
         */

CF_AppRegistry::import('loader', 'AppLibraryRegistry',CF_BASEPATH);

require_once CF_BASEPATH.DS.'loader'.DS.'IRegistry'.EXT;

CF_AppRegistry::import('database', 'Connect',CF_BASEPATH);
CF_AppRegistry::import('database', 'ActiveRecords',CF_BASEPATH);

  class CF_ApplicationModel extends CF_DBConnect
        {
                public static $dbobj = NUll;
               public  function __construct()
                {
                         parent::cnXN();
                       // var_dump(parent::get_cnXn_obj('test'));
                }

                public static function get_db_instance()
                {
                    if(is_null(self::$dbobj)):
                         parent::cnXN();
                          return $this;
                    endif;


               }
                 function __destruct() {
                      //  parent::flushcnXn();
                }
        }

class CF_AppLoader extends CF_AppLibraryRegistry implements IRegistry
{
                      var $directory = NULL;
                      private $data = array();
                      protected $loaded = NULL;
                      public $model = NULL;

                     public function __construct($registry = array())
                    {
                            $excfres = 60*60*24*14;
                            header("Pragma: public");
                            header("Cache-Control: maxage=".$excfres);
                            header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$excfres) . ' GMT');
                   }

                    //prevent clone.
                    public function __clone(){}


                    public function __get($key)
                    {
                            if (isset($this->data[$key]))
                                return $this->data[$key];
                    }

                    public function __set($key, $value)
                    {
                            $this->data[$key] = $value;
                    }

                    /*
                    * This function is to load requested model file
                    * @param string (model name)
                    *
                    */
                     public function model($model_name)
                      {
                        $this->directory = APPPATH."models".DS;
                                if(is_readable($this->directory.$model_name.EXT)) :

                                                 require_once($this->directory.$model_name.EXT);

                                         	 $modelname = $this->make_model($model_name);
                                                if (empty($this->data))
                                                       $modelobj = new $modelname();													   $this->__set($model_name, $modelobj);
                                                $this->_set_object($model_name, $modelobj);
                                else:
                                    throw new ErrorException("Unable to load Requested file ".$model_name.EXT);
                                endif;
                     }

                    private function _set_object($key,$value)
                    {
                            $this->data[$key] = $value;

                    }

                     private function make_model($modelname)
                     {
                            if(!empty($modelname))
                                     $standard_model_name = ucfirst($modelname).ucfirst(str_replace('/', '',APPPATH)).'Model';
                            return $standard_model_name;
                     }



                    function print_gzipped_output()
                    {
                            $HTTP_ACCEPT_ENCODING = $_SERVER["HTTP_ACCEPT_ENCODING"];
                            if(headers_sent())
                                $encoding = false;
                            else if( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false )
                                $encoding = 'x-gzip';
                            else if( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false )
                                $encoding = 'gzip';
                            else
                                $encoding = false;

                            if( $encoding ):
                                    $contents = ob_get_clean();
                                    $_temp1 = strlen($contents);
                                    if ($_temp1 < 2048) :   // no need to waste resources in compressing very little data
                                                print($contents);
                                    else:
                                                header('Content-Encoding: '.$encoding);
                                                print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
                                                $contents = gzcompress($contents, 9);
                                                $contents = substr($contents, 0, $_temp1);
                                                print($contents);
                                    endif;
                            else:
                                ob_end_flush();
                            endif;
                    }

                    /*
                    * This function is to load requested view file
                    * @param string (view name)
                    *
                    */
                   protected  function render($view_name ="",$arrValues=array(), $ui_content =NULL)
                  {
                        $this->directory = APPPATH.'views'.DS;
                        if(is_readable($this->directory.$view_name.EXT)) :
                                    ob_start();
                                    if(is_array($arrValues))
                                        extract($arrValues);
                                    require_once($this->directory.$view_name.EXT);

                                    $output=ob_get_contents();
                                    ob_get_clean();

                                    if(isset($ui_content) && $ui_content === "ui_contents")
                                            return $output;
                                    else
                                           echo $output;
                                     ob_end_flush();
                         else:
                                throw new ErrorException("Unable to load Requested file ".$view_name.EXT);
                         endif;

                     }
                    public function request($key)
                    {
                          if(!is_null($key) || $key != ""):
                              try{
                                      CF_AppRegistry::load_lib_class('CF_'.$key);
                              }catch(Exception $ex){
                                    $ex->getMessage();
                              }
                                 $this->loaded = CF_AppRegistry::load($key);

                                 return $this->loaded;
                       else:
                                  throw new ErrorException ("Invalid argument passed on ".__FUNCTION__);
                       endif;
                    }
                 /*
                *  Load Helper file
                *
                */
                  public   function helper($file_name,$prefix = FRAMEWORK_PREFIX)
                  {
                        $this->directory = CF_BASEPATH.DS."helpers".DS;
                                if(file_exists($this->directory.$prefix.$file_name.EXT) && is_readable($this->directory.$prefix.$file_name.EXT))
                                         require_once($this->directory.$prefix.$file_name.EXT);
                                else
                                    throw new ErrorException("Unable to load Requested file ".$file_name.EXT);
                        unset($file_name);
                }

                public function __destruct()
                {
                        ob_end_flush(); //ob_end_clean();
                        ob_get_flush(); unset($this->loaded);
                        unset($this->directory); unset($this);
                  }
}