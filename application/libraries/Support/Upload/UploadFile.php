<?php

namespace Lib\Support\Upload;

use \Upload\Storage\FileSystem;
use \Upload\File;
use \Upload\Validation\Mimetype;
use \Upload\Validation\Size;

class UploadFile
{
		
	protected $storage;
	
	protected $mimeType = [
				'image/png',
				'image/gif',
				'image/jpeg',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'application/vnd.ms-word',
				'application/msword',
				'application/vnd.ms-excel',
				'application/pdf',
				'application/x-rar-compressed',
				'application/x-rar',
				'application/octet-stream',
				'application/zip'
	];	// 注意添加类型里 请同是修改 /UploadFlie/Services/UploadFileService.php getMimeDirectory()
	
	protected $size = '5M';
	
	public function __construct(String $storage = 'tmp')
	{
		$this->storage = $storage;
	}
	
	public function getStorage()
	{
		return $this->storage;
	}
	
	public function setStorage($storage)
	{
		if ( !empty($storage) )
		{
			$this->storage = $storage;
		}
	}
	
	/**
     * 上传文件
     *
     * @param string $files
     * @return array
     */
	public function upload( $files )
	{	
		$returnData = array(
			'isSuccess' => false,
			'errorMessage' => ''
		);	

		if ( empty($files) )
		{
			return $returnData;
		}
		
		$storage = new FileSystem($this->storage);
		$file = new File($files, $storage);
		
		$original_name = $file->getName();
		$new_filename = sha1(uniqid() . $original_name);
		
		$file->setName($new_filename);

		if($file->getExtension() == 'doc'){
			$this->mimeType[] = 'text/html';
		}

		$file->addValidations(array(
		    new Mimetype( $this->mimeType ),
		    new Size( $this->size )
		));

		if($file->getSize() == 0){
			$returnData ['errorMessage'] = '不能上传空文件！';
			return $returnData;
		}

		$data = array(
		    'name'       => $file->getNameWithExtension(),
		    'original_name' => $original_name,
		    'new_name'   => $new_filename,
		    'path'		 => $this->storage,
		    'extension'  => $file->getExtension(),
		    'mime'       => $file->getMimetype(),
		    'size'       => $file->getSize(),
		    'md5'        => $file->getMd5(),
		    'dimensions' => $file->getSize()==0?'':$file->getDimensions()
		);
		
		try
		{
			$returnData['isSuccess'] = $file->upload();
			$returnData['data'] = $data;
			
		    return $returnData;	
		}
		catch (\Exception $e)
		{
			$returnData['errorMessage'] = $file->getErrors();	
			return $returnData;
		}
	}

	/**
     * 移动文件
     *
     * @param string $source
     * @param string $destination
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
	public function move($source, $destination, $overWrite = false)
	{
		if (!file_exists($source))
		{
            return false;
        }
		
		if (file_exists($destination) && $overWrite = false)
		{
            return false;
        }
        else if (file_exists($destination) && $overWrite = true)
        {
            $this->unlink($destination);
        }
		
		$dir = dirname($destination);
        $this->createDir($dir);
		
		rename($source, $destination);
		return true;
	}
	
	/**
     * 删除文件
     *
     * @param string $destination
     * @return boolean
     */
	
	public function unlink($destination)
	{
		if (file_exists($destination))
		{
            unlink($destination);
            return true;
        }
        else
        {
            return false;
        }
	}
	
	/**
     * 建立文件夹
     *
     * @param string $aimUrl
     * @return viod
     */
    public function createDir($aimUrl)
    {
        $aimUrl = str_replace('', '/', $aimUrl);
        $aimDir = '';
        $arr = explode('/', $aimUrl);
        $result = true;
        foreach ($arr as $str)
        {
            $aimDir .= $str . '/';
            if (!file_exists($aimDir))
            {
                $result = mkdir($aimDir);
            }
        }
        return $result;
    }
	
}
