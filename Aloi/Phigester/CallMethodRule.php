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
 * Rule implementation that calls a method on an object on the stack
 * (normally the top/parent object), passing arguments collected from
 * subsequent CallParamRule rules or from the body of this element
 *
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
class Aloi_Phigester_CallMethodRule extends Aloi_Phigester_Rule {
  /**
   * The body text collected from this element
   * 
   * @var string
   */
  protected $bodyText = null;
  
  /**
   * Location of the target object for the call, relative to the top of the
   * digester object stack
   * 
   * The default value of zero means the target object is the one on top
   * of the stack.
   * 
   * @var integer
   */
  protected $targetOffset = 0;
  
  /**
   * The method name to call on the parent object
   *
   * @var string
   */
  protected $methodName = null;
  
  /**
   * The number of parameters to collect from MethodParam rules
   * 
   * If this value is zero, a single parameter will be collected from the
   * body of this element.
   *
   * @var integer
   */
  protected $paramCount = 0;
  
  /**
   * The parameter types of the parameters to be collected
   *
   * @var array
   */
  protected $paramTypes = null;
  
  /**
   * Construct a "call method" rule with the specified method name
   * 
   * If paramCount is set to zero the rule will use the body of this element
   * as the single argument of the method, unless paramTypes is empty, in this
   * case the rule will call the specified method with no arguments.
   *
   * @param integer $targetOffset Location of the target object. Positive
   * numbers are relative to the top of the digester object stack. Negative
   * numbers are relative to the bottom of the stack. Zero implies the top
   * object on the stack.
   * @param string $methodName Method name of the parent method to call
   * @param integer $paramCount The number of parameters to collect, or
   * zero for a single argument from the body of the element
   * @param array $paramTypes The PHP types that represent the parameter types
   * of the method arguments
   */
  public function __construct($targetOffset, $methodName, $paramCount = 0
      , $paramTypes = null) {
    $this->targetOffset = (integer) $targetOffset;
    $this->methodName = (string) $methodName;
    $this->paramCount = (integer) $paramCount;

    if (is_null($paramTypes) || !is_array($paramTypes)) {
      if ($paramCount == 0) {
        $this->paramTypes = array('string');
      } else {
        $this->paramTypes = array_fill(0, $this->paramCount, 'string');
      }
    } else {
      if (empty($paramTypes)) {
        if ($this->paramCount > 0) {
          $this->paramTypes = array_fill(0, $this->paramCount, 'string');        
        } else {
          $this->paramTypes = array();
        }
      } else {
        foreach ($paramTypes as $paramType) {
          $this->paramTypes[] = (string) $paramType;
        }
      }
    }
  }
  
  /**
   * Process the beginning of this element
   *
   * @param array $attributes The attribute list of this element
   * @throws Exception
   */
  public function begin(array $attributes) {
    //Push an array to capture the parameter values if necessary
    if ($this->paramCount > 0) {
      $parameters = array_fill(0, $this->paramCount, null);
      $this->digester->pushParams($parameters);
    }
  }
    
  /**
   * Process the body text of this element
   * 
   * @param string $bodyText The text of the body of this element
   */
  public function body($bodyText) {
    if ($this->paramCount == 0) {
      $this->bodyText = trim($bodyText);
    }
  }
  
  /**
   * Process the end of this element
   */
  public function end() {
    $logger = $this->digester->getLogger();
    $indentLogger = $this->digester->getIndentLogger();
    $match = $this->digester->getMatch();
    
    //Retrieve or construct the parameter values array
    $parameters = null;
    if ($this->paramCount > 0) {
      $parameters = $this->digester->popParams();
      
      foreach ($parameters as $i => $parameter) {
        if (is_object($parameter)) {
          $logger->debug($indentLogger . '  [CallMethodRule](' . $i . ')'
              . get_class($parameter));
        } else {
          $logger->debug($indentLogger . '  [CallMethodRule](' . $i . ')'
              . $parameter);
        }
      }
      
      //In the case where the target method takes a single parameter
      //and that parameter does not exist (the CallParamRule never
      //executed or the CallParamRule was intended to set the parameter
      //from an attribute but the attribute wasn't present etc) then
      //skip the method call.
      //
      //This is useful when a class has a "default" value that should
      //only be overridden if data is present in the XML. I don't
      //know why this should only apply to methods taking *one*
      //parameter, but it always has been so we can't change it now.
      if ($this->paramCount == 1 && is_null($parameters[0])) {
        return;
      }
    } elseif (!empty($this->paramTypes)) {
      //Having paramCount == 0 and count(paramTypes) == 1 indicates
      //that we have the special case where the target method has one
      //parameter being the body text of the current element.
      
      //There is no body text included in the source XML file, so skip
      //the method call
      if ($this->bodyText == '') {
        return;
      }
      
      $parameters = array($this->bodyText);
    } else {
      //When paramCount is zero and count(paramTypes) is zero it means that
      //we truly are calling a method with no parameters. Nothing special
      //need to be done here.
      ;
    }
    
    //Construct the parameter values array we will need
    try {
      $paramValues = array();
      $countParamTypes = count($this->paramTypes);
      for ($i = 0; $i < $countParamTypes; $i++) {
        if (array_key_exists($i, $parameters)) {
          $parameter = $parameters[$i];
          if (!is_object($parameter))
            $parameter = Aloi_Phigester_ConvertUtils::convert($parameter
                , $this->paramTypes[$i]);
          $paramValues[$i] = $parameter;
        } else {
          $parameter = null;
          $parameter = Aloi_Phigester_ConvertUtils::convert($parameter
              , $this->paramTypes[$i]);
          $paramValues[$i] = $parameter;
        }
      }
    } catch (Aloi_Phigester_Exception_ConversionException $exception) {
      throw $exception;
    }
    
    //Determine the target object for the method call
    if ($this->targetOffset >= 0) {
      $target = $this->digester->peek($this->targetOffset);
    } else {
      $target = $this->digester->peek($this->digester->getCount()
          + $this->targetOffset);
    }
    
    if (is_null($target)) {
      $sb = '[CallMethodRule]{' . $match;
      $sb .= '} Call target is null (';
      $sb .= 'targetOffset=' . $this->targetOffset;
      $sb .= ',stackdepth=' . $this->digester->getCount();
      $sb .= ')';
      throw new Aloi_Phigester_Exception_ExpatParserException($sb);
    }
    
    //Invoke the required method on the top object
    $sb = $indentLogger . '  [CallMethodRule]{' . $match;
    $sb .= '} Call ' . get_class($target);
    $sb .= '->' . $this->methodName . '(';
    foreach ($paramValues as $i => $paramValue) {
      if ($i > 0) {
        $sb .= ',';
      }
      if (is_null($paramValue)) {
        $sb .= 'null';
      } else {
        if (is_object($paramValue)) {
          $sb .= get_class($paramValue);
        } else {
          $sb .= $paramValue;
        }
      }
      $sb .= '/' . $this->paramTypes[$i];
    }
    $sb .= ')';
    $logger->debug($sb);
    
    if (method_exists($target, $this->methodName)) {
      $result = call_user_func_array(array($target, $this->methodName)
          , $paramValues);
    } else {
      $msg = 'Class "' . get_class($target) . '" has no method named "'
          . $this->methodName . '"';
      throw new Aloi_Phigester_Exception_NoSuchMethodException($msg);
    }
  }
  
  /**
   * Subclasses may override this method to perform additional processing of
   * the invoked method's result
   *
   * @param mixed $result Result returned by the method invoked, possibly null
   */
  protected function processMethodCallResult($result) {
    //do nothing
  }
  
  /**
   * Render a printable version of this Rule
   * 
   * @return string
   */
  public function toString() {
    $sb = 'CallMethodRule[';
    $sb .= 'methodName=' . $this->methodName;
    $sb .= ', paramCount=' . $this->paramCount;
    $sb .= ', paramTypes={';
    foreach ($this->paramTypes as $i => $paramType) {
      if ($i > 0) $sb .= ', ';
      $sb .= $paramType;
    }
    $sb .= '}]';
    return $sb;
  }
}
?>
