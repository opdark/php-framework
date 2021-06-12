<?php

namespace Entity;

use Constant\FileType; 

/**
 * Description of UploadFile
 *
 * @author user
 */
class UploadFile extends \Core\Model\Entity {

    private $file;
    private $size;
    private $originalName;
    private $tempFilename;
    private $code;
    private $url;
    private $status;
    private $extension;


    public function setFile($file) {
        $this->preUpload();
        $this->init($file);
    }

    private function init($file) {
        if ($file) {
            $this->file = $file;
            $this->size = $file['size'];
            $this->url = $file['tmp_name'];
            $this->originalName = $file['name'];
            $arr = explode('.', $file['name']);
            $this->extension = end($arr);
        }
    }

    public function preUpload() {
        if (null === $this->file) {
            return;
        }
        $this->tempFilename = $this->getAbsolutePath();
        $this->url = $this->generateFileName();
    }

    public function generateFileName() {
        $name = '';
        $date = new \DateTime();
        $name .= $date->format('YmdHis');
        $name .= md5(uniqid(mt_rand(), true));

        $name .= '.' . $this->extension;

        return $name;
    }

    public function upload() {

        if (null === $this->file) {
            return;
        }
        $this->preUpload();
        $done = move_uploaded_file($this->file['tmp_name'], $this->getAbsolutePath());
        if ($done) {
            $this->deleteTempFile();
        }
        return $done;
    }

    public function deleteFile() {
        $this->tempFilename = $this->getAbsolutePath();
        $this->deleteTempFile();
    }

    public function deleteTempFile() {
        if (file_exists($this->tempFilename)) {
            unlink($this->tempFilename);
        }
    }

    public function getAbsolutePath() {
        return null === $this->url ? null : $this->getAbsoluteDir() . '' . $this->url;
    }

    public function getWebPath() {
        return null === $this->url ? null : $this->getWebDir() . '' . $this->url;
    }

    public function getAbsoluteDir() {

        $chemin = ROOT_DIR . '/web' . $this->getWebDir();
        if (!is_dir($chemin)) {
            mkdir($chemin, 0777, true);
        }

        return $chemin;
    }

    public function getWebDir() {
        return '/docs/' . FileType::getFileFolder($this->code) . '/';
    }

    public function getId() {
        return $this->id;
    }

    public function setCode($code) {
        $this->code = $code;

        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getFile() {
        return $this->file;
    }

    public function getTempFilename() {
        return $this->tempFilename;
    }

    public function setTempFilename($tempFilename) {
        $this->tempFilename = $tempFilename;
    }

    public function setExtension($extension) {
        $this->extension = $extension;

        return $this;
    }

    public function getExtension() {
        $extension = $this->extension;
        if ($this->file) {
            $extension = $this->file->guessExtension();
        }
        return $extension;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function isPdfFile() {
        return in_array(strtolower($this->extension), FileType::EXTENSION_PDF);
    }

    public function isExcelFile() {
        return in_array(strtolower($this->extension), FileType::EXTENSION_EXCEL);
    }

    public function isImageFile() {
        return in_array(strtolower($this->extension), FileType::EXTENSION_IMAGE);
    }

    public function __toString() {
        return $this->getAbsolutePath();
    }

    /**
     * 
     * @return array
     */
    public function toArray() {
        $result['ID'] = $this->id;
        $result['ORIGINAL_NAME'] = $this->originalName;
        $result['SIZES'] = $this->size;
        $result['URL'] = $this->url;
        $result['CODE'] = $this->code;
        $result['STATUS'] = $this->status;
        $result['EXTENSION'] = $this->extension;

        return $result;
    }

}
