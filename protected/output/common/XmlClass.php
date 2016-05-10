<?php 

/* used to store the parsed information */ 
class xml_container { 
function store($k,$v) { 
   $this->{$k}[] = $v; 
}
} 
/* parses the information */ 
class xml { 
// initialize some variables 
var $current_tag=array(); 
var $xml_parser; 
var $Version = 1.0; 
var $tagtracker = array(); 
/* Here are the XML functions needed by expat */ 
/* when expat hits an opening tag, it fires up this function */ 
function startElement($parser, $name, $attrs) { 
   array_push($this->current_tag, $name); // add tag to the cur. tag array 
   $curtag = implode("_",$this->current_tag); // piece together tag 
   /* this tracks what array index we are on for this tag */ 
   if(isset($this->tagtracker["$curtag"])) { 
    $this->tagtracker["$curtag"]++; 
   } else { 
    $this->tagtracker["$curtag"]=0; 
   } 
   /* if there are attributes for this tag, we set them here. */ 
   if(count($attrs)>0) { 
    $j = $this->tagtracker["$curtag"]; 
    if(!$j) $j = 0; 
    if(!is_object($GLOBALS[$this->identifier]["$curtag"][$j])) { 
     $GLOBALS[$this->identifier]["$curtag"][$j] = new xml_container; 
    } 
    $GLOBALS[$this->identifier]["$curtag"][$j]->store("attributes",$attrs); 
   } 
} // end function startElement 
/* when expat hits a closing tag, it fires up this function */ 
function endElement($parser, $name) { 
   $curtag = implode("_",$this->current_tag); // piece together tag 
   // before we pop it off, 
   // so we can get the correct 
   // cdata 
   if(!$this->tagdata["$curtag"]) { 
    $popped = array_pop($this->current_tag); // or else we screw up where we are 
    return; // if we have no data for the tag 
   } else { 
    $TD = $this->tagdata["$curtag"]; 
    unset($this->tagdata["$curtag"]); 
   } 
   $popped = array_pop($this->current_tag); 
   // we want the tag name for 
   // the tag above this, it 
   // allows us to group the 
   // tags together in a more 
   // intuitive way. 
   if(sizeof($this->current_tag) == 0) return; // if we aren't in a tag 
   $curtag = implode("_",$this->current_tag); // piece together tag 
   // this time for the arrays 
   $j = $this->tagtracker["$curtag"]; 
   if(!$j) $j = 0; 
   if(!is_object($GLOBALS[$this->identifier]["$curtag"][$j])) { 
    $GLOBALS[$this->identifier]["$curtag"][$j] = new xml_container; 
   } 
   $GLOBALS[$this->identifier]["$curtag"][$j]->store($name,$TD); #$this->tagdata["$curtag"]); 
   unset($TD); 
   return TRUE; 
} 
/* when expat finds some internal tag character data, 
it fires up this function */ 
function characterData($parser, $cdata) { 
   $curtag = implode("_",$this->current_tag); // piece together tag 
   $this->tagdata["$curtag"] .= $cdata; 
} 
/* this is the constructor: automatically called when the class is initialized */ 
function xml($data,$identifier='xml') { 
   $this->identifier = $identifier; 
   // create parser object 
   $this->xml_parser = xml_parser_create(); 
   // set up some options and handlers 
   xml_set_object($this->xml_parser,$this); 
   xml_parser_set_option($this->xml_parser,XML_OPTION_CASE_FOLDING,0); 
   xml_set_element_handler($this->xml_parser, "startElement", "endElement"); 
   xml_set_character_data_handler($this->xml_parser, "characterData"); 
  
   if (!xml_parse($this->xml_parser, $data, TRUE)) { 
    sprintf("XML error: %s at line %d", 
    xml_error_string(xml_get_error_code($this->xml_parser)), 
    xml_get_current_line_number($this->xml_parser)); 
   } 
   // we are done with the parser, so let's free it 
   xml_parser_free($this->xml_parser); 
} // end constructor: function xml() 
} // thus, we end our class xml 
?> 

 

