<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/JWT.php';
require APPPATH . '/libraries/ExpiredException.php';
require APPPATH . '/libraries/BeforeValidException.php';
require APPPATH . '/libraries/SignatureInvalidException.php';
require APPPATH . '/libraries/JWK.php';
require APPPATH . '/libraries/Key.php';

use chriskacerguis\RestServer\RestController;
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

class Api extends RestController
{

	function __construct()
	{
		// Construct the parent class
		parent::__construct();
		$this->load->model('product_model','product');
		$this->load->model('user_model', 'user');
		$this->load->model('activity_model', 'activity');
	}

	/**
	 * Menyimpan log aktivitas saat api diakses.
	 *
	 * @param array		 $data      Data untuk disimpan
	 */
	private function save_ativity($params)
	{
		$this->activity->insert($params);
	}

	function configToken()
	{
		$cnf['exp'] = 36000;
		$cnf['secretkey'] = '95587568698';
		return $cnf;
	}

	public function login_post()
	{
		$user = $this->user->get_user_by_username($this->post('username'));
		if (!$user) {
			$return = array('status' => '401',
				'message' => 'Username tidak terdaftar');
			$this->response($return, 401);
			die;
		}

		// cek apakah passwordnya benar
		if (!password_verify($this->post('password'), $user->pass)) {
			$return = array('status' => '401',
				'message' => 'Password salah');
			$this->response($return, 401);
			die;
		}

		$exp = time() + 36000;
		$token = array(
			"iss" => 'apprestservice',
			"aud" => 'pengguna',
			"iat" => time(),
			"nbf" => time() + 10,
			"exp" => $exp,
			"data" => array(
				"username" => $this->input->post('username'),
				"password" => $this->input->post('password')
			)
		);

		$jwt = JWT::encode($token, $this->configToken()['secretkey'], 'HS256');
		$data = array('status' => '200',
			'message' => 'success',
			'data' => array('token' => $jwt, 'exp' => $exp));
		$this->response($data, 200);
	}

	public function authtoken()
	{
		$secret_key = $this->configToken()['secretkey'];
		$authHeader = $this->input->request_headers()['Authorization'];
		$arr = explode(" ", $authHeader);
		$token = $arr[1];

		if ($token) {
			try {
				$decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
				if ($decoded) {
					return $decoded;
				}
			} catch (\Exception $e) {
				$result = array('status' => '401', 'message' => $e->getMessage());
				$this->response($result, 401);
			}
		}
	}

	/**
	 * Get data aktivitas.
	 */
	public function activity_get()
	{
		$decodeData = $this->authtoken();
		if (!$decodeData) {
			die();
		}
		$dataArr = json_decode($this->input->raw_input_stream);
		$dataCount = (isset($dataArr->datacount))?$dataArr->datacount:10;
		$dataActivity = $this->activity->get_last_activity($dataCount);
		if ($dataActivity) {
			$return = [
				"status" => 200,
				"success" => true,
				"data" => $dataActivity,
			];
			$this->response($return, 200);
		} else {
			$this->response([
				'status' => 404,
				'success' => false,
				'message' => 'Activity not found'
			], 404);
		}
	}

	/**
	 * Get data produk.
	 */
	public function products_get()
	{
		$decodeData = $this->authtoken();
		if (!$decodeData) {
			die();
		}
		$dataProducts = $this->product->get_last_entries();
		if ($dataProducts) {
			$return = [
				"status" => 200,
				"success" => true,
				"data" => $dataProducts,
			];
			$this->response($return, 200);
		} else {
			$this->response([
				'status' => 404,
				'success' => false,
				'message' => 'Products not found'
			], 404);
		}
	}

	/**
	 * Menyimpan produk baru.
	 *
	 * @param  array		$data      Data untuk disimpan
	 * @return string 		Response status
	 */
	public function products_post()
	{

		$decodeData = $this->authtoken();
		if (!$decodeData) {
			die();
		}

		$checkName = $this->product->check_product_name($this->post('product_name'));
		if($checkName > 0){
			$return = [
				'status' => 404,
				'success' => false,
				'message' => 'Nama Produk sudah ada di database'
			];
			$paramsActivity = [
				"username" => $decodeData->data->username,
				"activity_date" => date("Y-m-d H:i:s"),
				"activity" => 'gagal menambah produk, Nama Produk sudah ada di database',
				"product_id" => "",
				"ip" => $_SERVER["REMOTE_ADDR"]
			];
			$this->save_ativity($paramsActivity);
			$this->response($return, $return['status']);
		}

		$params = [
			"product_name" => $this->post('product_name'),
			"product_description" => $this->post('product_description'),
			"price" => $this->post('price'),
			"stock" => $this->post('stock'),
			"user_input" => $decodeData->data->username,
			"input_date" => date("Y-m-d H:i:s"),
		];

		$insertProducts = $this->product->insert($params);
		//echo $this->db->insert_id();exit;
		if ($insertProducts) {
			$return = [
				"status" => 200,
				"success" => true,
				"pesan" => "Simpan Data Berhasil",
			];
		} else {
			$return = [
				'status' => 404,
				'success' => false,
				'message' => 'Data gagal disimpan'
			];
		}
		$paramsActivity = [
			"username" => $decodeData->data->username,
			"activity_date" => date("Y-m-d H:i:s"),
			"activity" => ($return['status'] == 200)?"Berhasil Menambah data produk":"Gagal menambah data produk",
			"product_id" => ($return['status'] == 200)?$this->db->insert_id():"",
			"ip" => $_SERVER["REMOTE_ADDR"]
		];
		$this->save_ativity($paramsActivity);
		$this->response($return, $return['status']);
	}

	public function products_put()
	{
		$decodeData = $this->authtoken();
		if (!$decodeData) {
			die();
		}
		$id = $this->put('id');
		$checkName = $this->product->check_product_name($this->put('product_name'));
		if($checkName > 0){
			$return = [
				'status' => 404,
				'success' => false,
				'message' => 'Nama Produk sudah ada di database'
			];
			$paramsActivity = [
				"username" => $decodeData->data->username,
				"activity_date" => date("Y-m-d H:i:s"),
				"activity" => 'gagal update produk, Nama Produk sudah ada di database',
				"product_id" => $id,
				"ip" => $_SERVER["REMOTE_ADDR"]
			];
			$this->save_ativity($paramsActivity);
			$this->response($return, $return['status']);
		}


		$params = [
			"product_name" => $this->put('product_name'),
			"product_description" => $this->put('product_description'),
			"price" => $this->put('price'),
			"stock" => $this->put('stock'),
			"user_update" => $decodeData->data->username,
			"update_date" => date("Y-m-d H:i:s"),
		];

		$insertProducts = $this->product->update($params, $id);
		if ($insertProducts) {
			$return = [
				"status" => 200,
				"success" => true,
				"pesan" => "Update Data Berhasil",
			];

		} else {
			$return = [
				'status' => 404,
				'success' => false,
				'message' => 'Update Data gagal'
			];
		}
		$paramsActivity = [
			"username" => $decodeData->data->username,
			"activity_date" => date("Y-m-d H:i:s"),
			"activity" => ($return['status'] == 200)?"Berhasil update data produk":"Gagal update data produk",
			"product_id" => $this->put('id'),
			"ip" => $_SERVER["REMOTE_ADDR"]
		];
		$this->save_ativity($paramsActivity);
		$this->response($return, $return['status']);
	}

	/**
	 * Digunakan untuk menghapus data produk berdasarkan id
	 * @param int $id
	 * @return object
	*/
	public function products_delete()
	{
		$decodeData = $this->authtoken();
		if (!$decodeData) {
			die();
		}

		$id = json_decode($this->input->raw_input_stream)->id;
		$deleteProducts = $this->product->delete($id);

		if ($deleteProducts) {
			$return = [
				"status" => 200,
				"success" => true,
				"pesan" => "Delete Data Berhasil",
			];
		} else {
			$return = [
				'status' => 404,
				'success' => false,
				'message' => 'Delete Data gagal'
			];
		}

		$paramsActivity = [
			"username" => $decodeData->data->username,
			"activity_date" => date("Y-m-d H:i:s"),
			"activity" => ($return['status'] == 200)?"Berhasil menghapus data produk":"Gagal menghapus data produk",
			"product_id" => $id,
			"ip" => $_SERVER["REMOTE_ADDR"]
		];
		$this->save_ativity($paramsActivity);
		$this->response($return, $return['status']);
	}

	public function product_add_stock_put()
	{
		$decodeData = $this->authtoken();
		if (!$decodeData) {
			die();
		}

		$id = $this->put('id');
		$currentProduct = $this->product->get_product_detail($id);

		if(!$currentProduct){
			$paramsActivity = [
				"username" => $decodeData->data->username,
				"activity_date" => date("Y-m-d H:i:s"),
				"activity" =>  "Gagal menambahkan stock,Data tidak ditemukan",
				"product_id" => $id,
				"ip" => $_SERVER["REMOTE_ADDR"]
			];
			$this->save_ativity($paramsActivity);

			$return = [
				'status' => 404,
				'success' => false,
				'message' => 'Gagal menambahkan stock,Data tidak ditemukan'
			];
			$this->response($return, $return['status']);
		}else{
			$currentStock = $currentProduct->stock;
		}

		$addStock = $this->put('add_stock');

		$params = [
			"stock" => ($currentStock + $addStock)
		];

		$insertProducts = $this->product->update($params, $id);
		if ($insertProducts) {
			$return = [
				"status" => 200,
				"success" => true,
				"pesan" => "Berhasil menambahkan stock",
			];
		} else {
			$return = [
				'status' => 404,
				'success' => false,
				'message' => 'Gagal menambahkan stock'
			];
		}

		$paramsActivity = [
			"username" => $decodeData->data->username,
			"activity_date" => date("Y-m-d H:i:s"),
			"activity" => ($return['status'] == 200)?"Berhasil menambah stock produk":"Gagal menambah stock produk",
			"product_id" => $this->put('id'),
			"ip" => $_SERVER["REMOTE_ADDR"]
		];
		$this->save_ativity($paramsActivity);
		$this->response($return, $return['status']);
	}

	public function product_decrease_stock_put()
	{
		$decodeData = $this->authtoken();
		if (!$decodeData) {
			die();
		}

		$id = $this->put('id');
		$currentProduct = $this->product->get_product_detail($id);
		if(date('Y-m-d H:i:s') >= $currentProduct->exp_date){
			$paramsActivity = [
				"username" => $decodeData->data->username,
				"activity_date" => date("Y-m-d H:i:s"),
				"activity" =>  "Gagal mengurangi stock, produk sudah expired",
				"product_id" => $id,
				"ip" => $_SERVER["REMOTE_ADDR"]
			];
			$this->save_ativity($paramsActivity);

			$return = [
				'status' => 404,
				'success' => false,
				'message' => 'Gagal menambahkan stock,produk sudah expired'
			];
			$this->response($return, $return['status']);
		}

		if(!$currentProduct){
			$paramsActivity = [
				"username" => $decodeData->data->username,
				"activity_date" => date("Y-m-d H:i:s"),
				"activity" =>  "Gagal mengurangi stock,Data tidak ditemukan",
				"product_id" => $id,
				"ip" => $_SERVER["REMOTE_ADDR"]
			];
			$this->save_ativity($paramsActivity);

			$return = [
				'status' => 404,
				'success' => false,
				'message' => 'Gagal mengurangi stock,Data tidak ditemukan'
			];
			$this->response($return, $return['status']);
		}else{
			$currentStock = $currentProduct->stock;
		}

		$decreaseStock = $this->put('decrease_stock');
		if ($currentStock < $decreaseStock) {
			$paramsActivity = [
				"username" => $decodeData->data->username,
				"activity_date" => date("Y-m-d H:i:s"),
				"activity" =>  "Gagal mengurangi stock, stok tersedia $currentStock",
				"product_id" => $id,
				"ip" => $_SERVER["REMOTE_ADDR"]
			];
			$this->save_ativity($paramsActivity);

			$this->response([
				'status' => 404,
				'success' => false,
				'message' => 'Gagal mengurangi stock, stok tersedia ' . $currentStock
			], 404);
		}
		$params = [
			"stock" => ($currentStock - $decreaseStock)
		];

		$insertProducts = $this->product->update($params, $id);
		if ($insertProducts) {
			$return = [
				"status" => 200,
				"success" => true,
				"pesan" => "Berhasil mengurangi stock",
			];
		} else {
			$return = [
				'status' => 404,
				'success' => false,
				'message' => 'Gagal mengurangi stock'
			];
		}

		$paramsActivity = [
			"username" => $decodeData->data->username,
			"activity_date" => date("Y-m-d H:i:s"),
			"activity" => ($return['status'] == 200)?"Berhasil mengurangi stock produk":"Gagal mengurangi stock produk",
			"product_id" => $this->put('id'),
			"ip" => $_SERVER["REMOTE_ADDR"]
		];
		$this->save_ativity($paramsActivity);
		$this->response($return, $return['status']);
	}
}
