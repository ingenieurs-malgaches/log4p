<?php
/**
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER.
 * 
 * Copyright 2014 - Nabil Andriantomanga.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Date de creation : 07/06/2015
 * Date de modification : 07/06/2015
 * 
 * @since 1.0
 * @author N.Andriantomanga <nabil.projets at gmail.com>
 */
class builder /* @{log4p} builder */ {

    /** the output logging file path */
    private $filePath;
    
    /** the level of logging. This is set to Info as default */
    private $level;
    
    /** output date format . each line will start with date following this format */
    private $dateFormat;
    
    /** log4p will create new file when current file reach this size*/
    private $maxFileSize;

    function __construct($newFilePath, $newLevel) {
        $this->filePath = $newFilePath;
        $this->level = $newLevel;
        $this->dateFormat = 'Y-m-d G:i:s';

        /** default max file size is set to 1024 bytes */
        $this->maxFileSize = 1024;
    }

    /**
     * Set the logger file path .
     * 
     * @param type $newFilePath
     * @return \builder 
     */
    function filePath($newFilePath) {
        $this->filePath = $newFilePath;
        return $this;
    }

    function getFilePath() {
        return $this->filePath;
    }

    /**
     * Set the logger level .
     * 
     * @param type $newLevel
     * @return \builder
     */
    function level($newLevel) {
        $this->level = $newLevel;
        return $this;
    }

    function getLevel() {
        return $this->level;
    }

    /**
     * Set a date format to be used by the logger
     * By default, the next format is used : Y-m-d G:i:s
     * 
     * @param type $newDateFormat
     * @return \builder
     */
    function dateFormat($newDateFormat) {
        $this->dateFormat = $newDateFormat;
        return $this;
    }

    function getDateFormat() {
        return $this->dateFormat;
    }

    function maxFileSize($newMaxFileSize) {
        if ($newMaxFileSize > 0) {
            $this->maxFileSize = $newMaxFileSize;
        }
        return $this;
    }

    function getMaxFileSize() {
        return $this->maxFileSize;
    }

    function build() {
        return new log4p($this);
    }
}

class log4p {

    /** the output logging file path */
    private $filePath;
    
    /** the level of logging. This is set to Info as default */
    private $level;
    
    /** output date format . each line will start with date following this format */
    private $dateFormat;
    
    /** log4p will create new file when current file reach this size. This size is expressed in bytes */
    private $maxFileSize;
    
    /** output file handle */
    private $file_handle;
    
    /** This is a message queue */
    private $messages;
    
    /** Define a level of logging : verbose */
    const DEBUG = 1;

    /** Define a level of logging : INFO is the usual level of logging. It is set as default */
    const INFO = 2;

    /** Define a level of logging : ... */
    const WARN = 3;

    /** Define a level of logging : ... */
    const ERROR = 4;

    /** Define a level of logging : ... */
    const FATAL = 5;

    static function builder($newFilePath = 'log4p.log', $newLevel = log4p::INFO) {
        return new builder($newFilePath, $newLevel);
    }

    function __construct($log4pBuilder) {
        $this->filePath     = $log4pBuilder->getFilePath();
        $this->level        = $log4pBuilder->getLevel();
        $this->dateFormat   = $log4pBuilder->getDateFormat();
        $this->maxFileSize  = $log4pBuilder->getMaxFileSize();
        
        $this->messages     = array();
        
        if(file_exists($this->filePath)) {
            if(!is_writable($this->filePath)) {
                $this->messages[] = 'The log file exists but cannot be opened for writting.';
                return;
            }
            if(filesize($this->filePath) > $this->maxFileSize){
                $this->create_backup_log_file();
            }
        }
        $this->open_logger_file();
        return;
    }
    
    private function open_logger_file() {
        if($this->file_handle = fopen($this->filePath, 'a')) {
            $this->messages[] = 'The log file was opened successfully.';
            return true;
        } else {
            $this->messages[] = 'The log file could not be opened.';
            return false;
        }
    }
    
    /**
     * Create a backup log file by renaming the old one.
     * 
     * @return boolean
     */
    private function create_backup_log_file() {
        $log_files_names = glob($this->filePath . '*');
        $max = 0;
        for($i=0, $n=count($log_files_names); $i < $n; $i++) {
            
            $name = $log_files_names[$i];
            if(strlen($name) > strlen($this->filePath)) {
                $number_str = substr($name, strlen($this->filePath));
                $j = intval($number_str);
                if($j > $max) {
                    $max = $j;
                }
            }
        }
        $max++;
        if(!rename($this->filePath, $this->filePath . '' . $max)) {
            $this->messages[] = 'could not rename log file. Please check permissions';
            return false;
        }
        return true;
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        if($this->file_handle) {
            fclose($this->file_handle);
        }
    }
    
    /**
     * Write info line in log file.
     * @param type $newLine
     */
    public function info($newLine) {
        $this->log($newLine, log4p::INFO);
    }
    
    /**
     * Write debug line in log file.
     * @param type $newLine
     */
    public function debug($newLine) {
        $this->log($newLine, log4p::DEBUG);
    }
    
    /**
     * Write warn line in log file.
     * @param type $newLine
     */
    public function warn($newLine) {
        $this->log($newLine, log4p::WARN);
    }
    
    /**
     * Write error line in log file.
     * @param type $newLine
     */
    public function error($newLine) {
        $this->log($newLine, log4p::ERROR);
    }
    
    /**
     * Write fatal line in log file.
     * @param type $newLine
     */
    public function fatal($newLine) {
        $this->log($newLine, log4p::FATAL);
    }
    
    /**
     * Return the prefixe of each line corresponding to the given level
     * 
     * @param type $logLevel
     * @return type
     */
    private function getLinePrefix($logLevel) {
        $formatted_date = date($this->dateFormat);
        switch($logLevel) {
            case log4p::DEBUG :
                return "[DEBUG] $formatted_date : ";
            case log4p::WARN :
                return "[WARN] $formatted_date : ";
            case log4p::ERROR :
                return "[ERROR] $formatted_date : ";
            case log4p::FATAL :
                return "[FATAL] $formatted_date : ";
        }
        return "[INFO] $formatted_date : ";
    }
    
    /**
     * write a new line in log file. If the log file size reach the max file size,
     * then, new file will be created.
     * 
     * @param type $newLine new line to be added in log file.
     */
    private function writeToLogFile($newLine) {
        
        if($this->file_handle) {
            
            if($this->file_handle && fwrite($this->file_handle, $newLine) === false) {
                $this->messages[] = 'Could not write to file : ' . $this->filePath;
            }
            if(filesize($this->filePath) > $this->maxFileSize){
                fclose($this->filePath);
                $this->create_backup_log_file();
                $this->open_logger_file();
            }
        }
    }
    
    
    /**
     * Write the line in log file using the given level. 
     * 
     * @param type $line
     * @param type $logLevel
     */
    private function log($line, $logLevel) {
        if($this->level <= $logLevel) {
            $linePrefix = $this->getLinePrefix($logLevel);
            $this->writeToLogFile("$linePrefix $line \n");
        }
    }

    /**
     * Return the logger message queue
     * @return type array of string
     */
    public function getMessages() {
        return $this->messages;
    }

}
