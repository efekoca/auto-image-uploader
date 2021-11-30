<?php
    namespace jsonDB;
    if((basename($_SERVER["PHP_SELF"]) == basename(__FILE__)) or (basename($_SERVER["PHP_SELF"]) == substr(basename(__FILE__), 0, (strlen(basename(__FILE__)) - 4)))){
        header("Location: ./");
        die();
    }
    class jsondbException extends \Exception{
        public function errorMsg(){
            return "An unexpected error has occurred: " . $this->getMessage();
        }
    }
    class db{
        private $file;
        public function __construct($file){
            $pattern = "/.+\.json/";
            if(!preg_match($pattern, $file))
                throw new jsondbException("Only json files can be used.");
            $this->file = $file;
            if(!file_exists($file)){
                touch($file);
                if(!file_put_contents($file, json_encode(array("available" => true))))
                    throw new jsondbException("An unknown error has occurred.");
            }
        }
        public function get($par = null){
            $readFile = file_get_contents($this->file);
            if(($readFile == "") or (empty($readFile)) or (!$readFile)){
                throw new jsondbException("There was an error reading the {$this->file} file.");
            }
            $decoded = json_decode($readFile, true);
            if((!is_array($decoded)) and (is_null($decoded)))
                throw new jsondbException("{$this->file} file contains corrupt json type.");
            elseif(is_null($par))
                return $decoded;
            else{
                if(!empty($decoded[$par]))
                    return $decoded[$par];
                return null;
            }
        }
        public function delete($par){
            /**
             * @return bool
             */
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded !== false){
                    if(empty($decoded[$par]))
                        return false;
                    unset($decoded[$par]);
                    $encoded = json_encode($decoded);
                    if($encoded !== false){
                        if(file_put_contents($this->file, $encoded))
                            return true;
                        return false;
                    }
                    return false;
                }
                throw new jsondbException("{$this->file} file contains corrupt json type.");
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function set($par, $val, $multiple = false){
            /**
             * @return bool
             */
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded !== false){
                    if(($multiple === false) and (!is_array($val)))
                        $decoded[$par] = $val;
                    else{
                        if(!array_key_exists($par, $decoded)){
                            $decoded[$par] = array();
                        }
                        foreach($val as $key => $value){
                            $decoded[$par][$key] = $value;
                        }
                    }
                    $encoded = json_encode($decoded);
                    if($encoded === false)
                        throw new jsondbException("Decode error occurred.");
                    if(!file_put_contents($this->file, $encoded))
                        throw new jsondbException("Set operation failed.");
                    return true;
                }else{
                    throw new jsondbException("{$this->file} file contains corrupt json type.");
                }
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function add($par, $count){
            /**
             * @return bool
             * @param int $count
             */
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                elseif(empty($decoded[$par]))
                    return false;
                elseif(!is_numeric($decoded[$par]))
                    return false;
                else{
                    $decoded[$par] += $count;
                    $encoded = json_encode($decoded);
                    if($encoded !== false){
                        if(file_put_contents($this->file, $encoded))
                            return true;
                        throw new jsondbException("Add operation failed.");
                    }
                    throw new jsondbException("Add operation failed.");
                }
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function subtract($par, $count){
            /**
             * @return bool
             * @param int $count
             */
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                elseif(empty($decoded[$par]))
                    return false;
                elseif(!is_numeric($decoded[$par]))
                    return false;
                else{
                    $decoded[$par] -= $count;
                    $encoded = json_encode($decoded);
                    if($encoded !== false){
                        if(file_put_contents($this->file, $encoded))
                            return true;
                        throw new jsondbException("Subtract operation failed.");
                    }
                    throw new jsondbException("Subtract operation failed.");
                }
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function deleteFile(){
            /**
             * @return bool
             */
            if(!unlink($this->file))
                return false;
            return true;
        }
        public function deleteContent(){
            /**
             * @return bool
             */
            if(!file_put_contents($this->file, json_encode(array("available" => true))))
                throw new jsondbException("Could not delete contents of {$this->file} file.");
            return true;
        }
        public function order(){
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                if(asort($decoded))
                    return $decoded;
                return false;
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function reverse(){
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Order operation failed.");
                if(arsort($decoded))
                    return $decoded;
                return false;
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function regex($pattern){
                if($readFile = file_get_contents($this->file)){
                    $decoded = json_decode($readFile, true);
                    if($decoded === false)
                        throw new jsondbException("Decode error occurred.");
                    $testRegex = @preg_match($pattern, "testString");
                    if($testRegex === false){
                        if((preg_last_error() != PREG_NO_ERROR) and (preg_last_error() == PREG_INTERNAL_ERROR)){
                            throw new jsondbException("You submitted a wrong pattern.");
                        }
                    }
                    $newArr = array_filter($decoded, function($item) use ($pattern){
                        return preg_match($pattern, $item);
                    });
                    return count($newArr) > 0 ? $newArr : null;
                }
                throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function has($par){
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                if(!empty($decoded[$par]))
                    return true;
                return false;
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function update($key, $val){
            /**
             * @return bool
             */
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded !== false){
                    $decoded[$key] = $val;
                    $encoded = json_encode($decoded);
                    if($encoded === false)
                        throw new jsondbException("Decode error occurred.");
                    if(!file_put_contents($this->file, $encoded))
                        throw new jsondbException("Update operation failed.");
                    return true;
                }else{
                    throw new jsondbException("{$this->file} file contains corrupt json type.");
                }
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function hasVal($par){
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                $newArr = array_filter($decoded, function($item) use ($par){
                    $pattern = "/^{$par}$/";
                    return preg_match($pattern, $item);
                });
                return count($newArr) > 0 ? $newArr : null;
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function search($delimiter){
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                $newArr = array_filter($decoded, function($item) use ($delimiter){
                    $pattern = "/.*{$delimiter}.*/";
                    return preg_match($pattern, $item);
                });
                return count($newArr) > 0 ? $newArr : null;
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function searchKey($delimiter){
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                $newArr = array();
                foreach($decoded as $key => $value){
                    $pattern = "/.*{$delimiter}.*/";
                    if(!preg_match($pattern, $key))
                        continue;
                    $newArr[$key] = $value;
                }
                return count($newArr) > 0 ? $newArr : null;
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function getStringVals(){
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                $newArr = array_filter($decoded, function($item){
                    $pattern = "/[a-zA-Z]+/";
                    return preg_match($pattern, $item);
                });
                return count($newArr) > 0 ? $newArr : null;
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function getStringKeys(){
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                $newArr = array();
                foreach($decoded as $key => $value){
                    $pattern = "/[a-zA-Z]+/";
                    if(!preg_match($pattern, $key))
                        continue;
                    $newArr[$key] = $value;
                }
                return count($newArr) > 0 ? $newArr : null;
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function getNumericKeys(){
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                $newArr = array();
                foreach($decoded as $key => $value){
                    $pattern = "/[0-9]+/";
                    if(!preg_match($pattern, $key))
                        continue;
                    $newArr[$key] = $value;
                }
                return count($newArr) > 0 ? $newArr : null;
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
        public function getNumericVals(){
            if($readFile = file_get_contents($this->file)){
                $decoded = json_decode($readFile, true);
                if($decoded === false)
                    throw new jsondbException("Decode error occurred.");
                $newArr = array_filter($decoded, function($item){
                    $pattern = "/[0-9]+/";
                    return preg_match($pattern, $item);
                });
                return count($newArr) > 0 ? $newArr : null;
            }
            throw new jsondbException("There was an error reading the {$this->file} file.");
        }
    }
?>