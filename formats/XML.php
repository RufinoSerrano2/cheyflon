<?php
class XML {

    /**
     * Returns a XML string of the value passed as parameter.
     * @param mixed $value Value to stringify
     * @return string Parameter value as XML
     */
    public static function stringify(mixed $value) : string {
        return json_encode($value);
    }

    /**
     * Builds a PHP object or array from the XML string passed as parameter.
     * @param string $xml_string XML string to parse
     * @return mixed Parsed XML string
     */
    public static function parse(string $xml_string) {
        return simplexml_load_string($xml_string);
    }

    private static function arrayToXML($data, &$xml_data) {
        foreach($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)){
                    $key = 'item'.$key; //dealing with <0/>..<n/> issues
                }

                $subnode = $xml_data->addChild($key);
                XML::arrayToXML($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
            }
    }
}