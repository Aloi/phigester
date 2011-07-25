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
 * Digester
 *
 * A Digester processes an XML input stream by matching a
 * series of element nesting patterns to execute Rules that have been
 * added prior to the start of parsing. This package was inspired by the
 * Jakarta commons component.
 *
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @link http://jakarta.apache.org/commons/digester
 * @version $Id$
 */
class Aloi_Phigester_Digester extends Aloi_Phigester_AbstractExpatParser {
  /**
   * Logging class
   *
   * Most common logging calls.
   *
   * @var Logger
   */
  protected $logger = null;

  /**
   * String used for the indentation of logging information
   *
   * @var string
   */
  protected $indentLogger = '';
  
  /**
   * The body text of the current element
   *
   * @var string
   */
  protected $bodyText = '';

  /**
   * A stack of body text strings for surrounding elements
   *
   * @var array
   */
  protected $bodyTexts = array();

  /**
   * The current match pattern for nested element processing
   *
   * @var string
   */
  protected $match = '';

  /**
   * The "root" element of the stack
   * 
   * In other words, the last object that was popped.
   *
   * @var object
   */
  protected $root = null;

  /**
   * The object stack being constructed
   *
   * @var array
   */
  protected $stack = array();

  /**
   * The Rules implementation containing our collection of Rule instances
   * and associated matching policy
   *
   * @var Aloi_Phigester_Rules
   */
  protected $rules = null;

  /**
   * The parameters stack being utilized by CallMethodRule
   * and CallParamRule rules
   * 
   * @var array
   */
  protected $params = array();
  
  public function __construct() {
    //Call the ExpatParser constructor first
    parent::__construct();

    $this->logger = Aloi_Util_Logger_Manager::getLogger(
        'Phigester.' . __CLASS__);
  }

  /**
   * @return Logger
   */
  public function getLogger() {
    return $this->logger;
  }

  /**
   * @return string
   */
  public function getIndentLogger() {
    return $this->indentLogger;
  }
  
  /**
   * @return string
   */
  public function getMatch() {
    return $this->match;
  }
  
  /**
   * Set the Rules implementation object containing our rules collection
   * and associated matching policy
   *
   * @param Aloi_Phigester_Rules $rules New Rules implementation
   */
  public function setRules(Aloi_Phigester_Rules $rules) {
    $this->rules = $rules;
    $this->rules->setDigester($this);
  }

  /**
   * Return the Rules implementation object containing our rules collection
   * and associated matching policy
   *
   * If none has been established, a default implementation will be created
   * and returned.
   *
   * @return Aloi_Phigester_Rules
   */
  public function getRules() {
    if (is_null($this->rules)) {
      $this->rules = new Aloi_Phigester_RulesBase();
      $this->rules->setDigester($this);
    }
    return $this->rules;
  }

  /**
   * Returns the root element of the tree of objects created as a result
   * of applying the rule objects to the input XML
   *
   * @return object
   */
  public function getRoot() {
    return $this->root;
  }
  
  /**
   * Parse the content of the specified file using this digester
   *
   * Returns the root element from the object stack (if any).
   * @param string $xmlFile Name of the file containing the XML data to be
   * parsed
   * @return object
   * @throws Aloi_Phigester_Exception_ExpatParserException - If something gone wrong
   * during parsing
   * @throws Aloi_Phigester_Exception_IOException - If XML file can not be accessed
   */
  public function parse($xmlFile) {
    $this->logger->debug('---------- START PARSING "' . $xmlFile
        . '" ----------');
    try {
      parent::parse($xmlFile);
    } catch (Aloi_Phigester_Exception_IOException $exception) {
      throw $exception;
    } catch (Aloi_Phigester_Exception_ExpatParserException $exception) {
      throw $exception;
    }
    $this->logger->debug('---------- END PARSING "' . $xmlFile
        . '" ----------');
    
    return $this->root;
  }

  /**
   * Process notification of the start of an XML element being reached
   *
   * @param object $parser The XML parser object
   * @param string $name Name of the current xml element being processed
   * @param array $attribs An array of attributes for the current xml element
   * being processed
   * @throws Exception
   */
  public function startElementHandler($parser, $name, $attribs) {
    if ($this->match != '') $this->indentLogger .= str_repeat(' ', 4);
    $this->logger->debug($this->indentLogger . 'startElement(' . $name . ')');    
    $this->logger->debug($this->indentLogger . '  Pushing body text "'
        . $this->bodyText . '"');
    
    // Save the body text accumulated for our surrounding element
    $this->bodyTexts[] = $this->bodyText;
    $this->bodyText = '';
    
    // Compute the current matching rule
    $sb = $this->match;
    if ($sb != '') $sb .= '/';
    $sb .= $name;
    $this->match = $sb;
    
    $this->logger->debug($this->indentLogger . '  New match="' . $this->match
        . '"');

    // Fire "begin" events for all relevant rules
    $rules = $this->getRules()->match($this->match);
    
    if (count($rules) > 0) {
      foreach ($rules as $rule) {
        $this->logger->debug($this->indentLogger . '  Fire begin() for '
            . $rule->toString() . ' - Pattern : ' . $this->match);

        try {
          $rule->begin($attribs);
        } catch (Exception $exception) {
          $this->logger->error('Begin event threw exception : '
              . $exception->getMessage());
          throw $exception;
        }
      }
    } else {
      $this->logger->debug($this->indentLogger . '  No rules found matching "'
            . $this->match . '"');
    }
  }

  /**
   * Process notification of character data received from the body of
   * an XML element
   *
   * @param object $parser The XML parser object
   * @param string $data The characters from the XML element body
   */
  public function characterDataHandler($parser, $data) {
    $data = trim($data);

    if ($data != '')  {
      $this->logger->debug($this->indentLogger . 'characters(' . $data. ')');
    }

    // Append these data characters at the end of the bodyText buffer
    $this->bodyText .= $data;
  }

  /**
   * Process notification of the end of an XML element being reached
   *
   * @param object $parser The XML parser object
   * @param string $name Name of the current xml element being processed
   * @throws Exception
   */
  public function endElementHandler($parser, $name) {
    $this->logger->debug($this->indentLogger . '  match="'
        . $this->match . '"');
    $this->logger->debug($this->indentLogger . '  bodyText="'
        . $this->bodyText . '"');

    // Fire 'body' events for all relevant rules
    $rules = $this->getRules()->match($this->match);
    if (count($rules) > 0) {
      foreach($rules as $rule) {
        $this->logger->debug($this->indentLogger . '  Fire body() for '
            . $rule->toString());

        try {
          $rule->body($this->bodyText);
        } catch (Exception $exception) {
          $this->logger->error('Body event threw exception'
              , $exception->getMessage());
          throw $exception;
        }
      }
    } else {
      $this->logger->debug($this->indentLogger . '  No rules found matching "'
          . $this->match . '"');
    }
        
    $this->logger->debug($this->indentLogger . 'endElement(' . $name . ')');

    // Recover the body text from the surrounding element
    $this->bodyText = array_pop($this->bodyTexts);
    $this->logger->debug($this->indentLogger . '  Popping body text "'
        . $this->bodyText . '"');

    // Fire 'end' events for all relevant rules in reverse order
    if (count($rules) > 0) {
      $rulesReverse = array_reverse($rules, true);

      foreach ($rulesReverse as $rule) {
        $this->logger->debug($this->indentLogger . '  Fire end() for '
            . $rule->toString());

        try {
           $rule->end();
        } catch (Exception $exception) {
          $this->logger->error('End event threw exception'
              . $exception->getMessage());
          throw $exception;
        }
      }
    }

    // Recover the previous match expression
    $slash = strrpos($this->match, '/');
    if ($slash === false) {
      $this->match = '';
    } else {
      $this->match = substr($this->match, 0, $slash);
    }
    
    if ($this->indentLogger != '') $this->indentLogger
        = substr($this->indentLogger, 4);
  }

  /**
   * Register a new Rule matching the specified pattern
   *
   * This method sets the digester property on the rule.
   *
   * @param string $pattern Element matching pattern
   * @param Rule $rule The rule to be registered
   */
  public function addRule($pattern, Aloi_Phigester_Rule $rule) {
    $rules = $this->getRules();
    $rules->add($pattern, $rule);
  }
  
  /**
   * Add a "factory create" rule for the specified parameters.
   *
   * @param string $pattern Element matching pattern
   * @param Aloi_Phigester_ObjectCreationFactory $creationFactory Previously
   * instantiated Aloi_Phigester_ObjectCreationFactory to be utilized
   */
  public function addFactoryCreate($pattern
      , Aloi_Phigester_ObjectCreationFactory $creationFactory) {
    $creationFactory->setDigester($this);
    $this->addRule($pattern, new Aloi_Phigester_FactoryCreateRule($creationFactory));
  }

  /**
   * Add an "object create" rule for the specified parameters
   *
   * @param string $pattern Element matching pattern
   * @param string $className Default PHP class name to be created.
   * @param string $attributeName Attribute name that optionally overrides
   * the default PHP class name to be created
   */
  public function addObjectCreate($pattern, $className, $attributeName = '') {
    $rule = new Aloi_Phigester_ObjectCreateRule($className, $attributeName);
    $this->addRule($pattern, $rule);
  }

  /**
   * Add a "set property" rule for the specified parameters
   *
   * @param string $pattern The element matching pattern
   * @param string $name The attribute name containing the property name to be
   * set
   * @param string $value The attribute name containing the property value to
   * set
   */
  public function addSetProperty($pattern, $name, $value = '') {
    $rule = new Aloi_Phigester_SetPropertyRule($name, $value);
    $this->addRule($pattern, $rule);
  }

  /**
   * Add a "set properties" rule for the specified parameters
   *
   * @param string $pattern Element matching pattern
   * @param array $attributeNames Names of attributes with custom mappings
   * @param array $propertyNames Property names these attributes map to
   */
  public function addSetProperties($pattern, array $attributeNames = array()
      , array $propertyNames = array()) {
    $rule = new Aloi_Phigester_SetPropertiesRule($attributeNames, $propertyNames);
    $this->addRule($pattern, $rule);
  }

  /**
   * Add a "set next" rule for the specified parameters
   *
   * @param string $pattern Element matching pattern
   * @param string $methodName Method name to call on the parent element
   */
  public function addSetNext($pattern, $methodName) {
    $rule = new Aloi_Phigester_SetNextRule($methodName);
    $this->addRule($pattern, $rule);
  }

  /**
   * Add a "call method" rule for the specified parameters
   * 
   * If paramCount is set to zero the rule will use the body of the matched
   * element as the single argument of the method, unless paramTypes is null
   * or empty, in this case the rule will call the specified method with
   * no arguments.
   *
   * @param string $pattern Element matching pattern
   * @param string $methodName Method name to be called
   * @param integer $paramCount Number of expected parameters (or zero
   * for a single parameter from the body of this element)
   * @param array $paramTypes Set of PHP class names for the types of the
   * expected parameters
   */
  public function addCallMethod($pattern, $methodName, $paramCount = 0
      , $paramTypes = null) {
    $rule = new Aloi_Phigester_CallMethodRule(0, $methodName, $paramCount, $paramTypes);
    $this->addRule($pattern, $rule);
  }
  
  /**
   * Add a "call parameter" rule for the specified parameters
   *
   * @param string $pattern Element matching pattern
   * @param integer $paramIndex Zero-relative parameter index to set
   * (from the body of this element)
   * @param string $attributeName Attribute whose value is used
   * as the parameter value
   * @param boolean $fromStack Should the call parameter be taken from
   * the top of the stack?
   * @param integer $stackIndex Set the call parameter to the stackIndex'th
   * object down the stack, where 0 is the top of the stack, 1 the next element
   * down and so on
   */
  public function addCallParam($pattern, $paramIndex, $attributeName = ''
      , $fromStack = false, $stackIndex = 0) {
    $rule = new Aloi_Phigester_CallParamRule($paramIndex, $attributeName, $fromStack
        , $stackIndex);
    $this->addRule($pattern, $rule);
  }
  
  /**
   * Register a set of Rule instances defined in a RuleSet
   *
   * @param Aloi_Phigester_RuleSet $ruleSet The RuleSet instance to configure from
   */
  public function addRuleSet(Aloi_Phigester_RuleSet $ruleSet) {
    $ruleSet->addRuleInstances($this);
  }
  
  /**
   * Push a new object onto the top of the object stack
   *
   * @param object The new object
   */
  public function push($object) {
    if (count($this->stack) == 0) {
      $this->root = $object;
    }
    $this->stack[] = $object;
  }

  /**
   * Pop the top object off of the stack, and return it
   *
   * If there are no objects on the stack, return null.
   *
   * @return object
   */
  public function pop() {
    $object = array_pop($this->stack);
    if (is_null($object))
      $this->logger->warn('Empty stack (returning null)');

    return $object;
  }

  /**
   * Return the n'th object down the stack, where 0 is the top element
   * and [count()-1] is the bottom element
   *
   * If the specified index is out of range, return null.
   *
   * @param integer $n Index of the desired element, where 0 is the top of the
   * stack, 1 is the next element down, and so on
   * @return object
   */
  public function peek($n = 0) {
    // Emulate a stack behavour
    $tos = count($this->stack) - 1;   // last item pushed onto the stack
    $ix = $tos - $n;                  // required stack index

    // Return the next-to-top object on the stack without removing it
    $object = null;
    if (array_key_exists($ix, $this->stack)) {
      $object = $this->stack[$ix];   // [0] is top-of-stack index
    }

    if (is_null($object)) $this->logger->warn('Empty stack (returning null)');
    return $object;
  }

  /**
   * Return the current depth of the element stack
   *
   * @return integer
   */
  public function getCount() {
    return count($this->stack);
  }

  /**
   * Clear the current contents of the object stack
   */
  public function clear() {
    $this->match = '';
    $this->bodyTexts = array();
    $this->stack = array();
  }
  
  /**
   * Return the n'th object down the parameters stack, where 0 is the top
   * element and [count()-1] is the bottom element
   * 
   * If the specified index is out of range, return null. The parameters stack
   * is used to store CallMethodRule parameters.
   *
   * @param integer $n Index of the desired element, where 0 is the top
   * of the stack, 1 is the next element down, and so on.
   * @return object
   */
  public function &peekParams($n = 0) {
    // Emulate a stack behavour
    $tos = count($this->params) - 1;   // last item pushed onto the stack
    $ix = $tos - $n;                   // required stack index

    // Return the next-to-top object on the stack without removing it
    $object = null;
    if (array_key_exists($ix, $this->params)) {
      $object = &$this->params[$ix];   // [0] is top-of-stack index
    }
    
    if (is_null($object)) $this->logger->warn('Empty stack (returning null)');
    return $object;
  }
  
  /**
   * Pop the top object off of the parameters stack, and return it
   * 
   * If the are no objects on the stack, return null. The parameters stack
   * is used to store CallMethodRule parameters.
   *
   * @return object
   */
  public function popParams() {
    $object = array_pop($this->params);
    if (is_null($object))
      $this->logger->warn('Empty stack (returning null)');

    return $object;
  }
  
  /**
   * Push a new object onto the top of the parameters stack
   * 
   * The parameters stack is used to store CallMethodRule parameters.
   *
   * @param object $object The new object
   */
  public function pushParams($object) {
    $this->params[] = $object;
  }
}
?>
