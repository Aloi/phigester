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
 * This class is a wrapper for the PHP's internal expat parser
 *
 * It takes an XML file represented by an abstract path name, and starts
 * parsing the file and calling the different "trap" methods inherited from
 * the AbstractXMLParser class.<br>
 * Those methods then invoke the representative methods in the registered
 * handler classes.
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @author Yannick Lecaillez <yl@seasonfive.com>
 * @author Andreas Aderhold <andi@binarycloud.com>
 * @version $Id$
 */
abstract class Aloi_Phigester_AbstractExpatParser extends Aloi_Phigester_AbstractXMLParser {
  /**
   * Constructs a new AbstractExpatParser object
   */
  public function __construct() {
    $this->createParser();
  }
  
  /**
   * Sets up PHP's internal expat parser and options.
   */
  private function createParser() {
    //We need the xml parser to operate in this class
    $this->parser = xml_parser_create();
    xml_set_object($this->parser, $this);
    xml_set_element_handler($this->parser, 'startElementHandler'
        , 'endElementHandler');
    xml_set_character_data_handler($this->parser, 'characterDataHandler');
    
    //Default options
    $this->parserSetOption(XML_OPTION_CASE_FOLDING, false);
  }
 
  /**
   * Starts the parsing process
   *
   * @param string $xmlFile Name of the file containing the XML data to be
   * parsed
   * @throws Aloi_Phigester_Exception_ExpatParserException - If something gone wrong
   * during parsing
   * @throws Aloi_Phigester_Exception_IOException - If XML file can not be accessed
   */
  public function parse($xmlFile) {
    $xmlFile = (string) $xmlFile;

    //Check the XML file to be parsed
    if (is_file($xmlFile)) {
      //Open the XML file in read-only mode
      $fp = fopen($xmlFile, 'r');
      if (is_resource($fp)) {
        //Reading the XML file
        $data = fread($fp, filesize($xmlFile));
        //Parsing the XML file
        if (xml_parse($this->parser, $data)) {
          fclose($fp);
          //Initialize the parser for another use
          xml_parser_free($this->parser);
          $this->createParser();
        } else {
          //Get the XML parser error
          $error = xml_error_string(xml_get_error_code($this->parser));
          $line = xml_get_current_line_number($this->parser);
          xml_parser_free($this->parser);
          fclose($fp);
          throw new Aloi_Phigester_Exception_ExpatParserException(__METHOD__ . ' : ' . $error
              . ' on line ' . $line);
        }
      } else {
        throw new Aloi_Phigester_Exception_IOException(__METHOD__
            . ' : XML file "' . $xmlFile . '" can not be accessed');
      }
    } else {
      throw new Aloi_Phigester_Exception_IOException(__METHOD__
          . ' : XML file "' . $xmlFile . '" can not be accessed');
    }
  }

  /**
   * Override PHP's parser default settings, created in the constructor
   *
   * @param string $opt The option to set
   * @param mixed $val The value to set
   * @return boolean True if the option could be set, otherwise false
   */
  public function parserSetOption($opt, $val) {
    return xml_parser_set_option($this->parser, $opt, $val);
  }
}
?>
