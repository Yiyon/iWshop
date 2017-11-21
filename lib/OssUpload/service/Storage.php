<?php
require_once(dirname(dirname(__FILE__))."/oss/services/ossService.php");
class Storage{

	private $storage ;

	public function __construct($access_id,$access_key,$store_method,$domain = ''){
		switch($store_method){
			//case 'EmeSaeStorage':
				//$this->storage = new EmeSaeStorage($domain);break;
			case 'EmeOSS':
				$this->storage = new EmeOSS($access_id,$access_key,$domain);break;
			default:
				$this->storage = null;
		}
	}

	public function upload($srcFileName,$destFileName){
		return $this->storage->upload($srcFileName,$destFileName);
	}

	public function write($content,$file_path){
		return $this->storage->write($content,$file_path);
	}

    public function delete($fileName){
        return $this->storage->delete($fileName);
    }

    public function isObjectExist($fileName){
        return $this->storage->isObjectExist($fileName);
    }


}

//class EmeSaeStorage{

//    private $domain ;
//    private $storage ;

//    public function __construct($domain){
//        $this->domain = $domain;
//        $this->storage = new SaeStorage();
//    }

//    public function upload($srcFileName,$destFileName){
//        $attr = array('encoding'=>'gzip');
//        return $this->storage->upload($this->domain,$destFileName, $srcFileName, $attr, true);
//    }

//    public function write($content,$file_path){
//        return $this->storage->write($this->domain,$file_path,$content);
//    }

//    public function delete($fileName){
//        return $this->storage->delete($this->domain,$fileName);
//    }

//    public function isObjectExist($fileName){
//        return falase;
//    }
//}

class EmeOSS{

	private $domain ;
	private $storage ;

	public function __construct($access_id,$access_key,$domain){
		$this->domain = $domain;
		$this->storage = new ossService($access_id,$access_key,$this->domain);
	}

	public function upload($srcFileName,$destFileName){
		//$domain相当于 $bucket
		$res = $this->storage->upload_by_file($destFileName,$srcFileName);
		if($res->status == 200){
			$url = $res->header['_info']['url'];
			return str_replace('oss-cn-shenzhen.aliyuncs.com/emeoss','oss.emitong.cn',$url);
		}
	}

	public function write($content,$file_path){
		$res = $this->storage->upload_by_content($content,$file_path);
		if($res->status == 200){
			$url = $res->header['_info']['url'];
			return str_replace('oss-cn-shenzhen.aliyuncs.com/emeoss','oss.emitong.cn',$url);
		}
	}

    public function delete($fileName){
        return $this->storage->delete_object($fileName);
    }

    public function isObjectExist($fileName){
        return $this->storage->is_object_exist($fileName);
    }
}


?>
