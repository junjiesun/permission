<?php

namespace Lib\Support;

use ZipArchive;

class ZipFile
{
    /**
     * PHP ZipArchive压缩文件列表
     * @param array $folderPath=array('文件的绝对地址' => '对应文件的别名') 或 $folderPath=array('文件的相对地址' => '对应文件的别名')
     * @param string $zipAs 压缩文件的文件名，可以带路径
     * @param string $description 压缩文件的注释
     * @return 成功时 array 返回类似pathinfo格式的数据，否则 bool 返回false
     */
    static public function zipFiles($files, $zipAs, $description = 'OA'){
        $zipAs = (string)$zipAs;
        if(!$files || !is_array($files) || !$zipAs){
            return false;
        }
        if(!class_exists('ZipArchive')){
            throw new \Exception("not found class ZipArchive", 1);
            return false;
        }

        //check
        if(!($file_arr = self::checkFileExists($files, $zipAs))){
            return false;
        }

        if(!file_exists($file_arr['filepath'])){
            $za = new ZipArchive;
            if(true !== $za->open($file_arr['filepath'], ZipArchive::CREATE)){
                return false;
            }
            $za->setArchiveComment($description . PHP_EOL . date('Y-m-d H:i:s', time()));
            foreach($files as $aPath => $rPath){
                $za->addFile($aPath, $rPath);
            }
            if(!$za->close()){
                return false;
            }
            if($file_arr['stats'] == 2 && file_exists($zipAs)) {
                @unlink($zipAs);
            }
        }

        return $file_arr;
    }


    /**
     * PHP ZipArchive压缩文件夹，实现将目录及子目录中的所有文件压缩为zip文件
     * @param string $folderPath 要压缩的目录路径
     * @param string $zipAs 压缩文件的文件名，可以带路径
     * @param string $description 压缩文件的注释
     * @return 成功时 array 返回类似pathinfo格式的数据，否则 bool 返回false
     */

    static public function zipFolder($folderPath, $zipAs, $description = 'OA'){
        $folderPath = (string)$folderPath;
        $zipAs = (string)$zipAs;
        if(!class_exists('ZipArchive')){
            return false;
        }

        if(!is_dir($folderPath) || !$files = self::scanFolder($folderPath, true, true)){
            return false;
        }

        //check
        if(!($file_arr = self::checkFileExists($files, $zipAs))){
            return false;
        }

        if(!file_exists($file_arr['filepath'])){
            $za = new ZipArchive;
            if(true !== $za->open($file_arr['filepath'], ZipArchive::CREATE)){
                return false;
            }
            $za->setArchiveComment($description . PHP_EOL . date('Y-m-d H:i:s'));
            foreach($files as $aPath => $rPath){
                $za->addFile($aPath, $rPath);
            }
            if(!$za->close()){
                return false;
            }
        }
        if($file_arr['stats'] == 2 && file_exists($zipAs)) {
            @unlink($zipAs);
        }

        return $file_arr;
    }

    /**
     * 扫描文件夹，获取文件列表
     * @param string $path 需要扫描的目录路径
     * @param bool   $recursive 是否扫描子目录
     * @param bool   $noFolder 结果中只包含文件，不包含任何目录，为false时，文件列表中的目录统一在末尾添加/符号
     * @param bool   $returnAbsolutePath 文件列表使用绝对路径，默认将返回相对于指定目录的相对路径
     * @param int    $depth 子目录层级，此参数供系统使用，禁止手动指定或修改
     * @return array|bool 返回目录的文件列表，如果$returnAbsolutePath为true，返回索引数组，否则返回键名为绝对路径，键值为相对路径的关联数组
     */
    static public function scanFolder($path = '', $recursive = true, $noFolder = true, $returnAbsolutePath = false, $depth = 0){
        $path = (string)$path;
        if(!($path = realpath($path))){
            return false;
        }
        $path = str_replace('\\','/',$path);
        if(!($h = opendir($path))){
            return false;
        }
        $files = array();
        static $topPath;
        $topPath = ($depth===0 || empty($topPath)? $path: $topPath);
        while(false !== ($file = readdir($h))){
            if($file !== '..' && $file !== '.'){
                $fp = $path.'/'.$file;
                if(!is_readable($fp)){
                    continue;
                }
                if(is_dir($fp)){
                    $fp .= '/';
                    if(!$noFolder){
                        $files[$fp] = ($returnAbsolutePath? $fp: ltrim(str_replace($topPath,'',$fp),'/'));
                    }
                    if(!$recursive){
                        continue;
                    }
                    $function = __FUNCTION__;
                    $subFolderFiles = $function($fp, $recursive, $noFolder, $returnAbsolutePath, $depth+1);
                    if(is_array($subFolderFiles)){
                        $files = array_merge($files, $subFolderFiles);
                    }
                }else{
                    $files[$fp] = ($returnAbsolutePath? $fp: ltrim(str_replace($topPath, '', $fp),'/'));
                }
            }
        }
        return $returnAbsolutePath? array_values($files): $files;
    }

    /**
     * 验证打包的文件列表sha1值是否与保存的zip文件名一致
     * @param array $file_list 文件列表 $file_list=array('文件的绝对地址' => '当前目录的相对地址')
     * @param string $file_path 文件名（含绝对地址）
     * @return array|bool stats = 1则压缩的文件列表与压缩文件名相同，否则不同
     */
    private static function checkFileExists(Array $file_list, $file_path){
        if($file_list){
            foreach($file_list as $k => $v){
                if(file_exists($k)){
                    $file_list[$k] = $v;
                }else{
                    unset($file_list[$k]);
                }
            }
        }
        if($file_list && $file_path){
            $filesha1 = sha1(self::arrayToJSON($file_list));

            $pathinfo = pathInfo($file_path);
            $parentpath = $pathinfo['dirname'];
            $filename = $pathinfo['filename'];
            if(!is_dir($parentpath)){
                return false;
            }

            //$stats=1相等则原样输出,否则为2
            $stats = 1;
            if(!isset($filename) || $filename !== $filesha1) {
                $stats = 2;
                $file_path = $parentpath.'/'.$filesha1.'.zip';
            }

            return [
                'stats' => $stats,
                'filepath' => $file_path,
                'dirname' => $parentpath,
                'filename' => $filesha1,
                'basename' => $filesha1.'.zip',
                'extension' => 'zip'
            ];
        }

        return false;
    }

    /*数组转json - 解决中文键名问题*/
    private static function arrayToJSON($array) {
        foreach($array as $k => $v){
            $new_key = base64_encode($k);
            $array[$new_key] = base64_encode($v);
            unset($array[$k]);
        }

        return json_encode($array);
    }
} 