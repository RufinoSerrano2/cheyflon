<?php
    class JSON {

        /**
         * Returns a JSON string of the value passed as parameter.
         * @param mixed $value Value to stringify
         * @param int $flags PHP JSON flag if needed. Defaults to 0
         * @return string Stringified parameter
         */
        public static function stringify(mixed $value, int $flags = 0) : string {
            return json_encode($value, $flags);
        }

        /**
         * Returns a JSON string of the XML passed as parameter.
         * @param string $xml_string XML Document
         * @param int $flags PHP JSON flag if needed. Defaults to 0
         * @return string Stringified parameter
         */
        public static function stringifyFromXML(string $xml_string, int $flags = 0) : string {
            return JSON::stringify(XML::parse($xml_string), $flags);
        }

        /**
         * Returns a easiliy-readable JSON string of the value passed as parameter.
         * @param mixed $value Value to stringify
         * @return string Stringified parameter
         */
        public static function stringifyPretty($value) : string {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        /**
         * Builds a PHP object or array from the JSON string passed as parameter.
         * @param string $json_string JSON string to parse
         * @param bool $as_array If true, the return value will be an array
         * @return mixed Parsed JSON string
         */
        public static function parse(string $json_string, bool $as_array) {
            return json_decode($json_string, $as_array);
        }

        public static function parseObject(string $json_string, string $to_class) {
            $return_object = new $to_class();

            $parsed_json = JSON::parse($json_string, true);
            $return_object = JSON::set($parsed_json, $return_object);

            return $return_object;
        }
        
        public static function set($data, $obj) {
            foreach ($data AS $key => $value) {
                if (is_array($value)) {
                    if (array_key_exists("0", $value)) {
                        $value = array_values($value);
                    } else {
                        $capitalized_var = $key;
                        $child_class = get_class(new $capitalized_var);
                        $child_object = new $child_class();
                        $child_object = JSON::set($value, $child_object);
                        $value = $child_object;
                    }
                }

                $obj->{$key} = $value;
            }

            return $obj;
        }
    }