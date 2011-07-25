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
 * The abstract XML parser class
 *
 * This class represents an XML parser. It is an abstract class that must be
 * implemented by the real parser that must extend this class.
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @author Yannick Lecaillez <yl@seasonfive.com>
 * @author Andreas Aderhold <andi@binarycloud.com>
 * @version $Id$
 */
abstract class Aloi_Phigester_AbstractXMLParser {
  /**
   * Internal XML parser object
   * 
   * @var object
   */
  protected $parser = null;
  
  /**
   * Sets options for PHP internal parser
   *
   * Must be implemented by the parser class if it should be used.
   *
   * @param mixed $opt
   * @param mixed $val
   */
  abstract public function parserSetOption($opt, $val);
  
  /**
   * Method that gets invoked when the parser runs over a XML start element
   *
   * This method is called by PHP's internal parser functions and registered
   * in the actual parser implementation.<br>
   * It gives control to the current active handler object by calling the
   * startElement() method.
   *
   * @param object $parser The php's internal parser handle
   * @param string $name The open tag name
   * @param array $attribs The tag's attributes if any
   * @throws Exception
   */
  abstract public function startElementHandler($parser, $name, $attribs);
  
  /**
   * Method that gets invoked when the parser runs over a XML close element
   *
   * This method is called by PHP's internal parser functions and registered
   * in the actual parser implementation.<br>
   * It gives control to the current active handler object by calling the
   * endElement() method.
   *
   * @param object $parser The php's internal parser handle
   * @param string $name The closing tag name
   * @throws Exception
   */
  abstract public function endElementHandler($parser, $name);
  
  /**
   * Method that gets invoked when the parser runs over CDATA
   *
   * This method is called by PHP's internal parser functions and registered
   * in the actual parser implementation.<br>
   * It gives control to the current active handler object by calling the
   * characters() method. That processes the given CDATA.
   *
   * @param object $parser The php's internal parser handle
   * @param string $data The CDATA
   * @throws Exception
   */
  abstract public function characterDataHandler($parser, $data);
  
  /**
   * Entrypoint for parser
   *
   * This method needs to be implemented by the child class that utilizes
   * the concrete parser.
   * @param string $xmlFile Name of the file containing the XML data to be
   * parsed
   */
  abstract public function parse($xmlFile);
}
?>
