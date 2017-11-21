<?php
/**
 * 加载sdk包以及错误代码包
 */

//require_once '../sdk.class.php';
require_once(dirname(dirname(__FILE__))."/sdk.class.php");

class ossService{

	public $oss_sdk_service;
	public $bucket;

	public function __construct($access_id,$access_key,$bucket){
				
		$this->oss_sdk_service = new ALIOSS($access_id,$access_key);
		$this->bucket = $bucket;

		//设置是否打开curl调试模式
		$this->oss_sdk_service->set_debug_mode(FALSE);

		//设置开启三级域名，三级域名需要注意，域名不支持一些特殊符号，所以在创建bucket的时候若想使用三级域名，最好不要使用特殊字符
		//$this->oss_sdk_service->set_enable_domain_style(TRUE);

	}

	/**
	 * 函数定义
	 */
	/*%**************************************************************************************************************%*/
	// Service 相关

	//获取bucket列表
	public function get_service(){
		$response = $this->oss_sdk_service->list_bucket();
		return $response;
	}

	/*%**************************************************************************************************************%*/
	// Bucket 相关

	//创建bucket
	public function create_bucket(){
		//$acl = ALIOSS::OSS_ACL_TYPE_PRIVATE;
		$acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ;
		//$acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ_WRITE;
		$response = $this->oss_sdk_service->create_bucket($this->bucket,$acl);
		return $response;
	}

	//删除bucket
	public function delete_bucket(){
		$response = $this->oss_sdk_service->delete_bucket($this->bucket);
		return $response;
	}

	//设置bucket ACL
	public function set_bucket_acl(){
		//$acl = ALIOSS::OSS_ACL_TYPE_PRIVATE;
		//$acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ;
		$acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ_WRITE;
		
		$response = $this->oss_sdk_service->set_bucket_acl($this->bucket,$acl);
		return $response;
	}

	//获取bucket ACL
	public function get_bucket_acl(){
		$options = array(
			ALIOSS::OSS_CONTENT_TYPE => 'text/xml',
		);
			
		$response = $this->oss_sdk_service->get_bucket_acl($this->bucket,$options);
		return $response;	
	}

	//设置bucket logging
	public function  set_bucket_logging($target_bucket,$target_prefix){
		$response = $this->oss_sdk_service->set_bucket_logging($this->bucket,$target_bucket,$target_prefix);
		return $response;	
	}

	//获取bucket logging
	public function  get_bucket_logging(){
		$response = $this->oss_sdk_service->get_bucket_logging($this->bucket);
		return $response;	
	}

	//删除bucket logging
	public function  delete_bucket_logging(){
		$response = $this->oss_sdk_service->delete_bucket_logging($this->bucket);
		return $response;	
	}

	//设置bucket website
	public function  set_bucket_website($index_document,$error_document){
		$response = $this->oss_sdk_service->set_bucket_website($this->bucket,$index_document,$error_document);
		return $response;	
	}

	//获取bucket website
	public function  get_bucket_website(){
		$response = $this->oss_sdk_service->get_bucket_website($this->bucket);
		return $response;	
	}

	//删除bucket website
	public function  delete_bucket_website(){
		$response = $this->oss_sdk_service->delete_bucket_website($this->bucket);
		return $response;	
	}

	/*%**************************************************************************************************************%*/
	//跨域资源共享(CORS)

	//设置bucket cors
	/*public function  set_bucket_cors(){
		$cors_rule[ALIOSS::OSS_CORS_ALLOWED_HEADER]=array("x-oss-test");
		$cors_rule[ALIOSS::OSS_CORS_ALLOWED_METHOD]=array("GET");
		$cors_rule[ALIOSS::OSS_CORS_ALLOWED_ORIGIN]=array("http://www.b.com");
		$cors_rule[ALIOSS::OSS_CORS_EXPOSE_HEADER]=array("x-oss-test1");
		$cors_rule[ALIOSS::OSS_CORS_MAX_AGE_SECONDS] = 10;
		$cors_rules=array($cors_rule);
		
		$response = $this->oss_sdk_service->set_bucket_cors($this->bucket, $cors_rules);
		return $response;	
	}*/

	//获取bucket cors
	public function  get_bucket_cors(){
		$response = $this->oss_sdk_service->get_bucket_cors($this->bucket);
		return $response;	
	}

	//删除bucket cors
	public function  delete_bucket_cors(){
		$response = $this->oss_sdk_service->delete_bucket_cors($this->bucket);
		return $response;	
	}

	//options object
	/*public function  options_object(){
		$object='1.jpg';
		$origin='http://www.b.com';
		$request_method='GET';
		$request_headers='x-oss-test';
		
		$response = $this->oss_sdk_service->options_object($this->bucket, $object, $origin, $request_method, $request_headers);
		return $response;	
	}*/

	/*%**************************************************************************************************************%*/
	// Object 相关

	//获取object列表
	public function list_object($delimiter = '/',$prefix = '',$mkeys = 10){
		$options = array(
			'delimiter' => $delimiter,
			'prefix' => $prefix,
			'max-keys' => $mkeys,
			//'marker' => 'myobject-1330850469.pdf',
		);
		
		$response = $this->oss_sdk_service->list_object($this->bucket,$options);	
		return $response;
	}

	//创建目录
	public function create_directory($dir){
		$response  = $this->oss_sdk_service->create_object_dir($this->bucket,$dir);
		return $response;
	}

	//通过内容上传文件
	function upload_by_content($content,$file_path){	
		$upload_file_options = array(
			'content' => $content,
			'length' => strlen($content)
		);
		
		$response = $this->oss_sdk_service->upload_file_by_content($this->bucket,$file_path,$upload_file_options);
		return $response;
	}


	//通过路径上传文件
	public function upload_by_file($object,$file_path){
		$response = $this->oss_sdk_service->upload_file_by_file($this->bucket,$object,$file_path);
		return $response;
	}

	//拷贝object
	public function copy_object($to_bucket,$from_object,$to_object){
			//copy object
			$options = array(
				'content-type' => 'application/json',
			);

			$response = $this->oss_sdk_service->copy_object($this->bucket,$from_object,$to_bucket,$to_object,$options);
			return $response;
	}

    public function delete_object($object){
		$response = $this->oss_sdk_service->delete_object($this->bucket,$object);
		return $response;
	}

    public function is_object_exist($object){
        $response = $this->oss_sdk_service->is_object_exist($this->bucket,$object);
        return $response;
    }



	//获取object meta
	/*public function get_object_meta($this->oss_sdk_service){
		$this->bucket = 'invalidxml';
		$object = '&#26;&#26;_100.txt'; 

		$response = $this->oss_sdk_service->get_object_meta($this->bucket,$object);
		return $response;
	}*/

	//删除object
	/*public function delete_object(){
		$object = '&#26;&#26;_100.txt';
		$response = $this->oss_sdk_service->delete_object($this->bucket,$object);
		return $response;
	}

	//删除objects
	public function delete_objects($this->oss_sdk_service){
		$this->bucket = 'phpsdk1349849394';
		$this->oss_sdk_serviceects = array('myfoloder-1349850940/','myfoloder-1349850941/',);   
		
		$options = array(
			'quiet' => false,
			//ALIOSS::OSS_CONTENT_TYPE => 'text/xml',
		);
		
		$response = $this->oss_sdk_service->delete_objects($this->bucket,$object,$options);
		return $response;
	}

	//获取object
	public function get_object($this->oss_sdk_service){
		$this->bucket = 'phpsdk1349849394';
		$this->oss_sdk_serviceect = 'netbeans-7.1.2-ml-cpp-linux.sh'; 
		
		$options = array(
			ALIOSS::OSS_FILE_DOWNLOAD => "d:\\cccccccccc.sh",
			//ALIOSS::OSS_CONTENT_TYPE => 'txt/html',
		);	
		
		$response = $this->oss_sdk_service->get_object($this->bucket,$object,$options);
		return $response;
	}*/


	//通过multipart上传文件
	public function upload_by_multi_part($object,$filepath){
			
		$options = array(
			ALIOSS::OSS_FILE_UPLOAD => $filepath,
			'partSize' => 5242880,	//一块的大小
		);

		$response = $this->oss_sdk_service->create_mpu_object($this->bucket, $object,$options);
		return $response;
	}

	//通过multipart上传整个目录
	public function upload_by_dir($dir){
		$recursive = false;
		
		$response = $this->oss_sdk_service->create_mtu_object_by_dir($this->bucket,$dir,$recursive);
		return $response;	
	}

	//通过multi-part上传整个目录(新版)
	public function batch_upload_file($object,$directory){
		$options = array(
			'bucket' 	=> $this->bucket,
			'object'	=> $object,
			'directory' => $directory,
		);
		$response = $this->oss_sdk_service->batch_upload_file($options);
	}



	/*%**************************************************************************************************************%*/
	// 签名url 相关

	//生成签名url,主要用户私有权限下的访问控制
	public function get_sign_url($object,$timeout = 3600){
		$response = $this->oss_sdk_service->get_sign_url($this->bucket,$objeect,$timeout);
		return $response;
	}

	/*%**************************************************************************************************************%*/
	// 结果 相关

	//格式化返回结果
	public function _format($response) {
		echo '|-----------------------Start---------------------------------------------------------------------------------------------------'."\n";
		echo '|-Status:' . $response->status . "\n";
		echo '|-Body:' ."\n"; 
		echo $response->body . "\n";
		echo "|-Header:\n";
		print_r ( $response->header );
		echo '-----------------------End-----------------------------------------------------------------------------------------------------'."\n\n";
	}

}




