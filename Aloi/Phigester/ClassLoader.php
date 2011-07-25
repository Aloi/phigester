<?php
/* Copyright 2010 aloi-project 
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA
 *
 * This file incorporates work covered by the following copyright and 
 * permissions notice:
 * 
 * Copyright (C) 2007 Phigester
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * You may obtain a copy of the License at
 * 	
 * 		http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * This file incorporates work covered by the following copyright and
 * permission notice:
 *
 * Copyright 2004 The Apache Software Foundation
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * The ClassLoader is a way of customizing the way PHP gets its classes
 * and loads them into memory
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
class Aloi_Phigester_ClassLoader {
  protected static $phpExtensionFile = '.php';
  
  /**
   * @param string $phpExtensionFile
   */
  public static function setPhpExtensionFile($phpExtensionFile) {
    self::$phpExtensionFile = (string) $phpExtensionFile;
  }
  
  /**
   * Check if a fully qualified class name is valid
   *
   * @param string $name Fully qualified name of a class (with packages)
   * @return boolean Return true if the class name is valid
   */
  public static function isValidClassName($name) {
    $classPattern = '`^((([A-Z]|[a-z]|[0-9]|\_|\-)+\:{2})*)';
    $classPattern .= '(([A-Z]|[a-z]){1}([A-Z]|[a-z]|[0-9]|\_)*)$`';
    return (boolean) preg_match($classPattern, $name);
  }
  
  /**
   * Return only the class name of a fully qualified name
   *
   * @param string $name Fully qualified name of a class (with packages)
   * @return string
   */
  public static function getClassName($name) {
    $lastDot = strrpos($name, '::');
    if ($lastDot === false) {
      $className = $name;
    } else {
      $className = substr($name, -(strlen($name) - $lastDot - 2));
    }
    return $className;
  }
  
  /**
   * Load a class
   * 
   * @param string $name The fully qualified name of the class (with packages)
   * @return string Return the only class name
   * @throws Aloi_Phigester_Exception_IllegalArgumentException
   * @throws Aloi_Phigester_Exception_ClassNotFoundException
   */
  public static function loadClass($name) {
    //Check if the fully qualified class name is valid
    if (!self::isValidClassName($name))
      throw new Aloi_Phigester_Exception_IllegalArgumentException('Illegal class name ' . $name);
    
    //Get only the class name
    $className = self::getClassName($name);
    
    //Have we already loaded this class?
    if (class_exists($className)) {
      return $className;
    } else {
      //Try to load the class
      $pathClassFile = str_replace('::', '/', $name) . self::$phpExtensionFile;
      if (@include_once($pathClassFile)) {
        if (class_exists($className)) {
          return $className;
        } else {
          $msg = '"' . $name . '" class does not exist.';
          throw new Aloi_Phigester_Exception_ClassNotFoundException($msg);
        }        
      } else {
        $msg = 'PHP class file "' . $pathClassFile . '" does not exist.';
        throw new Aloi_Phigester_Exception_ClassNotFoundException($msg);
      }      
    }
  }
}
?>
