<?php
class JsonFileManager {
    private $filename;

    public function __construct($filename) {
        $this->filename = $filename;
    }

    public function saveData($data) {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        if (file_put_contents($this->filename, $json) !== false) {
            return true;
        }
        return false;
    }

    public function loadData() {
        if (file_exists($this->filename)) {
            $json = file_get_contents($this->filename);
            return json_decode($json);
        } else {
            return [];
        }
    }

    public function appendData($object) {
        $existingData = $this->loadData();
        $existingData[] = $object;
        return $this->saveData($existingData);
    }

    public function removeData($index) {
        $existingData = $this->loadData();
        if (isset($existingData[$index])) {
            unset($existingData[$index]);
            return $this->saveData(array_values($existingData));
        }
        return false;
    }
}

